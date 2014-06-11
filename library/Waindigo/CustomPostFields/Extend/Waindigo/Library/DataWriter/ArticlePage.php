<?php

/**
 *
 * @see Waindigo_Library_DataWriter_ArticlePage
 */
class Waindigo_CustomPostFields_Extend_Waindigo_Library_DataWriter_ArticlePage extends XFCP_Waindigo_CustomPostFields_Extend_Waindigo_Library_DataWriter_ArticlePage
{

    const DATA_POST_FIELD_DEFINITIONS = 'postFields';

    /**
     * The custom fields to be updated.
     * Use setCustomPostFields to manage this.
     *
     * @var array
     */
    protected $_updateCustomPostFields = array();

    /**
     *
     * @see Waindigo_Library_DataWriter_ArticlePage::_getFields()
    */
    protected function _getFields()
    {
        $fields = parent::_getFields();

        $fields['xf_article_page']['custom_post_fields'] = array(
            'type' => self::TYPE_SERIALIZED,
            'default' => ''
        );

        return $fields;
    } /* END _getFields */

    /**
     *
     * @see Waindigo_Library_DataWriter_ArticlePage::_messagePreSave()
     */
    protected function _messagePreSave()
    {
        if (!$node = $this->getExtraData(self::DATA_LIBRARY)) {
            $node = $this->getModelFromCache('Waindigo_Library_Model_Library')->getLibraryByArticleId(
                $this->get('article_id'));

            if ($node) {
                $this->setExtraData(self::DATA_LIBRARY, $node);
            }
        }

        if (isset($GLOBALS['Waindigo_Library_ControllerPublic_Article']) ||
        isset($GLOBALS['Waindigo_Library_ControllerPublic_Library'])) {
            if (isset($GLOBALS['Waindigo_Library_ControllerPublic_Article'])) {
                /* @var $controller Waindigo_Library_ControllerPublic_Article */
                $controller = $GLOBALS['Waindigo_Library_ControllerPublic_Article'];
            } else
            if (isset($GLOBALS['Waindigo_Library_ControllerPublic_Library'])) {
                /* @var $controller Waindigo_Library_ControllerPublic_Library */
                $controller = $GLOBALS['Waindigo_Library_ControllerPublic_Library'];
            }

            $fieldValues = array();
            if (isset($node['custom_post_fields']) && $node['custom_post_fields']) {
                $fieldValues = unserialize($node['custom_post_fields']);
            }

            $customPostFields = $controller->getInput()->filterSingle('custom_post_fields', XenForo_Input::ARRAY_SIMPLE);
            $customPostFieldsShown = $controller->getInput()->filterSingle('custom_post_fields_shown',
                XenForo_Input::STRING, array(
                    'array' => true
                ));

            foreach ($fieldValues as $fieldName => $fieldValue) {
                if (!in_array($fieldName, $customPostFieldsShown)) {
                    $customPostFieldsShown[] = $fieldName;
                    $customPostFields[$fieldName] = $fieldValue;
                }
            }

            $this->setCustomPostFields($customPostFields, $customPostFieldsShown);
        }

        if (isset($GLOBALS['Waindigo_Library_ControllerPublic_ArticlePage'])) {
            /* @var $controller Waindigo_Library_ControllerPublic_ArticlePage */
            $controller = $GLOBALS['Waindigo_Library_ControllerPublic_ArticlePage'];

            $customPostFields = $controller->getInput()->filterSingle('custom_post_fields', XenForo_Input::ARRAY_SIMPLE);
            $customPostFieldsShown = $controller->getInput()->filterSingle('custom_post_fields_shown',
                XenForo_Input::STRING, array(
                    'array' => true
                ));

            $this->setCustomPostFields($customPostFields, $customPostFieldsShown);
        }

        parent::_messagePreSave();
    } /* END _messagePreSave */

