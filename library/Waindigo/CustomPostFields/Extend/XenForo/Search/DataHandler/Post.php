<?php

/**
 *
 * @see XenForo_Search_DataHandler_Post
 */
class Waindigo_CustomPostFields_Extend_XenForo_Search_DataHandler_Post extends XFCP_Waindigo_CustomPostFields_Extend_XenForo_Search_DataHandler_Post
{

    /**
     *
     * @var Waindigo_CustomPostFields_Model_PostField
     */
    protected $_postFieldModel = null;

    /**
     *
     * @see XenForo_Search_DataHandler_Post::getTypeConstraintsFromInput()
     */
    public function getTypeConstraintsFromInput(XenForo_Input $input)
    {
        $constraints = parent::getTypeConstraintsFromInput($input);

        $postFields = $input->filterSingle('custom_post_fields', XenForo_Input::ARRAY_SIMPLE);
        if ($postFields) {
            foreach ($postFields as $fieldId => $fieldValue) {
                if ($fieldValue) {
                    $constraints['post_field_id_' . $fieldId] = $fieldId;
                    $constraints['post_field_value_' . $fieldId] = $fieldValue;
                }
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
        if (strlen($constraint) > strlen('post_field_id_') &&
             substr($constraint, 0, strlen('post_field_id_')) == 'post_field_id_') {
            if ($constraintInfo) {
                $constraintInfo = strval($constraintInfo);
                return array(
                    'query' => array(
                        'post_field_value_' . $constraintInfo,
                        'field_id',
                        '=',
                        $constraintInfo
                    )
                );
            }
        }
        if (strlen($constraint) > strlen('post_field_value_') &&
             substr($constraint, 0, strlen('post_field_value_')) == 'post_field_value_') {
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
            $postFieldModel = $this->_getPostFieldModel();

            $postFields = $postFieldModel->getUsablePostFields();

            $response->params['search']['customPostFields'] = $postFieldModel->prepareGroupedPostFields($postFields,
                true);
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
            if (strlen($tableName) > strlen('post_field_value_') &&
                 substr($tableName, 0, strlen('post_field_value_')) == 'post_field_value_') {
                $structures[$tableName] = array(
                    'table' => 'xf_post_field_value',
                    'key' => 'post_id',
                    'relationship' => array(
                        'search_index',
                        'content_id'
                    )
                );
            }
        }

        return $structures;
    } /* END getJoinStructures */

    /**
     *
     * @return Waindigo_CustomPostFields_Model_PostField
     */
    protected function _getPostFieldModel()
    {
        if (!$this->_postFieldModel) {
            $this->_postFieldModel = XenForo_Model::create('Waindigo_CustomPostFields_Model_PostField');
        }

        return $this->_postFieldModel;
    } /* END _getPostFieldModel */
}