<?php

class Waindigo_CustomFields_Install_Controller extends Waindigo_Install
{

    protected $_resourceManagerUrl = 'http://xenforo.com/community/resources/custom-fields-by-waindigo.885/';

    protected function _getTableNameChangesOnInstall()
    {
        return array(
            'xf_resource_category_field' => 'xf_resource_field_category', /* END 'xf_resource_category_field' */
        );
    } /* END _getTableNameChangesOnInstall */

    protected function _getFieldNameChanges()
    {
        return array(
            'xf_resource_category' => array(
                'custom_resource_fields' => 'category_resource_fields MEDIUMBLOB NULL', /* END 'custom_resource_fields' */
            ), /* END 'xf_category' */
        );
    } /* END _getFieldNameChanges */

    protected function _getTables()
    {
        return array(
            'xf_thread_field' => array(
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
                'viewable_forum_view' => 'TINYINT(4) UNSIGNED NOT NULL DEFAULT \'0\'', /* END 'viewable_forum_view' */
                'viewable_thread_view' => 'TINYINT(4) UNSIGNED NOT NULL DEFAULT \'0\'', /* END 'viewable_thread_view' */
                'below_title_on_create' => 'TINYINT(4) UNSIGNED NOT NULL DEFAULT \'0\'', /* END 'below_title_on_create' */
                'search_advanced_thread_waindigo' => 'TINYINT(4) UNSIGNED NOT NULL DEFAULT \'1\'', /* END 'search_advanced_thread_waindigo' */
                'search_quick_forum_waindigo' => 'TINYINT(4) UNSIGNED NOT NULL DEFAULT \'0\'', /* END 'search_quick_forum_waindigo' */
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
            ), /* END 'xf_thread_field' */
            'xf_thread_field_group' => array(
                'field_group_id' => 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY', /* END 'field_group_id' */
                'display_order' => 'INT(10) UNSIGNED NOT NULL', /* END 'display_order' */
            ), /* END 'xf_thread_field_group' */
            'xf_thread_field_value' => array(
                'thread_id' => 'INT(10) UNSIGNED NOT NULL', /* END 'thread_id' */
                'field_id' => 'VARCHAR(64) NOT NULL', /* END 'field_id' */
                'field_value' => 'MEDIUMTEXT NOT NULL', /* END 'field_value' */
            ), /* END 'xf_thread_field_value' */
            'xf_forum_field' => array(
                'node_id' => 'INT(10) UNSIGNED NOT NULL', /* END 'node_id' */
                'field_id' => 'VARCHAR(64) NOT NULL', /* END 'field_id' */
                'field_value' => 'MEDIUMTEXT NOT NULL', /* END 'field_value' */
            ), /* END 'xf_forum_field' */
            'xf_resource_field' => array(
                'field_id' => 'VARBINARY(25) NOT NULL PRIMARY KEY', /* END 'field_id' */
                'display_group' => 'VARCHAR(25) NOT NULL DEFAULT \'above_info\'', /* END 'display_group' */
                'display_order' => 'INT UNSIGNED NOT NULL DEFAULT 1', /* END 'display_order' */
                'field_type' => 'VARCHAR(25) NOT NULL DEFAULT \'textbox\'', /* END 'field_type' */
                'field_choices' => 'BLOB NOT NULL', /* END 'field_choices' */
                'match_type' => 'VARCHAR(25) NOT NULL DEFAULT \'none\'', /* END 'match_type' */
                'match_regex' => 'VARCHAR(250) NOT NULL DEFAULT \'\'', /* END 'match_regex' */
                'match_callback_class' => 'VARCHAR(75) NOT NULL DEFAULT \'\'', /* END 'match_callback_class' */
                'match_callback_method' => 'VARCHAR(75) NOT NULL DEFAULT  \'\'', /* END 'match_callback_method' */
                'max_length' => 'INT UNSIGNED NOT NULL DEFAULT 0', /* END 'max_length' */
                'required' => 'TINYINT UNSIGNED NOT NULL DEFAULT 0', /* END 'display_template' */
                'display_template' => 'TEXT NOT NULL', /* END 'display_template' */
            ), /* END 'xf_resource_field' */
            'xf_resource_field_group' => array(
                'field_group_id' => 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY', /* END 'field_group_id' */
                'display_order' => 'INT(10) UNSIGNED NOT NULL', /* END 'display_order' */
            ), /* END 'xf_resource_field_group' */
            'xf_resource_field_value' => array(
                'resource_id' => 'INT(10) UNSIGNED NOT NULL', /* END 'resource_id' */
                'field_id' => 'VARCHAR(64) NOT NULL', /* END 'field_id' */
                'field_value' => 'MEDIUMTEXT NOT NULL', /* END 'field_value' */
            ), /* END 'xf_resource_field_value' */
            'xf_resource_field_category' => array(
                'field_id' => 'VARBINARY(25) NOT NULL', /* END 'field_id' */
                'resource_category_id' => 'INT NOT NULL', /* END 'resource_category_id' */
            ), /* END 'xf_resource_field_category' */
            'xf_social_forum_field' => array(
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
                'viewable_information' => 'TINYINT(4) UNSIGNED NOT NULL DEFAULT \'0\'', /* END 'viewable_information' */
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
            ), /* END 'xf_social_forum_field' */
            'xf_social_forum_field_group' => array(
                'field_group_id' => 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY', /* END 'field_group_id' */
                'display_order' => 'INT(10) UNSIGNED NOT NULL', /* END 'display_order' */
            ), /* END 'xf_social_forum_field_group' */
            'xf_social_forum_field_value' => array(
                'social_forum_id' => 'INT(10) UNSIGNED NOT NULL', /* END 'social_forum_id' */
                'field_id' => 'VARCHAR(64) NOT NULL', /* END 'field_id' */
                'field_value' => 'MEDIUMTEXT NOT NULL', /* END 'field_value' */
            ), /* END 'xf_social_forum_field_value' */
            'xf_social_category_field' => array(
                'node_id' => 'INT(10) UNSIGNED NOT NULL', /* END 'node_id' */
                'field_id' => 'VARCHAR(64) NOT NULL', /* END 'field_id' */
                'field_value' => 'MEDIUMTEXT NOT NULL', /* END 'field_value' */
            ), /* END 'xf_social_category_field' */
            'xf_article_field_value' => array(
                'article_id' => 'INT(10) UNSIGNED NOT NULL', /* END 'article_id' */
                'field_id' => 'VARCHAR(64) NOT NULL', /* END 'field_id' */
                'field_value' => 'MEDIUMTEXT NOT NULL', /* END 'field_value' */
            ), /* END 'xf_article_field_value' */
            'xf_article_page_field_value' => array(
                'article_page_id' => 'INT(10) UNSIGNED NOT NULL', /* END 'article_page_id' */
                'field_id' => 'VARCHAR(64) NOT NULL', /* END 'field_id' */
                'field_value' => 'MEDIUMTEXT NOT NULL', /* END 'field_value' */
            ), /* END 'xf_article_page_field_value' */
            'xf_custom_field_attachment' => array(
                'field_attachment_id' => 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY', /* END 'content_id' */
                'field_id' => 'VARCHAR(64) NOT NULL', /* END 'field_id' */
                'custom_field_type' => 'ENUM(\'user\',\'thread\',\'post\',\'resource\',\'social_forum\',\'article\',\'article_page\') NOT NULL DEFAULT \'user\'', /* END 'custom_field_type' */
                'content_id' => 'INT(10) UNSIGNED NOT NULL', /* END 'content_id' */
                'temp_hash' => 'VARCHAR(32) NOT NULL', /* END 'temp_hash' */
                'unassociated' => 'TINYINT(3) UNSIGNED NOT NULL DEFAULT 1', /* END 'unassociated' */
                'attach_count' => 'INT(10) UNSIGNED NOT NULL DEFAULT 0', /* END 'content_id' */
            ), /* END 'xf_custom_field_attachment' */
        );
    } /* END _getTables */

