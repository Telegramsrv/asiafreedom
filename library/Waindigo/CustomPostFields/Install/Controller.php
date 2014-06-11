<?php

class Waindigo_CustomPostFields_Install_Controller extends Waindigo_Install
{

    protected $_resourceManagerUrl = 'http://xenforo.com/community/resources/custom-post-fields-by-waindigo.2970/';

    /**
     *
     * @see Waindigo_Install::_getPrerequisites()
     */
    protected function _getPrerequisites()
    {
        return array(
            'Waindigo_CustomFields' => '1394722759'
        );
    } /* END _getPrerequisites */

    protected function _getTables()
    {
        return array(
            'xf_forum_post_field' => array(
                'node_id' => 'INT(10) UNSIGNED NOT NULL', /* END 'node_id' */
                'field_id' => 'VARCHAR(64) NOT NULL', /* END 'field_id' */
                'field_value' => 'MEDIUMTEXT NOT NULL', /* END 'field_value' */
            ), /* END 'xf_forum_post_field' */
            'xf_post_field' => array(
                'field_id' => 'VARCHAR(64) NOT NULL PRIMARY KEY', /* END 'field_id' */
                'field_group_id' => 'INT(10) UNSIGNED NOT NULL', /* END 'field_group_id' */
                'display_order' => 'INT(10) UNSIGNED NOT NULL DEFAULT \'0\'', /* END 'display_order' */
                'materialized_order' => 'INT(10) UNSIGNED NOT NULL DEFAULT \'0\'', /* END 'materialized_order' */
                'field_type' => 'ENUM(\'textbox\',\'textarea\',\'select\',\'radio\',\'checkbox\',\'multiselect\',\'callback\') NOT NULL DEFAULT \'textbox\'', /* END 'field_type' */
                'field_choices' => 'BLOB NOT NULL', /* END 'field_choices' */
                'match_type' => 'ENUM(\'none\',\'number\',\'alphanumeric\',\'email\',\'url\',\'regex\',\'callback\') NOT NULL DEFAULT \'none\'', /* END 'match_type' */
                'match_regex' => 'VARCHAR(250) NOT NULL DEFAULT \'\'', /* END 'match_regex' */
                'match_callback_class' => 'VARCHAR(75) NOT NULL DEFAULT \'\'', /* END 'match_callback_class' */
                'match_callback_method' => 'VARCHAR(75) NOT NULL DEFAULT  \'\'', /* END 'match_callback_method' */
                'max_length' => 'INT(10) UNSIGNED NOT NULL DEFAULT \'0\'', /* END 'max_length' */
                'below_title_on_thread_create' => 'TINYINT(4) UNSIGNED NOT NULL DEFAULT \'0\'', /* END 'below_title_on_thread_create' */
                'display_template' => 'TEXT NOT NULL', /* END 'display_template' */
                'display_callback_class' => 'VARCHAR(75) NOT NULL DEFAULT \'\'', /* END 'display_callback_class' */
                'display_callback_method' => 'VARCHAR(75) NOT NULL DEFAULT  \'\'', /* END 'display_callback_method' */
                'allowed_user_group_ids' => 'BLOB NOT NULL', /* END 'allowed_user_group_ids' */
                'addon_id' => 'VARCHAR(25) NOT NULL DEFAULT \'\'', /* END 'addon_id' */
                'field_choices_callback_class' => 'VARCHAR(75) NOT NULL DEFAULT \'\'', /* END 'field_choices_callback_class' */
                'field_choices_callback_method' => 'VARCHAR(75) NOT NULL DEFAULT \'\'', /* END 'field_choices_callback_method' */
                'field_callback_class' => 'VARCHAR(75) NOT NULL DEFAULT \'\'', /* END 'field_callback_class' */
                'field_callback_method' => 'VARCHAR(75) NOT NULL DEFAULT  \'\'', /* END 'field_callback_method' */
                'export_callback_class' => 'VARCHAR(75) NOT NULL DEFAULT \'\'', /* END 'export_callback_class' */
                'export_callback_method' => 'VARCHAR(75) NOT NULL DEFAULT  \'\'', /* END 'export_callback_method' */
            ), /* END 'xf_post_field' */
            'xf_post_field_group' => array(
                'field_group_id' => 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY', /* END 'field_group_id' */
                'display_order' => 'INT(10) UNSIGNED NOT NULL', /* END 'display_order' */
            ), /* END 'xf_post_field_group' */
            'xf_post_field_value' => array(
                'post_id' => 'INT(10) UNSIGNED NOT NULL', /* END 'post_id' */
                'field_id' => 'VARCHAR(64) NOT NULL', /* END 'field_id' */
                'field_value' => 'MEDIUMTEXT NOT NULL', /* END 'field_value' */
            ), /* END 'xf_post_field_value' */
        );
    } /* END _getTables */

