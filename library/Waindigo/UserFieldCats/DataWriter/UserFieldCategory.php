<?php

/**
 * Data writer for user field categories.
 */
class Waindigo_UserFieldCats_DataWriter_UserFieldCategory extends XenForo_DataWriter
{
    /**
     * Title of the phrase that will be created when a call to set the
     * existing data fails (when the data doesn't exist).
     *
     * @var string
     */
    protected $_existingDataErrorPhrase = 'waindigo_requested_user_field_category_not_found_userfieldcats';

    /**
     * Gets the fields that are defined for the table. See parent for explanation.
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            'xf_user_field_category' => array(
                'user_field_category_id' => array('type' => self::TYPE_UINT, 'autoIncrement' => true), /* END 'user_field_category_id' */
                'title' => array('type' => self::TYPE_STRING, 'required' => true), /* END 'title' */
                'user_group_ids' => array('type' => self::TYPE_UNKNOWN, 'default' => ''), /* END 'user_group_ids' */
            ), /* END 'xf_user_field_category' */
        );
    } /* END _getFields */

    /**
     * Gets the actual existing data out of data that was passed in. See parent for explanation.
     *
     * @param mixed
     *
     * @return array|false
     */
    protected function _getExistingData($data)
    {
        if (!$userFieldCategoryId = $this->_getExistingPrimaryKey($data, 'user_field_category_id')) {
            return false;
        }

        $userFieldCategory = $this->_getUserFieldCategoryModel()->getUserFieldCategoryById($userFieldCategoryId);
        if (!$userFieldCategory) {
            return false;
        }

        return $this->getTablesDataFromArray($userFieldCategory);
    } /* END _getExistingData */

    /**
     * Gets SQL condition to update the existing record.
     *
     * @return string
     */
    protected function _getUpdateCondition($tableName)
    {
        return 'user_field_category_id = ' . $this->_db->quote($this->getExisting('user_field_category_id'));
    } /* END _getUpdateCondition */

    /**
     * Get the user field categories model.
     *
     * @return Waindigo_UserFieldCats_Model_UserFieldCategory
     */
    protected function _getUserFieldCategoryModel()
    {
        return $this->getModelFromCache('Waindigo_UserFieldCats_Model_UserFieldCategory');
    } /* END _getUserFieldCategoryModel */
}