    protected function _getTableChanges()
    {
        return array(
            'xf_user_field' => array(
                'display_callback_class' => 'VARCHAR(75) NOT NULL DEFAULT \'\'', /* END 'display_callback_class' */
                'display_callback_method' => 'VARCHAR(75) NOT NULL DEFAULT  \'\'', /* END 'display_callback_method' */
                'addon_id' => 'VARCHAR(25) NOT NULL DEFAULT \'\'', /* END 'addon_id' */
                'field_choices_callback_class' => 'VARCHAR(75) NOT NULL DEFAULT \'\'', /* END 'field_choices_callback_class' */
                'field_choices_callback_method' => 'VARCHAR(75) NOT NULL DEFAULT \'\'', /* END 'field_choices_callback_method' */
                'field_callback_class' => 'VARCHAR(75) NOT NULL DEFAULT \'\'', /* END 'field_callback_class' */
                'field_callback_method' => 'VARCHAR(75) NOT NULL DEFAULT  \'\'', /* END 'field_callback_method' */
                'export_callback_class' => 'VARCHAR(75) NOT NULL DEFAULT \'\'', /* END 'export_callback_class' */
                'export_callback_method' => 'VARCHAR(75) NOT NULL DEFAULT  \'\'', /* END 'export_callback_method' */
                'search_advanced_user_waindigo' => 'TINYINT(4) UNSIGNED NOT NULL DEFAULT 1', /* END 'search_advanced_user_waindigo' */
            ), /* END 'xf_user_field' */
            'xf_forum' => array(
                'field_cache' => 'MEDIUMBLOB NULL COMMENT \'Serialized data from xf_forum_field, [group_id][field_id] => field_id\'', /* END 'field_cache' */
                'custom_fields' => 'MEDIUMBLOB NULL', /* END 'custom_fields' */
                'required_fields' => 'MEDIUMBLOB NULL', /* END 'required_fields' */
                'social_forum_field_cache' => 'MEDIUMBLOB NULL COMMENT \'Serialized data from xf_social_forum_field, [group_id][field_id] => field_id\'', /* END 'social_forum_field_cache' */
                'custom_social_forum_fields' => 'MEDIUMBLOB NULL', /* END 'custom_social_forum_fields' */
                'required_social_forum_fields' => 'MEDIUMBLOB NULL', /* END 'required_social_forum_fields' */
            ), /* END 'xf_forum' */
            'xf_thread' => array(
                'custom_fields' => 'MEDIUMBLOB NULL', /* END 'custom_fields' */
            ), /* END 'xf_thread' */
            'xf_resource_field' => array(
                'field_group_id' => 'INT(10) UNSIGNED NOT NULL DEFAULT 0', /* END 'field_group_id' */
                'materialized_order' => 'INT(10) UNSIGNED NOT NULL DEFAULT 0', /* END 'materialized_order' */
                'viewable_information' => 'TINYINT(4) UNSIGNED NOT NULL DEFAULT 0', /* END 'viewable_information' */
                'display_callback_class' => 'VARCHAR(75) NOT NULL DEFAULT \'\'', /* END 'display_callback_class' */
                'display_callback_method' => 'VARCHAR(75) NOT NULL DEFAULT  \'\'', /* END 'display_callback_method' */
                'allowed_user_group_ids' => 'BLOB', /* END 'allowed_user_group_ids' */
                'addon_id' => 'VARCHAR(25) NOT NULL DEFAULT \'\'', /* END 'addon_id' */
                'field_choices_callback_class' => 'VARCHAR(75) NOT NULL DEFAULT \'\'', /* END 'field_choices_callback_class' */
                'field_choices_callback_method' => 'VARCHAR(75) NOT NULL DEFAULT \'\'', /* END 'field_choices_callback_method' */
                'field_callback_class' => 'VARCHAR(75) NOT NULL DEFAULT \'\'', /* END 'field_callback_class' */
                'field_callback_method' => 'VARCHAR(75) NOT NULL DEFAULT  \'\'', /* END 'field_callback_method' */
                'export_callback_class' => 'VARCHAR(75) NOT NULL DEFAULT \'\'', /* END 'export_callback_class' */
                'export_callback_method' => 'VARCHAR(75) NOT NULL DEFAULT  \'\'', /* END 'export_callback_method' */
            ), /* END 'xf_resource_field' */
            'xf_resource_field_category' => array(
                'field_value' => 'MEDIUMTEXT NULL', /* END 'field_value' */
            ), /* END 'xf_resource_field_category' */
        );
    } /* END _getTableChanges */

