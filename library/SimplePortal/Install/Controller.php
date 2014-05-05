<?php

class SimplePortal_Install_Controller
{
    public $installMethods = array( // version => method
        2 => '_installVersion1',
        5 => '_installVersion5',
        9 => '_installVersion9',
        39 => '_installVersion39',
        48 => '_installVersion48',
        1010030 => 'installBeta1',
        1010071 => 'install1010071',
        1020031 => 'install1020031',
        1020073 => 'install1020073'

    );

    /**
     * let's check if all columns are here!
     */
    public function install1020073(){
        $query = array();

        if (!$this->_checkIfExist('xf_portalcategory','style_id')){
            //add additional style_id column
            $query['add_styleId_column'] = "
              ALTER TABLE `xf_portalcategory` ADD style_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Style override for specific category'
            ";
        }

        if (!$this->_checkIfExist('xf_portalitem','extra_data')){
            //add additional data column
            $query['add_column'] = "
              ALTER TABLE `xf_portalitem` ADD `extra_data` MEDIUMBLOB NOT NULL COMMENT 'Serialized. Stores any extra data relevant to the item';
            ";
        }

        $this->runQuery($query);

    }


    public function install1020031(){

        if (!$this->_checkIfExist('xf_portalitem','extra_data')){
            //add additional data column
            $query['add_column'] = "
              ALTER TABLE `xf_portalitem` ADD `extra_data` MEDIUMBLOB NOT NULL COMMENT 'Serialized. Stores any extra data relevant to the item';
            ";

            $this->runQuery($query);
        }

    }

    public function install1010071()
    {
        // bugfix
        $query = array();
        if ($this->checkIfIndexExists('xf_portalitem', 'category_id')) {
            $query['drop_index'] = "ALTER TABLE xf_portalitem DROP INDEX category_id";
        }

        if (!$this->checkIfIndexExists('xf_portalitem', 'uniquecontent')){
            $query['add_index'] = "ALTER TABLE xf_portalitem ADD UNIQUE `uniquecontent` ( `content_type` , `content_id` )";
        }


        $this->runQuery($query);
    }


    public function installBeta1()
    {
        SimplePortal_Helper_Install::addHandlerClass('thread', 'SimplePortal_ItemHandler_Thread', false);
    }

    ## OLD INSTALLPART pre Version 1.1##


