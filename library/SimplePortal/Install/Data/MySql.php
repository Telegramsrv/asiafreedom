<?php
class SimplePortal_Install_Data_MySql{


    public function getTables(){

        $tables = array();
        $tables["xf_portalcategory"] = "
        	CREATE TABLE IF NOT EXISTS `xf_portalcategory` (
			  `category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `title` varchar(250) NOT NULL,
			  `display_order` int(10) unsigned NOT NULL DEFAULT '1',
			  PRIMARY KEY (`category_id`),
			  KEY `display_order` (`display_order`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8
   		 ";

        $tables["xf_portalitem"] = "
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

        return $tables;
    }

    public function getContentTypesAndFields(){
        return array();
    }
}