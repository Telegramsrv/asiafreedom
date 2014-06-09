<?php

/**
 *
 * @see Waindigo_Install
 */
class Waindigo_UserFieldCats_Install_Controller extends Waindigo_Install
{

    protected $_resourceManagerUrl = 'http://xenforo.com/community/resources/user-field-categories-by-waindigo.2343/';

    protected function _getTables()
    {
        return array(
            'xf_user_field_category' => array(
                'user_field_category_id' => 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY', /* END 'user_field_category_id' */
                'title' => 'VARCHAR(255) NOT NULL', /* END 'title' */
                'user_group_ids' => 'VARBINARY(255) NOT NULL DEFAULT \'\'', /* END 'user_group_ids' */
            ), /* END 'xf_user_field_category' */
        );
    } /* END _getTables */

    protected function _getTableChanges()
    {
        return array(
            'xf_user_field' => array(
                'user_field_category_id' => 'INT(10) UNSIGNED NOT NULL DEFAULT 0', /* END 'user_field_category_id' */
            ), /* END 'xf_user_field' */
        );
    } /* END _getTableChanges */

    protected function _getEnumValues()
    {
        return array(
            'xf_user_field' => array(
                'display_group' => array(
                    'add' => array(
                        'custom'
                    ), /* END 'add' */
                ), /* END 'display_group' */
            ), /* END 'xf_user_field' */
        );
    } /* END _getEnumValues */
}