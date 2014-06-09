<?php

/**
 *
 * @see XenForo_DataWriter_Forum
 */
class Waindigo_CustomPostFields_Extend_XenForo_DataWriter_Forum extends XFCP_Waindigo_CustomPostFields_Extend_XenForo_DataWriter_Forum
{

    const DATA_POST_FIELD_DEFINITIONS = 'postFields';

    /**
     * The custom fields to be updated.
     * Use setCustomPostFields to manage these.
     *
     * @var array
     */
    protected $_updateCustomPostFields = array();

    /**
     *
     * @see XenForo_DataWriter_Forum::_getFields()
     */
    protected function _getFields()
    {
        $fields = parent::_getFields();

        $fields['xf_forum']['custom_post_fields'] = array(
            'type' => self::TYPE_SERIALIZED,
            'default' => ''
        );
        $fields['xf_forum']['required_post_fields'] = array(
            'type' => self::TYPE_SERIALIZED,
            'default' => ''
        );

        return $fields;
    } /* END _getFields */

    /**
     *
     * @see XenForo_DataWriter_Forum::_preSave()
     */
    protected function _preSave()
    {
        if (isset($GLOBALS['XenForo_ControllerAdmin_Forum'])) {
            /* @var $controller XenForo_ControllerAdmin_Forum */
            $controller = $GLOBALS['XenForo_ControllerAdmin_Forum'];

            $customPostFields = $controller->getInput()->filterSingle('custom_post_fields', XenForo_Input::ARRAY_SIMPLE);
            $customPostFieldsShown = $controller->getInput()->filterSingle('custom_post_fields_shown',
                XenForo_Input::STRING, array(
                    'array' => true
                ));
            $this->setCustomPostFields($customPostFields, $customPostFieldsShown);

            $requiredPostFields = $controller->getInput()->filterSingle('required_post_fields',
                XenForo_Input::ARRAY_SIMPLE);
            $this->set('required_post_fields', serialize($requiredPostFields));
        }

        parent::_preSave();
    } /* END _preSave */

    /**
     *
     * @see XenForo_DataWriter_Forum::_postSave()
     */
    protected function _postSave()
    {
        if (isset($GLOBALS['XenForo_ControllerAdmin_Forum'])) {
            /* @var $controller XenForo_ControllerAdmin_Forum */
            $controller = $GLOBALS['XenForo_ControllerAdmin_Forum'];

            $fieldIds = $controller->getInput()->filterSingle('available_post_fields', XenForo_Input::STRING,
                array(
                    'array' => true
                ));
            $this->_getPostFieldModel()->updatePostFieldForumAssociationByForum($this->get('node_id'), $fieldIds);

            $templates = $controller->getInput()->filter(
                array(
                    'post_header' => XenForo_Input::STRING,
                    'post_footer' => XenForo_Input::STRING
                ));

            $headerName = '_header_post_node.' . $this->get('node_id');
            $footerName = '_footer_post_node.' . $this->get('node_id');

            $oldTemplates = $this->_getTemplateModel()->getTemplatesInStyleByTitles(
                array(
                    $headerName,
                    $footerName
                ));

            /* @var $templateWriter XenForo_DataWriter_Template */
            $templateWriter = XenForo_DataWriter::create('XenForo_DataWriter_Template');
            if (isset($oldTemplates[$headerName])) {
                $templateWriter->setExistingData($oldTemplates[$headerName]);
            }
            $templateWriter->set('title', $headerName);
            $templateWriter->set('style_id', 0);
            $templateWriter->set('template', $templates['post_header']);
            $templateWriter->save();

            /* @var $templateWriter XenForo_DataWriter_Template */
            $templateWriter = XenForo_DataWriter::create('XenForo_DataWriter_Template');
            if (isset($oldTemplates[$footerName])) {
                $templateWriter->setExistingData($oldTemplates[$footerName]);
            }
            $templateWriter->set('title', $footerName);
            $templateWriter->set('style_id', 0);
            $templateWriter->set('template', $templates['post_footer']);
            $templateWriter->save();

            $this->_updateCustomPostFields = unserialize($this->get('custom_post_fields'));
        }

        $this->updateCustomPostFields();

        parent::_postSave();
    } /* END _postSave */

