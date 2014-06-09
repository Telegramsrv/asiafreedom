<?php

class Waindigo_CustomFields_Extend_Waindigo_UserSearch_Search_DataHandler_User extends XFCP_Waindigo_CustomFields_Extend_Waindigo_UserSearch_Search_DataHandler_User
{

    /**
     *
     * @var XenForo_Model_UserField
     */
    protected $_userFieldModel = null;

    /**
     *
     * @see XenForo_Search_DataHandler_Post::getTypeConstraintsFromInput()
     */
    public function getTypeConstraintsFromInput(XenForo_Input $input)
    {
        $constraints = parent::getTypeConstraintsFromInput($input);

        $userFields = $input->filterSingle('custom_fields', XenForo_Input::ARRAY_SIMPLE);
        if ($userFields) {
            foreach ($userFields as $fieldId => $fieldValue) {
                if ($fieldValue) {
                    $constraints['user_field_id_' . $fieldId] = $fieldId;
                    $constraints['user_field_value_' . $fieldId] = $fieldValue;
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
        if (strlen($constraint) > strlen('user_field_id_') &&
        substr($constraint, 0, strlen('user_field_id_')) == 'user_field_id_') {
            if ($constraintInfo) {
                $constraintInfo = strval($constraintInfo);
                return array(
                    'query' => array(
                        'user_field_value_' . $constraintInfo,
                        'field_id',
                        '=',
                        $constraintInfo
                    )
                );
            }
        }
        if (strlen($constraint) > strlen('user_field_value_') &&
        substr($constraint, 0, strlen('user_field_value_')) == 'user_field_value_') {
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
            $userFieldModel = $this->_getUserFieldModel();

            $userFields = $userFieldModel->getUserFields(array(
            	'isSearchAdvancedUser' => true
            ));

            $response->params['search']['customFields'] = $userFieldModel->prepareUserFields(
                $userFields);
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
            if (strlen($tableName) > strlen('user_field_value_') &&
            substr($tableName, 0, strlen('user_field_value_')) == 'user_field_value_') {
                $structures[$tableName] = array(
                    'table' => 'xf_user_field_value',
                    'key' => 'user_id',
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
     * @return XenForo_Model_UserField
     */
    protected function _getUserFieldModel()
    {
        if (!$this->_userFieldModel) {
            $this->_userFieldModel = XenForo_Model::create('XenForo_Model_UserField');
        }

        return $this->_userFieldModel;
    } /* END _getUserFieldModel */
}