<?php

/**
 *
 * @see Waindigo_Library_Search_DataHandler_ArticlePage
 */
class Waindigo_CustomFields_Extend_Waindigo_Library_Search_DataHandler_ArticlePage extends XFCP_Waindigo_CustomFields_Extend_Waindigo_Library_Search_DataHandler_ArticlePage
{

    /**
     *
     * @var Waindigo_CustomFields_Model_ThreadField
     */
    protected $_threadFieldModel = null;

    /**
     *
     * @see XenForo_Search_DataHandler_Post::getTypeConstraintsFromInput()
     */
    public function getTypeConstraintsFromInput(XenForo_Input $input)
    {
        $constraints = parent::getTypeConstraintsFromInput($input);

        $threadFields = $input->filterSingle('custom_fields', XenForo_Input::ARRAY_SIMPLE);
        if ($threadFields) {
            foreach ($threadFields as $fieldId => $fieldValue) {
                $constraints['article_field_id_' . $fieldId] = $fieldId;
                $constraints['article_field_value_' . $fieldId] = $fieldValue;
            }
        }

        return $constraints;
    } /* END getTypeConstraintsFromInput */

    /**
     *
     * @see XenForo_Search_DataHandler_Post::processConstraint()
     */
    public function processConstraint(XenForo_Search_SourceHandler_Abstract $sourceHandler, $constraint, $constraintInfo,
        array $constraints)
    {
        if (strlen($constraint) > strlen('article_field_id_') &&
        substr($constraint, 0, strlen('article_field_id_')) == 'article_field_id_') {
            if ($constraintInfo) {
                $constraintInfo = strval($constraintInfo);
                return array(
                    'query' => array(
                        'article_field_value_' . $constraintInfo,
                        'field_id',
                        '=',
                        $constraintInfo
                    )
                );
            }
        }
        if (strlen($constraint) > strlen('article_field_value_') &&
        substr($constraint, 0, strlen('article_field_value_')) == 'article_field_value_') {
            if ($constraintInfo) {
                if (is_array($constraintInfo)) {
                    $constraintInfo = serialize($constraintInfo);
                } else {
                    $constraintInfo = strval($constraintInfo);
                }
                return array(
                    'query' => array(
                        strval($constraint),
                        'field_value',
                        '=',
                        $constraintInfo
                    )
                );
            }
        }

        return parent::processConstraint($sourceHandler, $constraint, $constraintInfo, $constraints);
    } /* END processConstraint */

    /**
     * Gets the search form controller response for this type.
     *
     * @see XenForo_Search_DataHandler_Abstract::getSearchFormControllerResponse()
     */
    public function getSearchFormControllerResponse(XenForo_ControllerPublic_Abstract $controller, XenForo_Input $input,
        array $viewParams)
    {
        $response = parent::getSearchFormControllerResponse($controller, $input, $viewParams);

        if ($response instanceof XenForo_ControllerResponse_View) {
            $threadFieldModel = $this->_getThreadFieldModel();

            $verifyUsability = XenForo_Application::get('options')->waindigo_showSearchUsableOnly_customFields;
            $threadFields = $threadFieldModel->getUsableThreadFields(null, $verifyUsability);

            foreach ($threadFields as $groupId => $group) {
                foreach ($group['fields'] as $threadFieldId => $threadField) {
                    if (empty($threadField['search_advanced_article_waindigo'])) {
                        unset($threadFields[$groupId]['fields'][$threadFieldId]);
                    }
                }
                if (empty($threadFields[$groupId]['fields'])) {
                    unset($threadFields[$groupId]);
                }
            }

            $response->params['search']['customArticleFields'] = $threadFieldModel->prepareGroupedThreadFields(
                $threadFields, true);
        }

        return $response;
    } /* END getSearchFormControllerResponse */

    /**
     *
     * @see XenForo_Search_DataHandler_Post::getJoinStructures()
     */
    public function getJoinStructures(array $tables)
    {
        $structures = parent::getJoinStructures($tables);

        foreach ($tables as $tableName => $table) {
            if (strlen($tableName) > strlen('article_field_value_') &&
            substr($tableName, 0, strlen('article_field_value_')) == 'article_field_value_') {
                $structures[$tableName] = array(
                    'table' => 'xf_article_field_value',
                    'key' => 'article_id',
                    'relationship' => array(
                        'search_index',
                        'discussion_id'
                    )
                );
            }
        }

        return $structures;
    } /* END getJoinStructures */

    /**
     *
     * @return Waindigo_CustomFields_Model_ThreadField
     */
    protected function _getThreadFieldModel()
    {
        if (!$this->_threadFieldModel) {
            $this->_threadFieldModel = XenForo_Model::create('Waindigo_CustomFields_Model_ThreadField');
        }

        return $this->_threadFieldModel;
    } /* END _getThreadFieldModel */
}