    /**
     *
     * @see Waindigo_Library_DataWriter_ArticlePage::_messagePostSave()
     */
    protected function _messagePostSave()
    {
        $this->updateCustomPostFields();

        $this->_associateCustomFieldsAttachments();

        parent::_messagePostSave();
    } /* END _messagePostSave */

    /**
     *
     * @param array $fieldValues
     * @param array $fieldsShown
     */
    public function setCustomPostFields(array $fieldValues, array $fieldsShown = null)
    {
        if (!$node = $this->getExtraData(self::DATA_LIBRARY)) {
            $node = $this->getModelFromCache('Waindigo_Library_Model_Library')->getLibraryByArticleId(
                $this->get('article_id'));

            $this->setExtraData(self::DATA_LIBRARY, $node);
        }

        $nodeRequiredPostFields = array();
        if (isset($node['required_post_fields']) && $node['required_post_fields']) {
            $nodeRequiredPostFields = unserialize($node['required_post_fields']);
        }

        if ($fieldsShown === null) {
            // not passed - assume keys are all there
            $fieldsShown = array_keys($fieldValues);
        }

        $fieldModel = $this->_getFieldModel();
        $fields = $this->_getPostFieldDefinitions();
        $callbacks = array();

        if ($this->get('article_page_id') && !$this->_importMode) {
            $existingValues = $fieldModel->getArticlePageFieldValues($this->get('article_page_id'));
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

                if (in_array($fieldId, $nodeRequiredPostFields) && ($value === '' || $value === array())) {
                    $this->error(new XenForo_Phrase('please_enter_value_for_all_required_fields'), "required");
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
            $articlePageId = $this->get('article_page_id');

            foreach ($this->_updateCustomPostFields as $fieldId => $value) {
                if (is_array($value)) {
                    $value = serialize($value);
                }
                $this->_db->query(
                    '
                        INSERT INTO xf_article_page_field_value
                        (article_page_id, field_id, field_value)
                        VALUES
                        (?, ?, ?)
                        ON DUPLICATE KEY UPDATE
                        field_value = VALUES(field_value)
                    ',
                    array(
                        $articlePageId,
                        $fieldId,
                        $value
                    ));
            }
        }
    } /* END updateCustomPostFields */

    protected function _associateCustomFieldsAttachments()
    {
        $fieldAttachmentModel = $this->getModelFromCache('Waindigo_CustomFields_Model_Attachment');

        $fieldAttachmentModel->associateAttachments($this->get('article_page_id'), 'article_page');
    } /* END _associateCustomFieldsAttachments */

    /**
     * Fetch (and cache) post field definitions
     *
     * @return array
     */
    protected function _getPostFieldDefinitions()
    {
        $fields = $this->getExtraData(self::DATA_POST_FIELD_DEFINITIONS);

        if (is_null($fields)) {
            $fields = $this->_getFieldModel()->getPostFields();

            $this->setExtraData(self::DATA_POST_FIELD_DEFINITIONS, $fields);
        }

        return $fields;
    } /* END _getPostFieldDefinitions */

    /**
     *
     * @see XenForo_DataWriter_DiscussionMessage_Post::_messagePostDelete()
     */
    protected function _messagePostDelete()
    {
        parent::_messagePostDelete();

        $db = $this->_db;
        $postId = $this->get('article_page_id');
        $postIdQuoted = $db->quote($postId);

        $db->delete('xf_article_page_field_value', "article_page_id = $postIdQuoted");
    } /* END _messagePostDelete */

    /**
     *
     * @return XenForo_Model_Thread
     */
    protected function _getThreadModel()
    {
        return $this->getModelFromCache('XenForo_Model_Thread');
    } /* END _getThreadModel */

    /**
     *
     * @return Waindigo_CustomPostFields_Model_PostField
     */
    protected function _getFieldModel()
    {
        return $this->getModelFromCache('Waindigo_CustomPostFields_Model_PostField');
    } /* END _getFieldModel */
}