    protected function _getAddOnTableChanges()
    {
        return array(
            'Waindigo_Library' => array(
                'xf_library' => array(
                    'field_cache' => 'MEDIUMBLOB NULL COMMENT \'Serialized data from xf_forum_field, [group_id][field_id] => field_id\'', /* END 'field_cache' */
                    'custom_fields' => 'MEDIUMBLOB NULL', /* END 'custom_fields' */
                    'required_fields' => 'MEDIUMBLOB NULL', /* END 'required_fields' */
                ), /* END 'xf_library' */
                'xf_article' => array(
                    'custom_fields' => 'MEDIUMBLOB NULL', /* END 'custom_fields' */
                ), /* END 'xf_article' */
            ),
            'Waindigo_SocialGroups' => array(
                'xf_social_forum' => array(
                    'custom_social_forum_fields' => 'MEDIUMBLOB NULL', /* END 'custom_social_forum_fields' */
                ), /* END 'xf_social_forum' */
            ),
            'XenResource' => array(
                'xf_resource_category' => array(
                    'field_cache' => 'MEDIUMBLOB NULL', /* END 'field_cache' */
                    'prefix_cache' => 'MEDIUMBLOB NULL COMMENT \'Serialized data from xf_resource_category_prefix, [group_id][prefix_id] => prefix_id\'', /* END 'prefix_cache' */
                    'require_prefix' => 'TINYINT UNSIGNED NOT NULL DEFAULT 0', /* END 'require_prefix' */
                    'featured_count' => 'SMALLINT UNSIGNED NOT NULL DEFAULT 0', /* END 'featured_count' */
                    'category_resource_fields' => 'MEDIUMBLOB NULL', /* END 'category_resource_fields' */
                    'required_fields' => 'MEDIUMBLOB NULL', /* END 'required_fields' */
                ), /* END 'xf_resource_category' */
                'xf_resource' => array(
                    'custom_resource_fields' => 'MEDIUMBLOB NULL', /* END 'custom_resource_fields' */
                    'prefix_id' => 'INT UNSIGNED NOT NULL DEFAULT 0', /* END 'prefix_id' */
                    'icon_date' => 'INT UNSIGNED NOT NULL DEFAULT 0', /* END 'icon_date' */
                ), /* END 'xf_resource' */
            )
        );
    } /* END _getAddOnTableChanges */

