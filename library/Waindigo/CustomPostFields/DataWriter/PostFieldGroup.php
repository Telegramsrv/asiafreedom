<?php

/**
* Data writer for post field groups.
*/
class Waindigo_CustomPostFields_DataWriter_PostFieldGroup extends XenForo_DataWriter
{

    /**
     * Constant for extra data that holds the value for the phrase
     * that is the title of this field.
     *
     * This value is required on inserts.
     *
     * @var string
     */
    const DATA_TITLE = 'phraseTitle';

    /**
     * Title of the phrase that will be created when a call to set the
     * existing data fails (when the data doesn't exist).
     *
     * @var string
     */
    protected $_existingDataErrorPhrase = 'requested_field_group_not_found';

    /**
     * Gets the fields that are defined for the table.
     * See parent for explanation.
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            'xf_post_field_group' => array(
                'field_group_id' => array(
                    'type' => self::TYPE_UINT,
                    'autoIncrement' => true
                ), /* END 'field_group_id' */
                'display_order' => array(
                    'type' => self::TYPE_UINT_FORCED,
                    'default' => 0
                ), /* END 'display_order' */
            ), /* END 'xf_post_field_group' */
        );
    } /* END _getFields */

    /**
     * Gets the actual existing data out of data that was passed in.
     * See parent for explanation.
     *
     * @param mixed
     *
     * @return array false
     */
    protected function _getExistingData($data)
    {
        if (!$id = $this->_getExistingPrimaryKey($data, 'field_group_id')) {
            return false;
        }

        return array(
            'xf_post_field_group' => $this->_getFieldModel()->getPostFieldGroupById($id)
        );
    } /* END _getExistingData */

    /**
     * Gets SQL condition to update the existing record.
     *
     * @return string
     */
    protected function _getUpdateCondition($tableName)
    {
        return 'field_group_id = ' . $this->_db->quote($this->getExisting('field_group_id'));
    } /* END _getUpdateCondition */


    protected function _preSave()
    {
        $titlePhrase = $this->getExtraData(self::DATA_TITLE);
        if ($titlePhrase !== null && strlen($titlePhrase) == 0) {
            $this->error(new XenForo_Phrase('please_enter_valid_title'), 'title');
        }
    } /* END _preSave */

    protected function _postSave()
    {
        $titlePhrase = $this->getExtraData(self::DATA_TITLE);
        if ($titlePhrase !== null) {
            $this->_insertOrUpdateMasterPhrase($this->_getTitlePhraseName($this->get('field_group_id')), $titlePhrase,
                '', array(
                    'global_cache' => 1
                ));
        }

        if ($this->isChanged('display_order')) {
            $this->_getFieldModel()->rebuildPostFieldMaterializedOrder();
        }

        $this->_getFieldModel()->rebuildPostFieldCache();
    } /* END _postSave */

    protected function _postDelete()
    {
        $fieldGroupId = $this->get('field_group_id');

        $this->_deleteMasterPhrase($this->_getTitlePhraseName($fieldGroupId));

        $this->_db->update('xf_post_field', array(
            'field_group_id' => 0
        ), 'field_group_id = ' . $this->_db->quote($fieldGroupId));

        $this->_getFieldModel()->rebuildPostFieldMaterializedOrder();
        $this->_getFieldModel()->rebuildPostFieldCache();
    } /* END _postDelete */

    /**
     * Gets the name of the title phrase for this field.
     *
     * @param integer $fieldId
     *
     * @return string
     */
    protected function _getTitlePhraseName($fieldGroupId)
    {
        return $this->_getFieldModel()->getPostFieldGroupTitlePhraseName($fieldGroupId);
    } /* END _getTitlePhraseName */

    /**
     *
     * @return Waindigo_CustomPostFields_Model_PostField
     */
    protected function _getFieldModel()
    {
        return $this->getModelFromCache('Waindigo_CustomPostFields_Model_PostField');
    } /* END _getFieldModel */
}