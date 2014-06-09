<?php

abstract class Waindigo_Listener_TemplatePostRender extends Waindigo_Listener_Template
{

    /**
     * To remove the copyright notice, please download the copyright notice
     * removal add-on from (or follow the instructions at):
     * http://xenforo.com/community/resources/copyright-notice-by-waindigo.892/
     *
     * Removing copyright notices without permission is strictly prohibited.
     * This will be considered as theft of software and legal action will be
     * taken against anyone who attempts to steal our software.
     */
    protected $_templateName = null;

    protected $_containerData = null;

    public function __construct($templateName, &$content, array &$containerData, XenForo_Template_Abstract $template)
    {
        $this->_templateName = $templateName;
        $this->_containerData = $containerData;
        parent::__construct($content, $template);
    } /* END __construct */

    // This only works on PHP 5.3+, so method should be overridden for now
    public static function templatePostRender($templateName, &$content, array &$containerData,
        XenForo_Template_Abstract $template)
    {
        $class = get_called_class();
        $templatePostRender = new $class($templateName, $content, $containerData, $template);
        list ($content, $containerData) = $templatePostRender->run();
    } /* END templatePostRender */

    /**
     *
     * @see Waindigo_Listener_Template::run()
     */
    public function run()
    {
        switch ($this->_templateName) {
            case 'PAGE_CONTAINER':
                if (!self::$_copyrightNotice) {
                    $this->_copyrightNotice();
                }
                break;
        }

        $templates = $this->_getTemplates();
        foreach ($templates as $templateName) {
            if ($templateName == $this->_templateName) {
                $callback = $this->_getTemplateCallbackFromTemplateName($templateName);
                $this->_runTemplateCallback($callback);
            }
        }

        $templateCallbacks = $this->_getTemplateCallbacks();
        foreach ($templateCallbacks as $templateName => $callback) {
            if ($templateName == $this->_templateName) {
                $this->_runTemplateCallback($callback);
            }
        }

        return array(
            $this->_contents,
            $this->_containerData
        );
    } /* END run */

    /**
     *
     * @param string $templateName
     * @return $callback
     */
    protected function _getTemplateCallbackFromTemplateName($templateName)
    {
        return array(
            '$this',
            '_' . lcfirst(str_replace(" ", "", ucwords(str_replace("_", " ", $templateName))))
        );
    } /* END _getTemplateCallbackFromTemplateName */

    /**
     *
     * @param callback Callback to run. Use an array with a string '$this' to
     * callback to this object.
     *
     * @return boolean
     */
    protected function _runTemplateCallback($callback)
    {
        if (is_array($callback) && isset($callback[0]) && $callback[0] == '$this') {
            $callback[0] = $this;
        }

        return (boolean) call_user_func_array($callback,
            array(
                $this->_templateName,
                $this
            ));
    } /* END _runTemplateCallback */

    /**
     *
     * @return array
     */
    protected function _getTemplateCallbacks()
    {
        return array();
    } /* END _getTemplateCallbacks */

    /**
     *
     * @return array
     */
    protected function _getTemplates()
    {
        return array();
    } /* END _getTemplates */

    protected function _appendTemplateToContainerData($templateName)
    {
        $template = $this->_render($templateName);

        if (!isset($this->_containerData['topctrl'])) {
            $this->_containerData['topctrl'] = $template;
        } else {
            $this->_containerData['topctrl'] .= $template;
        }
    } /* END _appendTemplateToContainerData */

    protected function _copyrightNotice()
    {
        if (!isset(XenForo_Application::getInstance()->adminStyleModifiedDate)) {
            $newContents = preg_replace('#(<div class="breadBoxBottom">\s*<nav>.*</nav>\s*</div>)#Us',
                '$1' . self::$copyrightNotice, $this->_contents);
            if ($newContents != $this->_contents) {
                $this->_contents = $newContents;
            } else {
                $this->_contents = preg_replace('#(<body.*>.*)</body>#Us', '$1' . self::$copyrightNotice . '</body>',
                    $this->_contents);
            }
        }
        self::$_copyrightNotice = true;
    } /* END _copyrightNotice */
}

if (function_exists('lcfirst') === false) {

    /**
     * Make a string's first character lowercase
     *
     * @param string $str
     * @return string the resulting string.
     */
    function lcfirst($str)
    {
        $str[0] = strtolower($str[0]);
        return (string) $str;
    }
}