    /**
     *
     * @param array $fieldValues
     * @param array $fieldsShown
     */
    public function setCustomPostFields(array $fieldValues, array $fieldsShown = null)
    {
        if ($fieldsShown === null) {
            // not passed - assume keys are all there
            $fieldsShown = array_keys($fieldValues);
        }

        $fieldModel = $this->_getPostFieldModel();
        $fields = $this->_getPostFieldDefinitions();
        $callbacks = array();

        if ($this->get('node_id') && !$this->_importMode) {
            $existingValues = $fieldModel->getDefaultPostFieldValues($this->get('node_id'));
        } else {
            $existingValues = array();
        }

        $finalValues = array();

        foreach ($fieldsShown as $fieldId) {
            if (!isset($fields[$fieldId])) {
                continue;
            }

            $field = $fields[$fieldId];
            if ($field['field_type'] == 'callback') {
                if (isset($fieldValues[$fieldId])) {
                    if (is_array($fieldValues[$fieldId])) {
                        $fieldValues[$fieldId] = serialize($fieldValues[$fieldId]);
                        $callbacks[] = $fieldId;
                    }
                }
                $field['field_type'] = 'textbox';
            }
            $multiChoice = ($field['field_type'] == 'checkbox' || $field['field_type'] == 'multiselect');

            if ($multiChoice) {
                // multi selection - array
                $value = (isset($fieldValues[$fieldId]) && is_array($fieldValues[$fieldId])) ? $fieldValues[$fieldId] : array();
            } else {
                // single selection - string
                $value = (isset($fieldValues[$fieldId]) ? strval($fieldValues[$fieldId]) : '');
            }

            $existingValue = (isset($existingValues[$fieldId]) ? $existingValues[$fieldId] : null);

            if (!$this->_importMode) {
                $error = '';
                $valid = $fieldModel->verifyPostFieldValue($field, $value, $error);
                if (!$valid) {
                    $this->error($error, "custom_post_field_$fieldId");
                    continue;
                }
            }

            foreach ($callbacks as $callbackFieldId) {
                if (isset($fieldValues[$callbackFieldId])) {
                    if (is_array($fieldValues[$callbackFieldId])) {
                        $value = unserialize($value);
                    }
                }
            }

            if ($value !== $existingValue) {
                $finalValues[$fieldId] = $value;
            }
        }

        $this->_updateCustomPostFields = $finalValues + $this->_updateCustomPostFields;
        $this->set('custom_post_fields', $finalValues + $existingValues);
    } /* END setCustomPostFields */

    public function updateCustomPostFields()
    {
        if ($this->_updateCustomPostFields) {
            $nodeId = $this->get('node_id');

            foreach ($this->_updateCustomPostFields as $fieldId => $value) {
                if (is_array($value)) {
                    $value = serialize($value);
                }
                $this->_db->update('xf_forum_post_field',
                    array(
                        'field_value' => $value
                    ), 'node_id = ' . $this->_db->quote($nodeId) . ' AND field_id = ' . $this->_db->quote($fieldId));
            }
        }
    } /* END updateCustomPostFields */

    /**
     * Fetch (and cache) post field definitions
     *
     * @return array
     */
    protected function _getPostFieldDefinitions()
    {
        $fields = $this->getExtraData(self::DATA_POST_FIELD_DEFINITIONS);

        if (is_null($fields)) {
            $fields = $this->_getPostFieldModel()->getPostFields();

            $this->setExtraData(self::DATA_POST_FIELD_DEFINITIONS, $fields);
        }

        return $fields;
    } /* END _getPostFieldDefinitions */

    /**
     *
     * @return Waindigo_CustomPostFields_Model_PostField
     */
    protected function _getPostFieldModel()
    {
        return $this->getModelFromCache('Waindigo_CustomPostFields_Model_PostField');
    } /* END _getPostFieldModel */
}