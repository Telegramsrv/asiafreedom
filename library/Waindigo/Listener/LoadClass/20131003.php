<?php

class Waindigo_Listener_LoadClass
{

    protected $_class = null;

    protected $_extend = null;

    protected $_type = '';

    protected static $_runOnce = array();

    /**
     *
     * @param string $class
     * @param array $extend
     */
    public function __construct(&$class = '', array &$extend = array(), $type = '')
    {
        $this->_class = $class;
        $this->_extend = $extend;
        $this->_type = $type;
    } /* END __construct */

    /**
     * Called when instantiating any class.
     * This event can be used to extend
     * the class that will be instantiated dynamically.
     *
     * @param string $class - the name of the class to be created
     * @param array &$extend - a modifiable list of classes that wish to extend
     * the class.
     */
    public static function loadClass($class, array &$extend, $type = '')
    {
        if (function_exists('get_called_class')) {
            $className = get_called_class();
        } else {
            $className = get_class();
        }
        $loadClass = new $className($class, $extend, $type);
        $extend = $loadClass->run();
    } /* END loadClass */

    /**
     * Called when instantiating a BB code formatter.
     * This event can be used to
     * extend the class that will be instantiated dynamically.
     *
     * @param string $class - the name of the class to be created
     * @param array &$extend - a modifiable list of classes that wish to extend
     * the class.
     */
    public static function loadClassBBCode($class, array &$extend)
    {
        self::loadClass($class, $extend, 'bb_code');
    } /* END loadClassBBCode */

    /**
     * Called when instantiating a controller.
     * This event can be used to extend
     * the class that will be instantiated dynamically.
     *
     * @param string $class - the name of the class to be created
     * @param array &$extend - a modifiable list of classes that wish to extend
     * the class.
     */
    public static function loadClassController($class, array &$extend)
    {
        self::loadClass($class, $extend, 'controller');
    } /* END loadClassController */

    /**
     * Called when instantiating a data writer.
     * This event can be used to
     * extend the class that will be instantiated dynamically.
     *
     * @param string $class - the name of the class to be created
     * @param array &$extend - a modifiable list of classes that wish to extend
     * the class.
     */
    public static function loadClassDataWriter($class, array &$extend)
    {
        self::loadClass($class, $extend, 'datawriter');
    } /* END loadClassDataWriter */

    /**
     * Called when instantiating an image processor.
     * This event can be used to
     * extend the class that will be instantiated dynamically.
     *
     * @param string $class - the name of the class to be created
     * @param array &$extend - a modifiable list of classes that wish to extend
     * the class.
     */
    public static function loadClassImage($class, array &$extend)
    {
        self::loadClass($class, $extend, 'image');
    } /* END loadClassImage */

    /**
     * Called when instantiating an importer.
     * This event can be used to extend
     * the class that will be instantiated dynamically.
     *
     * @param string $class - the name of the class to be created
     * @param array &$extend - a modifiable list of classes that wish to extend
     * the class.
     */
    public static function loadClassImporter($class, array &$extend)
    {
        self::loadClass($class, $extend, 'importer');
    } /* END loadClassImporter */

    /**
     * Called when instantiating an installer.
     * This event can be used to extend
     * the class that will be instantiated dynamically.
     *
     * @param string $class - the name of the class to be created
     * @param array &$extend - a modifiable list of classes that wish to extend
     * the class.
     */
    public static function loadClassInstallerWaindigo($class, array &$extend)
    {
        self::loadClass($class, $extend, 'installer_waindigo');
    } /* END loadClassInstallerWaindigo */

    /**
     * Called when instantiating a mail object.
     * This event can be used to
     * extend the class that will be instantiated dynamically.
     *
     * @param string $class - the name of the class to be created
     * @param array &$extend - a modifiable list of classes that wish to extend
     * the class.
     */
    public static function loadClassMail($class, array &$extend)
    {
        self::loadClass($class, $extend, 'mail');
    } /* END loadClassMail */

