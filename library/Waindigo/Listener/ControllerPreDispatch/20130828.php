<?php

class Waindigo_Listener_ControllerPreDispatch
{

    protected static $_checkForUpdates = null;

    protected static $_upgradeAddOns = array();

    protected static $_installAddOns = array(); /* Deprecated. */

    protected static $_checkForCopyright = null;

    protected static $_showCopyright = false;

    /**
     * Standard approach to caching other model objects for the lifetime of the
     * model.
     *
     * @var array
     */
    protected $_modelCache = array();

    /**
     *
     * @var XenForo_Controller
     */
    protected static $_controller = null;

    protected static $_action = '';

    protected static $_controllerName = '';

    const LAST_XML_UPLOAD_DATE_SIMPLE_CACHE_KEY = 'waindigo_lastXmlUploadDate';

    /**
     *
     * @param XenForo_Dependencies_Abstract $dependencies
     * @param array $data
     * @param string $controllerName since XenForo 1.2
     */
    public function __construct(XenForo_Controller $controller, $action, $controllerName = '')
    {
        if (is_null(self::$_controller)) {
            self::$_controller = $controller;
        }
        if (!self::$_action) {
            self::$_action = $action;
        }
        if (!self::$_controllerName) {
            if ($controllerName) {
                self::$_controllerName = $controllerName;
            } else {
                self::$_controllerName = get_class(self::$_controller);
            }
        }
    } /* END __construct */

    /**
     * Called before attempting to dispatch the request in a specific
     * controller.
     * The visitor object is available at this point.
     *
     * @param XenForo_Controller $controller - the controller instance. From
     * this, you can inspect the
     * request, response, etc.
     * @param string $action - the specific action that will be executed in this
     * controller.
     */
    public static function controllerPreDispatch(XenForo_Controller $controller, $action)
    {
        $arguments = func_get_args();
        if (isset($arguments[2])) {
            $controllerName = func_get_arg(2);
        } else {
            $controllerName = '';
        }

        if (function_exists('get_called_class')) {
            $className = get_called_class();
        } else {
            $className = get_class();
        }
        $controllerPreDispatch = new $className($controller, $action, $controllerName);
        $extend = $controllerPreDispatch->run();
    } /* END controllerPreDispatch */

    public function run()
    {
        $this->_runOnceInFirstListener();

        $this->_runOnceInLastListener();
    } /* END run */