    protected function _getPrimaryKeys()
    {
        return array(
            'xf_thread_field_value' => array(
                'thread_id',
                'field_id'
            ), /* END 'xf_thread_field_value' */
            'xf_forum_field' => array(
                'node_id',
                'field_id'
            ), /* END 'xf_forum_field' */
            'xf_article_field_value' => array(
                'article_id',
                'field_id'
            ), /* END 'xf_article_field_value' */
            'xf_article_page_field_value' => array(
                'article_page_id',
                'field_id'
            ), /* END 'xf_article_page_field_value' */
            'xf_resource_field_value' => array(
                'resource_id',
                'field_id'
            ), /* END 'xf_resource_field_value' */
            'xf_social_forum_field_value' => array(
                'social_forum_id',
                'field_id'
            ), /* END 'xf_social_forum_field_value' */
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
            'xf_forum_field' => array(
                'field_id' => array(
                    'field_id'
                ), /* END 'field_id' */
            ), /* END 'xf_forum_field' */
            'xf_article_field_value' => array(
                'field_id' => array(
                    'field_id'
                ), /* END 'field_id' */
            ), /* END 'xf_article_field_value' */
            'xf_article_page_field_value' => array(
                'field_id' => array(
                    'field_id'
                ), /* END 'field_id' */
            ), /* END 'xf_article_page_field_value' */
            'xf_resource_field' => array(
                'materialized_order' => array(
                    'materialized_order'
                ), /* END 'materialized_order' */
                'display_group_order' => array(
                    'display_group',
                    'display_order'
                ), /* END 'display_group_order' */
            ), /* END 'xf_resource_field' */
            'xf_resource_field_value' => array(
                'field_id' => array(
                    'field_id'
                ), /* END 'field_id' */
            ), /* END 'xf_resource_field_value' */
            'xf_social_forum_field' => array(
                'materialized_order' => array(
                    'materialized_order'
                ), /* END 'materialized_order' */
            ), /* END 'xf_social_forum_field' */
            'xf_social_forum_field_value' => array(
                'field_id' => array(
                    'field_id'
                ), /* END 'field_id' */
            ), /* END 'xf_social_forum_field_value' */
        );
    } /* END _getKeys */

    protected function _getEnumValues()
    {
        return array(
            'xf_user_field' => array(
                'field_type' => array(
                    'add' => array(
                        'callback'
                    ), /* END 'add' */
                ), /* END 'field_type' */
            ), /* END 'xf_user_field' */
        );
    } /* END _getEnumValues */

    /**
     * Gets the content types to be created for this add-on.
     * See parent for explanation.
     *
     * @return array Format: [content type] => array(addon id, fields =>
     * array([field_name] => [field_value])
     */
    protected function _getContentTypes()
    {
        return array(
            'custom_field' => array(
                'addon_id' => 'Waindigo_CustomFields', /* END 'addon_id' */
                'fields' => array(
                    'attachment_handler_class' => 'Waindigo_CustomFields_AttachmentHandler_CustomField', /* END 'attachment_handler_class' */
                ), /* END 'fields' */
            ), /* END 'custom_field' */
        );
    } /* END _getContentTypes */

    protected function _preUninstall()
    {
        $this->_db->delete('xf_user_field', "field_type = 'callback'");
    } /* END _preUninstall */
}