    /**
     * Called when instantiating a model.
     * This event can be used to extend the
     * class that will be instantiated dynamically.
     *
     * @param string $class - the name of the class to be created
     * @param array &$extend - a modifiable list of classes that wish to extend
     * the class.
     */
    public static function loadClassModel($class, array &$extend)
    {
        self::loadClass($class, $extend, 'model');
    } /* END loadClassModel */

    /**
     * Called when instantiating a PDF.
     * This event can be used to extend the
     * class that will be instantiated dynamically.
     *
     * @param string $class - the name of the class to be created
     * @param array &$extend - a modifiable list of classes that wish to extend
     * the class.
     */
    public static function loadClassPdfWaindigo($class, array &$extend)
    {
        self::loadClass($class, $extend, 'pdf_waindigo');
    } /* END loadClassPdfWaindigo */

    /**
     * Called when instantiating a PHP file.
     * This event can be used to extend
     * the class that will be instantiated dynamically.
     *
     * @param string $class - the name of the class to be created
     * @param array &$extend - a modifiable list of classes that wish to extend
     * the class.
     */
    public static function loadClassPhpFileWaindigo($class, array &$extend)
    {
        self::loadClass($class, $extend, 'php_file_waindigo');
    } /* END loadClassPhpFileWaindigo */

    /**
     * Called when instantiating a reward handler.
     * This event can be used to
     * extend the class that will be instantiated dynamically.
     *
     * @param string $class - the name of the class to be created
     * @param array &$extend - a modifiable list of classes that wish to extend
     * the class.
     */
    public static function loadClassRewardHandler($class, array &$extend)
    {
        self::loadClass($class, $extend, 'reward_handler');
    } /* END loadClassRewardHandler */

    /**
     * Called when instantiating a specific route prefix class.
     * This event can
     * be used to extend the class that will be instantiated dynamically.
     *
     * @param string $class - the name of the class to be created
     * @param array &$extend - a modifiable list of classes that wish to extend
     * the class.
     */
    public static function loadClassRoutePrefix($class, array &$extend)
    {
        self::loadClass($class, $extend, 'route_prefix');
    } /* END loadClassRoutePrefix */

    /**
     * Called when instantiating a search data handler.
     * This event can be used
     * to extend the class that will be instantiated dynamically.
     *
     * @param string $class - the name of the class to be created
     * @param array &$extend - a modifiable list of classes that wish to extend
     * the class.
     */
    public static function loadClassSearchData($class, array &$extend)
    {
        self::loadClass($class, $extend, 'search_data');
    } /* END loadClassSearchData */

    /**
     * Called when instantiating a search source.
     * This event can be used to
     * extend the class that will be instantiated dynamically.
     *
     * @param string $class - the name of the class to be created
     * @param array &$extend - a modifiable list of classes that wish to extend
     * the class.
     */
    public static function loadClassSearchSource($class, array &$extend)
    {
        self::loadClass($class, $extend, 'search_source');
    } /* END loadClassSearchSource */

    /**
     * Called when instantiating a template.
     * This event can be used to extend
     * the class that will be instantiated dynamically.
     *
     * @param string $class - the name of the class to be created
     * @param array &$extend - a modifiable list of classes that wish to extend
     * the class.
     */
    public static function loadClassTemplateWaindigo($class, array &$extend)
    {
        self::loadClass($class, $extend, 'template_waindigo');
    } /* END loadClassTemplateWaindigo */

    /**
     * Called when instantiating a view.
     * This event can be used to extend the
     * class that will be instantiated dynamically.
     *
     * @param string $class - the name of the class to be created
     * @param array &$extend - a modifiable list of classes that wish to extend
     * the class.
     */
    public static function loadClassView($class, array &$extend)
    {
        self::loadClass($class, $extend, 'view');
    } /* END loadClassView */