    public function _installVersion1()
    {
        $query = array();
        $query["xf_portalcategory"] = "
        	CREATE TABLE IF NOT EXISTS `xf_portalcategory` (
			  `category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `title` varchar(250) NOT NULL,
			  `display_order` int(10) unsigned NOT NULL DEFAULT '1',
			  PRIMARY KEY (`category_id`),
			  KEY `display_order` (`display_order`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8
   		 ";

        $query["xf_portalitem"] = "
		        CREATE TABLE IF NOT EXISTS `xf_portalitem` (
				  `portalItem_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				  `content_type` varchar(25) NOT NULL,
				  `content_id` int(10) unsigned NOT NULL,
				  `display_order` int(10) unsigned NOT NULL DEFAULT '1',
				  `category_id` int(10) unsigned NOT NULL DEFAULT '0',
				  PRIMARY KEY (`portalItem_id`),
				  UNIQUE KEY `category_id` (`content_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8
    	";

        $this->runQuery($query);
    }

    public function _installVersion5()
    {
        $query = array();
        if (!$this->_checkIfExist('xf_portalitem', 'attachment')) {
            $query['add_attachmentcolumn'] = "
			ALTER TABLE  `xf_portalitem` ADD  `attachment` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
		";
            $this->runQuery($query);
        }

    }

    public function _installVersion9()
    {
        $query = array();
        if (!$this->_checkIfExist('xf_portalitem', 'attachment_id')) {
            $query['add_attachmentid'] = "
				ALTER TABLE  `xf_portalitem` ADD  `attachment_id` INT UNSIGNED NOT NULL DEFAULT  '0'
			";
            $this->runQuery($query);
        }
    }


    public function _installVersion39()
    {
        $query = array();
        if (!$this->_checkIfExist('xf_portalcategory', 'item_count')) {
            $query['add_counter'] = "
    			ALTER TABLE xf_portalcategory ADD `item_count` int(10) unsigned NOT NULL default '0'
    		";

            $this->runQuery($query);
        }
    }


    public function _installVersion48()
    {

        $query = array();
        $db = XenForo_Application::getDb();
        $db->beginTransaction();

        $query['bugfix'] = "
				SELECT portalitem.portalitem_Id, thread.thread_id
				FROM xf_portalitem AS portalitem
				LEFT JOIN xf_thread AS thread ON ( portalitem.content_id = thread.thread_id )
				WHERE portalitem.content_type = " . $db->quote('thread');

        $items = $db->fetchAll($query['bugfix']);

        foreach ($items AS $i => $item) {
            if ($item['thread_id'] == '' OR $item['thread_id'] === NULL) {
                $db->query("DELETE FROM xf_portalitem where portalitem_Id = ?", $item['portalitem_Id']);
            }
        }

        $db->commit();
    }


    public static function uninstall()
    {
        $query = self::getUninstallQueries();

        self::runQuery($query);
    }

    public static function getUninstallQueries()
    {

        $query = array();

        $query["xf_portalcategory"] = "
        	DROP TABLE IF EXISTS xf_portalcategory
    	";

        $query["xf_portalitem"] = "
        	DROP TABLE IF EXISTS xf_portalitem
    	";

        $db = XenForo_Application::getDb();

        $fieldNameQuoted = $db->quote('simpleportal_handler_class');

        $query["remove_contenttypefiels"] = "DELETE FROM xf_content_type_field WHERE field_name = " . $fieldNameQuoted;

##uninstall_code##
        return $query;
    }

    public static $isUpgrade = false;

    public static function install($existingAddOn,
                                   $addOnData,
                                   $xml)
    {
        if ($existingAddOn) {

            $currentVersion = $existingAddOn['version_id'];
        }


        $installClassInstance = new self();

        $installClassInstance->runBeforeInstallMethodCall();

        $upgrades = $installClassInstance->installMethods;


        ksort($upgrades, SORT_NUMERIC);
        if (isset($currentVersion)) {
            foreach ($upgrades AS $key => $upgrade) {
                if ($key <= $currentVersion) {
                    unset($upgrades[$key]);
                }
            }
        }

        foreach ($upgrades AS $method) {
            $installClassInstance->$method();
        }
    }


    public static function runQuery(array $queries)
    {
        $db = XenForo_Application::getDb();
        foreach ($queries AS $id => $query) {
            try {
                $db->query($query);
            } catch (XenForo_Exception $e){}

        }
    }

    static private $instance = null;


    public static function addColumn($table, $field, $attr)
    {
        if (!self::_checkIfExist($table, $field)) {
            $db = XenForo_Application::get('db');
            return $db->query("ALTER TABLE `" . $table . "` ADD `" . $field . "` " . $attr);
        }
    }

    protected static function _checkIfExist($table, $field)
    {
        $db = XenForo_Application::get('db');
        if ($db->fetchRow('SHOW columns FROM `' . $table . '` WHERE Field = ?', $field)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * @deprecated, will be removed with simple portal 1.3
     * @param $addonId
     * @return bool
     * @throws XenForo_Exception
     */
    protected static function _checkIfAddonExists($addonId)
    {
        /** @var $addonModel    XenForo_Model_AddOn */
        $addonModel = XenForo_Model::create('XenForo_Model_AddOn');
        if (!$addonModel->getAddOnById($addonId)) {
            throw new XenForo_Exception('This addon requires ' . $addonId);
        }
        return true;
    }

    protected function checkIfIndexExists($table, $name)
    {
        $db = XenForo_Application::getDb();
        $query = "SHOW INDEX FROM $table WHERE KEY_NAME = " . $db->quote($name);

        if ($db->fetchRow($query)) {
            return true;
        }
        return false;
    }


    protected function runBeforeInstallMethodCall()
    {
        $this->checkXFVersion(1020070);
    }

    protected $XfVersionIds = array(
        1020070 => '1.2.0'
    );


    protected function checkXFVersion($versionId)
    {
        if (XenForo_Application::$versionId < $versionId) {
            throw new XenForo_Exception('This add-on requires XenForo ' . $this->XfVersionIds[$versionId] . ' or higher.', true);
        }
    }

    public function __construct()
    {
        $this->db = XenForo_Application::getDb();
    }

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $db;
}