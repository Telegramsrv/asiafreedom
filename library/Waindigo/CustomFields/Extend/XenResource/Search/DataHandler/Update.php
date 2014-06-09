<?php

/**
 *
 * @see XenResource_Search_DataHandler_Update
 */
class Waindigo_CustomFields_Extend_XenResource_Search_DataHandler_Update extends XFCP_Waindigo_CustomFields_Extend_XenResource_Search_DataHandler_Update
{

    /**
     *
     * @var XenResource_Model_ResourceField
     */
    protected $_resourceFieldModel = null;

    /**
     *
     * @see XenForo_Search_DataHandler_Post::getTypeConstraintsFromInput()
     */
    public function getTypeConstraintsFromInput(XenForo_Input $input)
    {
        $constraints = parent::getTypeConstraintsFromInput($input);

        $resourceFields = $input->filterSingle('custom_fields', XenForo_Input::ARRAY_SIMPLE);
        if ($resourceFields) {
            foreach ($resourceFields as $fieldId => $fieldValue) {
                if ($fieldValue) {
                    $constraints['resource_field_id_' . $fieldId] = $fieldId;
                    $constraints['resource_field_value_' . $fieldId] = $fieldValue;
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
        if (strlen($constraint) > strlen('resource_field_id_') &&
             substr($constraint, 0, strlen('resource_field_id_')) == 'resource_field_id_') {
            if ($constraintInfo) {
                $constraintInfo = strval($constraintInfo);
                return array(
                    'query' => array(
                        'resource_field_value_' . $constraintInfo,
                        'field_id',
                        '=',
                        $constraintInfo
                    )
                );
            }
        }
        if (strlen($constraint) > strlen('resource_field_value_') &&
             substr($constraint, 0, strlen('resource_field_value_')) == 'resource_field_value_') {
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
            $resourceFieldModel = $this->_getResourceFieldModel();

            $verifyUsability = XenForo_Application::get('options')->waindigo_showSearchUsableOnly_customFields;
            $resourceFields = $resourceFieldModel->getUsableResourceFields(null, $verifyUsability);

            $response->params['search']['customFields'] = $resourceFieldModel->prepareGroupedResourceFields(
                $resourceFields, true);
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
            if (strlen($tableName) > strlen('resource_field_value_') &&
                 substr($tableName, 0, strlen('resource_field_value_')) == 'resource_field_value_') {
                $structures[$tableName] = array(
                    'table' => 'xf_resource_field_value',
                    'key' => 'resource_id',
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
     * @return XenResource_Model_ResourceField
     */
    protected function _getResourceFieldModel()
    {
        if (!$this->_resourceFieldModel) {
            $this->_resourceFieldModel = XenForo_Model::create('XenResource_Model_ResourceField');
        }

        return $this->_resourceFieldModel;
    } /* END _getResourceFieldModel */
}