    /**
     *
     * @return array $extend
     */
    public function run()
    {
        $extends = $this->_getExtends();

        if ($this->_type) {
            $allExtendedClasses = $this->_getExtendedClasses();
            foreach ($allExtendedClasses as $addOnId => $extendedClasses) {
                if (isset($extendedClasses[$this->_type])) {
                    foreach ($extendedClasses[$this->_type] as $class) {
                        if ($class == $this->_class) {
                            $this->_extend[] = $addOnId . '_Extend_' . $class;
                        }
                    }
                }
            }
        }

        if (!empty($extends)) {
            foreach ($extends as $class => $extend) {
                if ($class == $this->_class && !empty($extend)) {
                    if (is_array($extend)) {
                        foreach ($extend as $extendClass) {
                            if (!in_array($extendClass, $this->_extend)) {
                                $this->_extend[] = $extendClass;
                            }
                        }
                    } else {
                        if (!in_array($extend, $this->_extend)) {
                            $this->_extend[] = $extend;
                        }
                    }
                }
            }
        }

        foreach ($this->_extend as $key => $extend) {
            if (class_exists('XFCP_' . $extend, false)) {
                unset($this->_extend[$key]);
            }
        }

        if (!in_array($this->_class, self::$_runOnce)) {
            $this->_runOnce();
        }

        return $this->_extend;
    } /* END run */

    /**
     *
     * @return array $extend
     */
    protected function _run()
    {
        try {
            return $this->run();
        } catch (Exception $e) {
            return $this->_extend;
        }
    } /* END _run */

    protected function _runOnce()
    {
        $hints = XenForo_Application::get('options')->waindigo_loadClassHints;

        if (isset($hints[$this->_class])) {
            foreach ($hints[$this->_class] as $addOnId) {
                $extendClass = $addOnId . '_Extend_' . $this->_class;
                if (!in_array($extendClass, $this->_extend)) {
                    $this->_extend[] = $extendClass;
                }
            }
        }

        self::$_runOnce[] = $this->_class;
    } /* END _runOnce */

    /**
     *
     * @return array $extend
     */
    protected function _getExtends()
    {
        return array();
    } /* END _getExtends */

    /**
     *
     * @return array ([type] => array ([addon id] => array))
     */
    protected function _getExtendedClasses()
    {
        return array();
    } /* END _getExtendedClasses */

    /**
     *
     * @return array
     */
    public function getExtendedClasses($classesOnly = true, $addOnId = '', $type = '')
    {
        if ($classesOnly || ($addOnId && $type)) {
            $extends = array_filter($this->_getExtends());

            foreach ($extends as $class => $extend) {
                if (!is_array($extend)) {
                    $extends[$class] = array(
                        $extend
                    );
                }
            }
        }

        if ($classesOnly) {
            $allExtendedClasses = $this->_getExtendedClasses();
            foreach ($allExtendedClasses as $addOnId => $extendedClasses) {
                foreach ($extendedClasses as $loadClassType => $typeExtendedClasses) {
                    if ($this->_type && $this->_type != $loadClassType) {
                        continue;
                    }
                    foreach ($typeExtendedClasses as $class) {
                        if (!isset($extends[$class]) || !array_search($addOnId . '_Extend_' . $class, $extends[$class])) {
                            $extends[$class][] = $addOnId . '_Extend_' . $class;
                        }
                    }
                }
            }

            return $extends;
        }

        $extendedClasses = $this->_getExtendedClasses();

        if ($addOnId && $type) {
            foreach ($extends as $class => $extend) {
                if (in_array($addOnId . '_Extend_' . $class, $extend)) {
                    if (!isset($extendedClasses[$addOnId][$type]) || !in_array($class,
                        $extendedClasses[$addOnId][$type])) {
                        $extendedClasses[$addOnId][$type][] = $class;
                    }
                }
            }
        }

        return $extendedClasses;
    } /* END getExtendedClasses */

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
     * Factory method to get the named load class code event listener.
     * The class must exist or be autoloadable or an exception will be thrown.
     *
     * @param string Class to load
     *
     * @return Waindigo_Listener_LoadClass
     */
    public static function create($class)
    {
        $createClass = XenForo_Application::resolveDynamicClass($class, 'waindigo_load_class');
        if (!$createClass) {
            throw new XenForo_Exception("Invalid code event listener '$class' specified");
        }

        return new $createClass();
    } /* END create */

    /**
     *
     * @deprecated Deprecated.
     *
     * @param $class
     * @param $extend
     */
    protected static function _extend($class, array &$extend)
    {
        if (!in_array($class, $extend)) {
            $extend[] = $class;
        }
    } /* END _extend */
}