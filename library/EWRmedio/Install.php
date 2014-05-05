<?php

class EWRmedio_Install
{
	private static $_instance;
	protected $_db;

	public static final function getInstance()
	{
		if (!self::$_instance)
		{
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	protected function _getDb()
	{
		if ($this->_db === null)
		{
			$this->_db = XenForo_Application::get('db');
		}

		return $this->_db;
	}

	public static function installCode($existingAddOn, $addOnData)
	{
		$endVersion = $addOnData['version_id'];
		$strVersion = $existingAddOn ? ($existingAddOn['version_id'] + 1) : 1;

		$install = self::getInstance();

		for ($i = $strVersion; $i <= $endVersion; $i++)
		{
			$method = '_install_'.$i;

			if (method_exists($install, $method))
			{
				$install->$method();
			}
		}

		$lowLoc = XenForo_Helper_File::getExternalDataPath()."/media";
		$highLoc = XenForo_Helper_File::getExternalDataPath()."/media/high";
		
		if (!is_dir($lowLoc)) { mkdir($lowLoc, 0777); }
		if (!is_dir($highLoc)) { mkdir($highLoc, 0777); }
		
		XenForo_Model::create('EWRmedio_Model_Services')->rebuildServices(false);
	}

	protected function _install_1()
	{
 		$this->_getDb()->query("
			CREATE TABLE IF NOT EXISTS `EWRmedio_categories` (
				`category_id`			int(10) unsigned	NOT NULL AUTO_INCREMENT,
				`category_name`			varchar(255)		NOT NULL,
				`category_description`	text				NOT NULL,
				`category_order`		int(10) unsigned	NOT NULL DEFAULT '1',
				`category_parent`		int(10) unsigned	NOT NULL DEFAULT '0',
				`category_disabled`		int(1) unsigned		NOT NULL DEFAULT '0',
				PRIMARY KEY (`category_id`),
				INDEX (`category_parent`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->_getDb()->query("INSERT IGNORE INTO `EWRmedio_categories` (`category_id`, `category_name`, `category_description`, `category_order`, `category_parent`)
			VALUES ('1', 'General Media', 'General Media', '1', '0')");

 		$this->_getDb()->query("
			CREATE TABLE IF NOT EXISTS `EWRmedio_comments` (
				`comment_id`		int(10) unsigned						NOT NULL AUTO_INCREMENT,
				`media_id`			int(10) unsigned						NOT NULL,
				`post_id`			int(10) unsigned						NOT NULL DEFAULT '0',
				`user_id`			int(10) unsigned						NOT NULL,
				`username`			varchar(50)								NOT NULL,
				`comment_date`		int(10) unsigned						NOT NULL,
				`comment_message`	mediumtext								NOT NULL,
				`comment_state`		enum('visible','moderated','deleted')	NOT NULL DEFAULT 'visible',
				`comment_ip`		int(10) unsigned						NOT NULL DEFAULT '0',
				PRIMARY KEY (`comment_id`),
				INDEX (`media_id`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");

 		$this->_getDb()->query("
			CREATE TABLE IF NOT EXISTS `EWRmedio_keylinks` (
				`keyword_id`	int(10) unsigned	NOT NULL,
				`media_id`		int(10) unsigned	NOT NULL,
				`user_id`		int(10) unsigned	NOT NULL,
				`keylink_id`	int(10) unsigned	NOT NULL AUTO_INCREMENT,
				`keylink_date`	int(10) unsigned	NOT NULL,
				PRIMARY KEY (`keylink_id`),
				UNIQUE KEY `UNIQUE` (`keyword_id`,`media_id`),
				INDEX (`media_id`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");

 		$this->_getDb()->query("
			CREATE TABLE IF NOT EXISTS `EWRmedio_keywords` (
				`keyword_id`	int(10) unsigned	NOT NULL AUTO_INCREMENT,
				`keyword_text`	varchar(255)		NOT NULL,
				PRIMARY KEY (`keyword_id`),
				UNIQUE KEY (`keyword_text`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");

 		$this->_getDb()->query("
			CREATE TABLE IF NOT EXISTS `EWRmedio_media` (
				`media_id`				int(10) unsigned						NOT NULL AUTO_INCREMENT,
				`category_id`			int(10) unsigned						NOT NULL,
				`user_id`				int(10) unsigned						NOT NULL,
				`username`				varchar(50)								NOT NULL,
				`thread_id`				int(10) unsigned						NOT NULL DEFAULT '0',
				`service_id`			int(10) unsigned						NOT NULL,
				`service_value`			varchar(255)							NOT NULL,
				`service_value2`		varchar(255)							NOT NULL,
				`media_title`			varchar(255)							NOT NULL,
				`media_description`		mediumtext								NOT NULL,
				`media_keywords`		text									NOT NULL,
				`media_date`			int(10) unsigned						NOT NULL,
				`media_duration`		int(10) unsigned						NOT NULL,
				`media_state`			enum('visible','moderated','deleted')	NOT NULL DEFAULT 'visible',
				`media_likes`			int(10) unsigned						NOT NULL DEFAULT '0',
				`media_like_users`		blob									NOT NULL,
				`media_comments`		int(10) unsigned						NOT NULL DEFAULT '0',
				`media_views`			int(10) unsigned						NOT NULL DEFAULT '0',
				`media_custom1`			varchar(255)							NOT NULL,
				`media_custom2`			varchar(255)							NOT NULL,
				`media_custom3`			varchar(255)							NOT NULL,
				`media_custom4`			varchar(255)							NOT NULL,
				`media_custom5`			varchar(255)							NOT NULL,
				`media_custom_cache`	blob									NOT NULL,
				`last_comment_date`		int(10) unsigned						NOT NULL,
				`last_comment_id`		int(10) unsigned						NOT NULL,
				`last_comment_user_id`	int(10) unsigned						NOT NULL,
				`last_comment_username`	varchar(50)								NOT NULL,
				PRIMARY KEY (`media_id`),
				UNIQUE KEY `UNIQUE` (`service_id`,`service_value`(100),`service_value2`(100)),
				INDEX (`category_id`),
				INDEX (`user_id`),
				INDEX (`service_id`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");

 		$this->_getDb()->query("
			CREATE TABLE IF NOT EXISTS `EWRmedio_playlists` (
				`user_id` 				int(10) unsigned 						NOT NULL,
				`playlist_id` 			int(10) unsigned 						NOT NULL AUTO_INCREMENT,
				`playlist_date`			int(10) unsigned						NOT NULL,
				`playlist_update`		int(10) unsigned						NOT NULL,
				`playlist_name` 		varchar(255) 							NOT NULL,
				`playlist_description` 	text 									NOT NULL,
				`playlist_state` 		enum('public','unlisted','private') 	NOT NULL DEFAULT 'private',
				`playlist_media` 		text									NOT NULL,
				`playlist_views`		int(10) unsigned						NOT NULL DEFAULT '0',
				`playlist_likes`		int(10) unsigned						NOT NULL DEFAULT '0',
				`playlist_like_users`	blob									NOT NULL,
				PRIMARY KEY (`playlist_id`),
				INDEX (`user_id`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");

 		$this->_getDb()->query("
			CREATE TABLE IF NOT EXISTS `EWRmedio_read` (
				`media_read_id` 	int(10) unsigned 	NOT NULL AUTO_INCREMENT,
				`user_id` 			int(10) unsigned 	NOT NULL,
				`media_id` 			int(10) unsigned 	NOT NULL,
				`media_read_date` 	int(10) unsigned 	NOT NULL,
				PRIMARY KEY (`media_read_id`),
				UNIQUE KEY `user_id_media_id` (`user_id`,`media_id`),
				KEY `media_id` (`media_id`),
				KEY `media_read_date` (`media_read_date`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");

 		$this->_getDb()->query("
			CREATE TABLE IF NOT EXISTS `EWRmedio_services` (
				`service_id`			int(10) unsigned			NOT NULL AUTO_INCREMENT,
				`service_name`			varchar(255)				NOT NULL,
				`service_media`			enum('video','gallery')		NOT NULL DEFAULT 'video',
				`service_regex`			text						NOT NULL,
				`service_playlist`		text						NOT NULL,
				`service_url`			varchar(255)				NOT NULL,
				`service_callback`		varchar(75)					NOT NULL,
				`service_width`			int(10) unsigned			NOT NULL,
				`service_height`		int(10) unsigned			NOT NULL,
				`service_embed`			text						NOT NULL,
				`service_local`			int(1) unsigned				NOT NULL,
			PRIMARY KEY (`service_id`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		
 		$this->_getDb()->query("
			CREATE TABLE IF NOT EXISTS `EWRmedio_userlinks` (
				`username_id`		int(10) unsigned	NOT NULL,
				`media_id`			int(10) unsigned	NOT NULL,
				`user_id`			int(10) unsigned	NOT NULL,
				`userlink_id`		int(10) unsigned	NOT NULL AUTO_INCREMENT,
				`userlink_date`		int(10) unsigned	NOT NULL,
				PRIMARY KEY (`userlink_id`),
				UNIQUE KEY `UNIQUE` (`username_id`,`media_id`),
				KEY `media_id` (`media_id`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");

 		$this->_getDb()->query("
			CREATE TABLE IF NOT EXISTS `EWRmedio_users` (
				`user_id`				int(10) unsigned 							NOT NULL,
				`media_watch_state`		enum('','watch_no_email','watch_email') 	NOT NULL,
				PRIMARY KEY (`user_id`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");

 		$this->_getDb()->query("
			CREATE TABLE IF NOT EXISTS `EWRmedio_watch` (
				`user_id`			int(10) unsigned	NOT NULL,
				`media_id`			int(10) unsigned	NOT NULL,
				`email_subscribe`	int(3) unsigned		NOT NULL,
				PRIMARY KEY (`user_id`,`media_id`),
				KEY `media_id_email_subscribe` (`media_id`,`email_subscribe`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");

		$this->_getDb()->query("INSERT IGNORE INTO `xf_content_type` (`content_type`, `addon_id`, `fields`) VALUES ('media', 'EWRmedio', '')");
		$this->_getDb()->query("INSERT IGNORE INTO `xf_content_type` (`content_type`, `addon_id`, `fields`) VALUES ('media_comment', 'EWRmedio', '')");
		$this->_getDb()->query("INSERT IGNORE INTO `xf_content_type` (`content_type`, `addon_id`, `fields`) VALUES ('media_playlist', 'EWRmedio', '')");
		$this->_getDb()->query("INSERT IGNORE INTO `xf_content_type_field` (`content_type`, `field_name`, `field_value`) VALUES ('media', 'alert_handler_class', 'EWRmedio_AlertHandler_Media')");
		$this->_getDb()->query("INSERT IGNORE INTO `xf_content_type_field` (`content_type`, `field_name`, `field_value`) VALUES ('media', 'like_handler_class', 'EWRmedio_LikeHandler_Media')");
		$this->_getDb()->query("INSERT IGNORE INTO `xf_content_type_field` (`content_type`, `field_name`, `field_value`) VALUES ('media', 'news_feed_handler_class', 'EWRmedio_NewsFeedHandler_Media')");
		$this->_getDb()->query("INSERT IGNORE INTO `xf_content_type_field` (`content_type`, `field_name`, `field_value`) VALUES ('media', 'report_handler_class', 'EWRmedio_ReportHandler_Media')");
		$this->_getDb()->query("INSERT IGNORE INTO `xf_content_type_field` (`content_type`, `field_name`, `field_value`) VALUES ('media', 'search_handler_class', 'EWRmedio_SearchHandler_Media')");
		$this->_getDb()->query("INSERT IGNORE INTO `xf_content_type_field` (`content_type`, `field_name`, `field_value`) VALUES ('media_comment', 'alert_handler_class', 'EWRmedio_AlertHandler_Comments')");
		$this->_getDb()->query("INSERT IGNORE INTO `xf_content_type_field` (`content_type`, `field_name`, `field_value`) VALUES ('media_comment', 'news_feed_handler_class', 'EWRmedio_NewsFeedHandler_Comments')");
		$this->_getDb()->query("INSERT IGNORE INTO `xf_content_type_field` (`content_type`, `field_name`, `field_value`) VALUES ('media_comment', 'report_handler_class', 'EWRmedio_ReportHandler_Comments')");
		$this->_getDb()->query("INSERT IGNORE INTO `xf_content_type_field` (`content_type`, `field_name`, `field_value`) VALUES ('media_playlist', 'like_handler_class', 'EWRmedio_LikeHandler_Playlists')");
		XenForo_Model::create('XenForo_Model_ContentType')->rebuildContentTypeCache();
		
		$targetXml = XenForo_Application::getInstance()->getRootDir().'/library/EWRmedio/Services';
		XenForo_Model::create('EWRmedio_Model_Services')->importService($targetXml.'/YouTube.xml');
	}

	protected function _install_39()
	{
		$this->_getDb()->query("ALTER TABLE `EWRmedio_services` CHANGE `service_regex` `service_regex` TEXT NOT NULL");
	}

	protected function _install_51()
	{
		$this->addColumnIfNotExist("EWRmedio_playlists", "playlist_date", "int(10) unsigned NOT NULL");
	}

	protected function _install_52()
	{
		$this->addColumnIfNotExist("EWRmedio_media", "last_comment_date", "int(10) unsigned NOT NULL");
		$this->addColumnIfNotExist("EWRmedio_media", "last_comment_id", "int(10) unsigned NOT NULL");
		$this->addColumnIfNotExist("EWRmedio_media", "last_comment_user_id", "int(10) unsigned NOT NULL");
		$this->addColumnIfNotExist("EWRmedio_media", "last_comment_username", "varchar(50) NOT NULL");

 		$this->_getDb()->query("
			CREATE TABLE IF NOT EXISTS `EWRmedio_read` (
				`media_read_id` 	int(10) unsigned 	NOT NULL AUTO_INCREMENT,
				`user_id` 			int(10) unsigned 	NOT NULL,
				`media_id` 			int(10) unsigned 	NOT NULL,
				`media_read_date` 	int(10) unsigned 	NOT NULL,
				PRIMARY KEY (`media_read_id`),
				UNIQUE KEY `user_id_media_id` (`user_id`,`media_id`),
				KEY `media_id` (`media_id`),
				KEY `media_read_date` (`media_read_date`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");

 		$this->_getDb()->query("
			CREATE TABLE IF NOT EXISTS `EWRmedio_users` (
				`user_id`				int(10) unsigned 							NOT NULL,
				`media_watch_state`		enum('','watch_no_email','watch_email') 	NOT NULL,
				PRIMARY KEY (`user_id`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");

 		$this->_getDb()->query("
			CREATE TABLE IF NOT EXISTS `EWRmedio_watch` (
				`user_id`			int(10) unsigned	NOT NULL,
				`media_id`			int(10) unsigned	NOT NULL,
				`email_subscribe`	int(3) unsigned		NOT NULL,
				PRIMARY KEY (`user_id`,`media_id`),
				KEY `media_id_email_subscribe` (`media_id`,`email_subscribe`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
	}

	protected function _install_53()
	{
		$this->addColumnIfNotExist("EWRmedio_media", "media_custom_cache", "blob NOT NULL");
	}

	protected function _install_55()
	{
		$this->addColumnIfNotExist("EWRmedio_services", "service_callback", "varchar(75) NOT NULL");
		$this->addColumnIfNotExist("EWRmedio_services", "service_embed", "text NOT NULL");
		
		$this->dropColumnIfExist("EWRmedio_services", "service_type");
		$this->dropColumnIfExist("EWRmedio_services", "service_slug");
		$this->dropColumnIfExist("EWRmedio_services", "service_feed");
		$this->dropColumnIfExist("EWRmedio_services", "service_movie");
		$this->dropColumnIfExist("EWRmedio_services", "service_value2");
		$this->dropColumnIfExist("EWRmedio_services", "service_thumb");
		$this->dropColumnIfExist("EWRmedio_services", "service_title");
		$this->dropColumnIfExist("EWRmedio_services", "service_description");
		$this->dropColumnIfExist("EWRmedio_services", "service_duration");
		$this->dropColumnIfExist("EWRmedio_services", "service_keywords");
		$this->dropColumnIfExist("EWRmedio_services", "service_errors");
		$this->dropColumnIfExist("EWRmedio_services", "service_parameters");
	}
	
	protected function _install_56()
	{
		$this->_getDb()->query("DELETE IGNORE FROM `xf_content_type_field` WHERE content_type = 'media' AND field_name = 'moderation_queue_handler_class'");
		XenForo_Model::create('XenForo_Model_ContentType')->rebuildContentTypeCache();
	}
	
	protected function _install_57()
	{
		$this->addColumnIfNotExist("EWRmedio_services", "service_local", "int(1) unsigned NOT NULL");
	}
	
	protected function _install_60()
	{
		$this->_getDb()->query("ALTER TABLE `EWRmedio_playlists` CHANGE `playlist_state` `playlist_state` enum('public','unlisted','private') NOT NULL DEFAULT 'private'");
		
		$this->addColumnIfNotExist("EWRmedio_playlists", "playlist_update", "int(10) unsigned NOT NULL");
		$this->addColumnIfNotExist("EWRmedio_playlists", "playlist_views", "int(10) unsigned NOT NULL DEFAULT '0'");
		$this->addColumnIfNotExist("EWRmedio_playlists", "playlist_likes", "int(10) unsigned NOT NULL DEFAULT '0'");
		$this->addColumnIfNotExist("EWRmedio_playlists", "playlist_like_users", "blob NOT NULL");
		
		$this->_getDb()->query("INSERT IGNORE INTO `xf_content_type` (`content_type`, `addon_id`, `fields`) VALUES ('media_playlist', 'EWRmedio', '')");
		$this->_getDb()->query("INSERT IGNORE INTO `xf_content_type_field` (`content_type`, `field_name`, `field_value`) VALUES ('media_playlist', 'like_handler_class', 'EWRmedio_LikeHandler_Playlists')");
		XenForo_Model::create('XenForo_Model_ContentType')->rebuildContentTypeCache();
	}
	
	protected function _install_61()
	{
 		$this->_getDb()->query("
			CREATE TABLE IF NOT EXISTS `EWRmedio_userlinks` (
				`username_id`		int(10) unsigned	NOT NULL,
				`media_id`			int(10) unsigned	NOT NULL,
				`user_id`			int(10) unsigned	NOT NULL,
				`userlink_id`		int(10) unsigned	NOT NULL AUTO_INCREMENT,
				`userlink_date`		int(10) unsigned	NOT NULL,
				PRIMARY KEY (`userlink_id`),
				UNIQUE KEY `UNIQUE` (`username_id`,`media_id`),
				KEY `media_id` (`media_id`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
	}
	
	protected function _install_63()
	{
		$this->addColumnIfNotExist("EWRmedio_services", "service_playlist", "text NOT NULL");
	}

	public static function uninstallCode()
	{
		$uninstall = self::getInstance();
		$uninstall->_uninstall_0();
	}

	protected function _uninstall_0()
	{
 		$this->_getDb()->query("
			DROP TABLE IF EXISTS
				`EWRmedio_categories`,
				`EWRmedio_comments`,
				`EWRmedio_keylinks`,
				`EWRmedio_keywords`,
				`EWRmedio_media`,
				`EWRmedio_playlists`,
				`EWRmedio_read`,
				`EWRmedio_services`,
				`EWRmedio_users`,
				`EWRmedio_watch`
		");

		$this->_getDb()->query("DELETE IGNORE FROM `xf_content_type` WHERE content_type = 'media'");
		$this->_getDb()->query("DELETE IGNORE FROM `xf_content_type` WHERE content_type = 'media_comment'");
		$this->_getDb()->query("DELETE IGNORE FROM `xf_content_type` WHERE content_type = 'media_playlist'");
		$this->_getDb()->query("DELETE IGNORE FROM `xf_content_type_field` WHERE content_type = 'media'");
		$this->_getDb()->query("DELETE IGNORE FROM `xf_content_type_field` WHERE content_type = 'media_comment'");
		$this->_getDb()->query("DELETE IGNORE FROM `xf_content_type_field` WHERE content_type = 'media_playlist'");
		XenForo_Model::create('XenForo_Model_ContentType')->rebuildContentTypeCache();

		$targetLoc = glob(XenForo_Helper_File::getExternalDataPath()."/media/high/*.jpg");
		foreach ($targetLoc AS $file) { unlink($file); }
		
		$targetLoc = glob(XenForo_Helper_File::getExternalDataPath()."/media/*.jpg");
		foreach ($targetLoc AS $file) { unlink($file); }

		$highLoc = XenForo_Helper_File::getExternalDataPath()."/media/high";
		$lowLoc = XenForo_Helper_File::getExternalDataPath()."/media";
		
		if (is_dir($highLoc)) { rmdir($highLoc); }
		if (is_dir($lowLoc)) { rmdir($lowLoc); }
	}

	public function addColumnIfNotExist($table, $field, $attr)
	{
		if ($this->_getDb()->fetchRow('SHOW columns FROM `'.$table.'` WHERE Field = ?', $field))
		{
			return false;
		}
		
		return $this->_getDb()->query("ALTER TABLE `".$table."` ADD `".$field."` ".$attr);
	}

	public function dropColumnIfExist($table, $field)
	{
		if ($this->_getDb()->fetchRow('SHOW columns FROM `'.$table.'` WHERE Field = ?', $field))
		{
			return $this->_getDb()->query("ALTER TABLE `".$table."` DROP COLUMN `".$field."`");
		}
		
		return false;
	}
}