    protected function _getTableChanges()
    {
        return array(
            'xf_forum' => array(
                'post_field_cache' => 'MEDIUMBLOB NULL COMMENT \'Serialized data from xf_forum_post_field, [group_id][field_id] => field_id\'', /* END 'post_field_cache' */
    	        'custom_post_fields' => 'MEDIUMBLOB NULL', /* END 'custom_post_fields' */
    	        'required_post_fields' => 'MEDIUMBLOB NULL', /* END 'required_post_fields' */
    	    ), /* END 'xf_forum' */
        );
    } /* END _getTableChanges */

    protected function _getAddOnTableChanges()
    {
        return array(
            'Waindigo_Library' => array(
                'xf_library' => array(
                    'post_field_cache' => 'MEDIUMBLOB NULL COMMENT \'Serialized data from xf_forum_post_field, [group_id][field_id] => field_id\'', /* END 'post_field_cache' */
                    'custom_post_fields' => 'MEDIUMBLOB NULL', /* END 'custom_post_fields' */
                    'required_post_fields' => 'MEDIUMBLOB NULL', /* END 'required_post_fields' */
                ), /* END 'xf_library' */
                'xf_article_page' => array(
                    'custom_post_fields' => 'MEDIUMBLOB NULL', /* END 'custom_post_fields' */
                ), /* END 'xf_article_page' */

            )
        );
    } /* END _getAddOnTableChanges */

    protected function _getPrimaryKeys()
    {
        return array(
            'xf_post_field_value' => array(
                'post_id',
                'field_id'
            ), /* END 'xf_post_field_value' */
        );
    } /* END _getPrimaryKeys */

    protected function _getKeys()
    {
        return array(
            'xf_thread_field' => array(
                'materialized_order' => array(
                    'materialized_order'
                ), /* END 'materialized_order' */
            ), /* END 'xf_thread_field' */
            'xf_thread_field_value' => array(
                'field_id' => array(
                    'field_id'
                ), /* END 'field_id' */
            ), /* END 'xf_thread_field_value' */
            'xf_post_field' => array(
                'materialized_order' => array(
                    'materialized_order'
                ), /* END 'materialized_order' */
            ), /* END 'xf_post_field' */
            'xf_post_field_value' => array(
                'field_id' => array(
                    'field_id'
                ), /* END 'field_id' */
            ), /* END 'xf_post_field_value' */
        );
    } /* END _getKeys */

    protected function _postInstallAfterTransaction()
    {
        $this->_makeTableChanges(
            array(
                'xf_post' => array(
                    'custom_post_fields' => 'MEDIUMBLOB NULL', /* END 'custom_post_fields' */
                ), /* END 'xf_post' */
            ));
    } /* END _postInstallAfterTransaction */

    protected function _postUninstallAfterTransaction()
    {
        $this->_dropTableChanges(
            array(
                'xf_post' => array(
                    'custom_post_fields' => 'MEDIUMBLOB NULL', /* END 'custom_post_fields' */
                ), /* END 'xf_post' */
            ));
    } /* END _postUninstallAfterTransaction */ /* END _preUninstall */
}