    protected function _runOnceInFirstListener()
    {
        if (!$this->_isFirstListenerToBeCalled()) {
            return;
        }

        if (!isset(self::$_checkForUpdates)) {
            self::$_checkForUpdates = true;
            $lastXMLUploadDate = $this->_getLastXmlUploadDate();

            $addOns = array();

            try {
                $installDataDir = XenForo_Application::getInstance()->getRootDir() . '/install/data/';
                if (is_dir($installDataDir)) {
                    /* @var $dir Directory */
                    $dir = dir($installDataDir);

                    while ($entry = $dir->read()) {
                        if (strlen($entry) > strlen('addon-.xml') && substr($entry, 0, strlen('addon-')) == 'addon-') {
                            if (filemtime(XenForo_Application::getInstance()->getRootDir() . '/install/data/' . $entry) >
                                 $lastXMLUploadDate) {
                                $addOns[] = substr($entry, strlen('addon-'), strlen($entry) - strlen('addon-.xml'));
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                // do nothing
            }

            if ($this->_checkAddOnsNeedUpgrading($addOns)) {
                $upgradeAddOns = $this->getUpgradeAddOns();
                if (!empty($upgradeAddOns)) {
                    XenForo_Application::get('options')->set('boardActive', false);
                    if (!XenForo_Visitor::getInstance()->get('is_admin')) {
                        try {
                            self::$_controller->preDispatch(self::$_action, self::$_controllerName);
                        } catch (XenForo_ControllerResponse_Exception $exception) {
                            $controllerResponse = $exception->getControllerResponse();
                            if (get_class($controllerResponse) == 'XenForo_ControllerResponse_Message') {
                                $controllerResponse->message = new XenForo_Phrase('board_currently_being_upgraded');
                            }
                            throw $exception;
                        }
                    }
                }
            }
            if (is_subclass_of(self::$_controller, 'XenForo_ControllerAdmin_Abstract')) {
                $controllerName = self::$_controller->getRequest()->getParam('_controllerName');
                $action = self::$_controller->getRequest()->getParam('_action');
                if ($controllerName == 'XenForo_ControllerAdmin_AddOn' && $action == 'UpgradeAllFromXml') {
                    $this->_upgradeAddOns();
                }
            }
        }
    } /* END _runOnceInFirstListener */

    protected function _isFirstListenerToBeCalled()
    {
        $cpdListeners = XenForo_CodeEvent::getEventListeners('controller_pre_dispatch');

        if (XenForo_Application::$versionId >= 1020000) {
            $cpdListeners = $cpdListeners['_'];
        }

        $firstListener = reset($cpdListeners);

        if ($firstListener[0] == get_class($this)) {
            return true;
        }

        return false;
    } /* END _isFirstListenerToBeCalled */

    protected function _runOnceInLastListener()
    {
        if (!$this->_isLastListenerToBeCalled()) {
            return;
        }

        if (!isset(self::$_checkForCopyright)) {
            self::$_checkForCopyright = true;
            $this->_removeUnwantedCopyrightNotice();
        }
    } /* END _runOnceInLastListener */

    protected function _isLastListenerToBeCalled()
    {
        $cpdListeners = XenForo_CodeEvent::getEventListeners('controller_pre_dispatch');

        if (XenForo_Application::$versionId >= 1020000) {
            $cpdListeners = $cpdListeners['_'];
        }

        $lastListener = end($cpdListeners);

        if ($lastListener[0] == get_class($this)) {
            return true;
        }

        return false;
    } /* END _isLastListenerToBeCalled */

    protected function _run()
    {
        try {
            return $this->run();
        } catch (Exception $e) {
            // do nothing
        }
    } /* END _run */

    /**
     *
     * @param string $addOnId
     * @param string|null $codeEvent
     */
    public static function isAddOnEnabled($addOnId, $codeEvent = '')
    {
        if (XenForo_Application::$versionId >= 1020000) {
            return array_key_exists($addOnId, XenForo_Application::get('addOns'));
        }

        if (!$codeEvent) {
            $codeEvents = array(
                'container_admin_params',
                'container_public_params',
                'controller_post_dispatch',
                'controller_pre_dispatch',
                'criteria_page',
                'criteria_user',
                'file_health_check',
                'front_controller_post_view',
                'front_controller_pre_dispatch',
                'front_controller_pre_route',
                'front_controller_pre_view',
                'init_dependencies',
                'init_router_public',
                'load_class',
                'load_class_bb_code',
                'load_class_controller',
                'load_class_datawriter',
                'load_class_importer',
                'load_class_mail',
                'load_class_model',
                'load_class_route_prefix',
                'load_class_search_data',
                'load_class_view',
                'navigation_tabs',
                'option_captcha_render',
                'search_source_create',
                'template_create',
                'template_file_change',
                'template_hook',
                'template_post_render',
                'visitor_setup'
            );
        } else {
            $codeEvents = array(
                $codeEvent
            );
        }

        foreach ($codeEvents as $codeEvent) {
            $allListeners = XenForo_CodeEvent::getEventListeners($codeEvent);
            if (!empty($allListeners)) {
                foreach ($allListeners as $listeners) {
                    if (XenForo_Application::$versionId < 1020000) {
                        $listeners = array(
                            '_' => $listeners
                        );
                    }
                    foreach ($listeners as $callback) {
                        if (strlen($callback[0]) > strlen($addOnId) &&
                             substr($callback[0], 0, strlen($addOnId)) == $addOnId) {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    } /* END isAddOnEnabled */

    /**
     * Gets the specified model object from the cache.
     * If it does not exist,
     * it will be instantiated.
     *
     * @param string $class Name of the class to load
     *
     * @return XenForo_Model
     */
    public function getModelFromCache($class)
    {
        if (!isset($this->_modelCache[$class])) {
            $this->_modelCache[$class] = XenForo_Model::create($class);
        }

        return $this->_modelCache[$class];
    } /* END getModelFromCache */

    /**
     *
     * @param array $addOns
     */
    protected function _checkAddOnsNeedUpgrading(array $addOns)
    {
        if (empty($addOns)) {
            return false;
        }

        /* @var $addOnModel XenForo_Model_AddOn */
        $addOnModel = XenForo_Model::create('XenForo_Model_AddOn');

        $allAddOns = $addOnModel->getAllAddOns();

        $lastXmlUploadDate = $this->_getLastXmlUploadDate();
        foreach ($addOns as $addOnId) {
            if (isset($allAddOns[$addOnId])) {
                $addOn = $allAddOns[$addOnId];

                try {
                    $addOnXML = new SimpleXMLElement(
                        file_get_contents(
                            XenForo_Application::getInstance()->getRootDir() . '/install/data/addon-' . $addOnId . '.xml'));
                    $versionId = (string) $addOnXML->attributes()->version_id;
                } catch (Exception $e) {
                    $versionId = '';
                }
                if ($versionId > $addOn['version_id']) {
                    self::$_upgradeAddOns[$addOnId] = $addOn;
                } else {
                    $xmlUploadDate = filemtime(
                        XenForo_Application::getInstance()->getRootDir() . '/install/data/addon-' . $addOnId . '.xml');
                    if ($xmlUploadDate > $lastXmlUploadDate) {
                        $lastXmlUploadDate = $xmlUploadDate;
                    }
                }
            }
        }

        if (is_subclass_of(self::$_controller, 'XenForo_ControllerAdmin_Abstract')) {
            eval(
                '
                class Waindigo_Listener_ControllerPreDispatch_TemplatePostRender
                {
                	public static function templatePostRender($templateName, &$content, array &$containerData, XenForo_Template_Abstract $template)
                	{
                		if ($templateName == "PAGE_CONTAINER") {
                			$upgradeAddOns = Waindigo_Listener_ControllerPreDispatch::getUpgradeAddOns();
                			if (!empty($upgradeAddOns)) {
                			    $params = $template->getParams();
                			    if (!$params[\'showUpgradePendingNotice\']) {
                    			    $pattern = \'#<noscript><p class="importantMessage">.*</p></noscript>#U\';
                                    $replacement = \'<p class="importantMessage">\' . new XenForo_Phrase(\'forum_is_currently_closed_only_admins_may_access\') . \'<br /><a href="\'.XenForo_Link::buildAdminLink(\'add-ons/upgrade-all-from-xml\').\'">\' . new XenForo_Phrase(\'upgrade_add_on\') . \'</a></p>\';
                    			    $content = preg_replace($pattern, \'${1}\' . $replacement, $content);
                			    }
                			}
                		}
                	}
                }');
            $tprListeners = XenForo_CodeEvent::getEventListeners('template_post_render');
            if (!$tprListeners || XenForo_Application::$versionId >= 1020052) {
                $tprListeners = array();
            }
            $newListener = array(
                'Waindigo_Listener_ControllerPreDispatch_TemplatePostRender',
                'templatePostRender'
            );
            if (XenForo_Application::$versionId < 1020000) {
                $tprListeners[] = $newListener;
            } else {
                $tprListeners['_'][] = $newListener;
            }
            XenForo_CodeEvent::setListeners(
                array(
                    'template_post_render' => $tprListeners
                ));
        }

        if (empty(self::$_upgradeAddOns)) {
            $this->_setLastXmlUploadDate($lastXmlUploadDate);
            return false;
        }

        return true;
    } /* END _checkAddOnsNeedUpgrading */

    public static function getUpgradeAddOns()
    {
        return self::$_upgradeAddOns;
    } /* END getUpgradeAddOns */

    /**
     *
     * @deprecated Deprecated.
     */
    public static function getInstallAddOns()
    {
        return array();
    } /* END getInstallAddOns */

    /**
     *
     * @deprecated Deprecated.
     * @param string $action
     */
    protected function _upgradeOrInstallAddOns($action)
    {
        return $this->_upgradeAddOns();
    } /* END _upgradeOrInstallAddOns */

    /**
     *
     * @param string $action
     */
    protected function _upgradeAddOns()
    {
        $template = new XenForo_Template_Admin('PAGE_CONTAINER_SIMPLE',
            array(
                'jQuerySource' => XenForo_Dependencies_Abstract::getJquerySource(),
                'xenOptions' => XenForo_Application::get('options')->getOptions(),
                '_styleModifiedDate' => XenForo_Application::get('adminStyleModifiedDate')
            ));
        $template->setLanguageId(1);

        $template->setParam('title', 'Upgrading Add-ons...');
        $addOns = array_keys(self::getUpgradeAddOns());

        $addOnModel = XenForo_Model::create('XenForo_Model_AddOn');
        $nextAddOnId = '';
        if (count($addOns)) {
            $next = self::$_controller->getInput()->filterSingle('next', XenForo_Input::STRING);
            if ($next) {
                $addOn = $next;
            } else {
                $addOn = reset($addOns);
            }
            for ($i = 0; $i < count($addOns); $i++) {
                if ($addOns[$i] != $addOn) {
                    unset($addOns[$i]);
                    continue;
                }
                break;
            }
            $fileName = XenForo_Application::getInstance()->getRootDir() . '/install/data/addon-' . $addOn . '.xml';
            try {
                $caches = $addOnModel->installAddOnXmlFromFile($fileName, $addOn);
                $template->setParam('contents',
                    '<form action="' . XenForo_Link::buildAdminLink('add-ons/upgrade-all-from-xml') . '" class="xenForm formOverlay CacheRebuild" method="post">
					<p id="ProgressText">Upgrading... <span class="RebuildMessage"></span> <span class="DetailedMessage"></span></p>
					<p id="ErrorText" style="display: none">' .
                         new XenForo_Phrase('error_occurred_or_request_stopped') . '</p>
					<input type="submit" class="button" value="Continue Upgrading" />
					<input type="hidden" name="_xfToken" value="' .
                         XenForo_Visitor::getInstance()->get('csrf_token_page') . '" />
					</form>');
            } catch (Exception $e) {
                if (count($addOns) == 1) {
                    $template->setParam('contents',
                        'Upgrade error (' . $addOn . '). Please use the <a href="' . XenForo_Link::buildAdminLink(
                            'add-ons/upgrade',
                            array(
                                'addon_id' => $addOn
                            )) . '">standard upgrade tool</a> and report any error messages to the developer.');
                } else {
                    unset($addOns[array_search($addOn, $addOns)]);
                    $nextAddOnId = reset($addOns);
                    $template->setParam('contents',
                        '<form action="' . XenForo_Link::buildAdminLink('add-ons/upgrade-all-from-xml') . '" class="xenForm formOverlay CacheRebuild" method="post">
						<p id="ProgressText">Upgrading... <span class="RebuildMessage"></span> <span class="DetailedMessage"></span></p>
						<p id="ErrorText" style="display: none">' .
                             new XenForo_Phrase('error_occurred_or_request_stopped') . '</p>
						<input type="submit" class="button" value="Continue Upgrading" />
						<input type="hidden" name="next" value="' . $nextAddOnId . '" />
						<input type="hidden" name="_xfToken" value="' .
                             XenForo_Visitor::getInstance()->get('csrf_token_page') . '" />
						</form>');
                }
            }
        } else {
            $caches = $addOnModel->rebuildAddOnCaches();
        }

        if (!count($addOns) && (isset($caches) || XenForo_Application::$versionId > 1020000)) {
            if (self::$_controller->getRouteMatch()->getResponseType() == 'json') {
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode(
                    array(
                        '_redirectTarget' => XenForo_Link::buildAdminLink('index')
                    ));
            } else {
                header('Location: ' . XenForo_Link::buildAdminLink('index'));
            }
        } elseif (count($addOns) == 1 && (isset($caches) || XenForo_Application::$versionId > 1020000)) {
            if (XenForo_Application::$versionId > 1020000) {
                $url = XenForo_Link::buildAdminLink('tools/run-deferred');
            } else {
                $url = XenForo_Link::buildAdminLink('tools/cache-rebuild', null,
                    array(
                        'caches' => json_encode($caches)
                    ));
            }
            if (self::$_controller->getRouteMatch()->getResponseType() == 'json') {
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode(array(
                    '_redirectTarget' => $url
                ));
            } else {
                header('Location: ' . $url);
            }
        } else {
            if (self::$_controller->getRouteMatch()->getResponseType() == 'json') {
                echo json_encode(
                    array(
                        '_redirectTarget' => XenForo_Link::buildAdminLink('add-ons/upgrade-all-from-xml', array(),
                            array(
                                'next' => $nextAddOnId
                            ))
                    ));
            } else {
                $output = $template->render();
                $output = str_replace("<!--XenForo_Require:JS-->",
                    '<script src="js/xenforo/cache_rebuild.js"></script>', $output);
                echo $output;
            }
        }
        exit();
    } /* END _upgradeAddOns */

    /**
     * This is to ensure copyright notices don't display on every page if all
     * add-ons are newer than 15 September 2012 (unless explicitly set).
     *
     * This code is redundant if all add-ons are newer than 25 June 2013.
     */
    protected function _removeUnwantedCopyrightNotice()
    {
        $thListeners = XenForo_CodeEvent::getEventListeners('template_hook');
        $tprListeners = XenForo_CodeEvent::getEventListeners('template_post_render');
        if (!empty($thListeners) || !empty($tprListeners)) {
            $this->_getLibraryListenerFileVersion('Template');
            if (!empty($thListeners)) {
                $templateHookVersion = $this->_getLibraryListenerFileVersion('TemplateHook');
            }
            if (!empty($tprListeners)) {
                $templatePostRenderVersion = $this->_getLibraryListenerFileVersion('TemplatePostRender');
            }
        }

        $tcListeners = XenForo_CodeEvent::getEventListeners('template_create');
        if (!empty($tcListeners)) {
            $this->_getLibraryListenerFileVersion('TemplateCreate');
        }

        $lccListeners = XenForo_CodeEvent::getEventListeners('load_class_controller');
        if (!empty($lccListeners)) {
            $loadClassVersion = $this->_getLibraryListenerFileVersion('LoadClass', false);
        }

        if (defined(Waindigo_Listener_InitDependencies::COPYRIGHT_MODIFICATION_SIMPLE_CACHE_KEY) &&
             !XenForo_Application::getSimpleCacheData(self::COPYRIGHT_MODIFICATION_SIMPLE_CACHE_KEY)) {
            return;
        }

        if (XenForo_Application::$versionId < 1020000 && self::$_showCopyright) {
            return;
        }

        $className = get_class(self::$_controller);
        if (XenForo_Application::$versionId < 1020000 && strpos($className, 'Waindigo') === 0) {
            return;
        }

        if (XenForo_Application::getOptions()->waindigo_loadClassHints) {
            $controllers = XenForo_Application::getOptions()->waindigo_loadClassHints;
        } else {
            $controllers = array(
                'XenForo_ControllerPublic_Misc' => array()
            );
        }

        $addOns = array();
        if (XenForo_Application::$versionId < 1020000 || !array_key_exists($className, $controllers)) {
            if (XenForo_Application::$versionId >= 1020000) {
                if (!empty($thListeners['_'])) {
                    $thListeners = $thListeners['_'];
                } else {
                    $thListeners = array();
                }
            }
            if (!empty($thListeners)) {
                foreach ($thListeners as $templateHook) {
                    if (strlen($templateHook[0]) > strlen('Waindigo_') &&
                         substr($templateHook[0], 0, strlen('Waindigo_')) == 'Waindigo_') {
                        if ($templateHookVersion < '20120715') {
                            return;
                        }
                        $addOns[] = substr($templateHook[0], 0,
                            strlen($templateHook[0]) - strlen('_Listener_TemplateHook'));
                    }
                }
            }

            if (XenForo_Application::$versionId >= 1020000) {
                if (!empty($tprListeners['_'])) {
                    $tprListeners = $tprListeners['_'];
                } else {
                    $tprListeners = array();
                }
            }
            if (!empty($tprListeners)) {
                foreach ($tprListeners as $templatePostRender) {
                    if (strlen($templatePostRender[0]) > strlen('Waindigo_') &&
                         substr($templatePostRender[0], 0, strlen('Waindigo_')) == 'Waindigo_') {
                        $addOnId = substr($templatePostRender[0], 0,
                            strlen($templatePostRender[0]) - strlen('_Listener_TemplatePostRender'));
                        if (in_array($addOnId, $addOns)) {
                            continue;
                        }
                        if ($templatePostRenderVersion < '20120715') {
                            return;
                        }
                        $addOns[] = $addOnId;
                    }
                }
            }

            if (XenForo_Application::$versionId >= 1020000) {
                if (!empty($lccListeners['_'])) {
                    $lccListeners = $lccListeners['_'];
                } else {
                    $lccListeners = array();
                }
            }
            if (!empty($lccListeners)) {
                foreach ($lccListeners as $loadClassController) {
                    if (strlen($loadClassController[0]) > strlen('Waindigo_') &&
                         substr($loadClassController[0], 0, strlen('Waindigo_')) == 'Waindigo_') {
                        if (strpos($loadClassController[0], '_Listener_LoadClassController') > 0) {
                            $addOnId = substr($loadClassController[0], 0,
                                strlen($loadClassController[0]) - strlen('_Listener_LoadClassController'));
                        } else {
                            $addOnId = substr($loadClassController[0], 0,
                                strlen($loadClassController[0]) - strlen('_Listener_LoadClass'));
                        }
                        if ($loadClassVersion >= '20121018') {
                            $loadClassController = Waindigo_Listener_LoadClass::create($loadClassController[0]);
                            if (method_exists($loadClassController, 'getExtendedClass')) {
                                $extendedClasses = $loadClassController->getExtendedClasses();
                                if (in_array(get_class(self::$_controller), $extendedClasses)) {
                                    return;
                                }
                            }
                        }
                        if (!in_array($addOnId, $addOns)) {
                            $addOns[] = $addOnId;
                        }
                    }
                }
            }

            if (!empty($addOns) && XenForo_Application::$versionId >= 1020000) {
                $enabledAddOns = XenForo_Application::get('addOns');
                foreach ($enabledAddOns as $addOnId => $addOn) {
                    if ($addOn > 1372118400) {
                        $addOnKey = array_search($addOnId, $addOns);
                        if ($addOnKey !== FALSE) {
                            unset($addOns[$addOnKey]);
                        }
                    }
                }
            }

            if (!empty($addOns)) {
                $cpdListeners = XenForo_CodeEvent::getEventListeners('controller_pre_dispatch');
                if (XenForo_Application::$versionId >= 1020000) {
                    $cpdListeners = $cpdListeners['_'];
                }
                foreach ($cpdListeners as $controllerPreDispatch) {
                    if (strlen($controllerPreDispatch[0]) > strlen('Waindigo_') &&
                         substr($controllerPreDispatch[0], 0, strlen('Waindigo_')) == 'Waindigo_') {
                        if ($controllerPreDispatch[0] != 'Waindigo_Listener_ControllerPreDispatch') {

                            $addOnId = substr($controllerPreDispatch[0], 0,
                                strlen($controllerPreDispatch[0]) - strlen('_Listener_ControllerPreDispatch'));
                            $addOnKey = array_search($addOnId, $addOns);
                            if ($addOnKey !== FALSE) {
                                unset($addOns[$addOnKey]);
                            }
                        }
                    }
                }
            }
        }

        if (self::$_showCopyright && !array_key_exists($className, $controllers) && strpos($className, 'Waindigo') !== 0) {
            return;
        }

        if (empty($addOns) && (!empty($thListeners) || !empty($tprListeners))) {
            if (class_exists('Waindigo_Listener_Template')) {
                if (method_exists('Waindigo_Listener_Template', 'setCopyrightNotice')) {
                    Waindigo_Listener_Template::setCopyrightNotice(true);
                } else {
                    $addOnId = reset($addOns);
                    eval(
                        '
                        class ' . $addOnId . '_Listener_Template extends Waindigo_Listener_Template
                        {
                        	public static function setCopyrightNotice($copyrightNotice)
                        	{
                        		self::$_copyrightNotice = $copyrightNotice;
                        	}
                        }');
                    call_user_func(
                        array(
                            $addOnId . '_Listener_Template',
                            'setCopyrightNotice'
                        ), true);
                }
            }
        }
    } /* END _removeUnwantedCopyrightNotice */

    protected function _getLibraryListenerFileVersion($filename, $autoload = true)
    {
        $rootDir = XenForo_Autoloader::getInstance()->getRootDir();

        $version = 0;
        $handle = @opendir($rootDir . '/Waindigo/Listener/' . $filename);
        if ($handle) {
            while (false !== ($entry = readdir($handle))) {
                if (intval($entry) > $version) {
                    $version = intval($entry);
                }
            }
            if ($autoload) {
                require_once $rootDir . '/Waindigo/Listener/' . $filename . '/' . $version . '.php';
            }
        }

        return $version;
    } /* END _getLibraryListenerFileVersion */

    protected function _getLastXmlUploadDate()
    {
        return XenForo_Application::getSimpleCacheData(self::LAST_XML_UPLOAD_DATE_SIMPLE_CACHE_KEY);
    } /* END _getLastXmlUploadDate */

    /**
     *
     * @param integer $lastXmlUploadDate
     */
    protected function _setLastXmlUploadDate($lastXmlUploadDate)
    {
        XenForo_Application::setSimpleCacheData(self::LAST_XML_UPLOAD_DATE_SIMPLE_CACHE_KEY, $lastXmlUploadDate);
    } /* END _setLastXmlUploadDate */
}