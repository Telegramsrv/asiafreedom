<?php

/**
 *
 * @see XenResource_ControllerAdmin_Field
 */
class Waindigo_CustomFields_Extend_XenResource_ControllerAdmin_Field extends XFCP_Waindigo_CustomFields_Extend_XenResource_ControllerAdmin_Field
{

    /**
     *
     * @see XenResource_ControllerAdmin_Field::_getFieldAddEditResponse()
     */
    protected function _getFieldAddEditResponse(array $field)
    {
        $userGroups = $this->getModelFromCache('XenForo_Model_UserGroup')->getAllUserGroups();

        if ((isset($field['field_choices_callback_class']) && $field['field_choices_callback_class']) &&
             (isset($field['field_choices_callback_method']) && $field['field_choices_callback_method'])) {
            $field['choice_type'] = "callback";
        } else {
            $field['choice_type'] = "custom";
        }

        if (!empty($field['field_id'])) {
            $selUserGroupIds = explode(',', $field['allowed_user_group_ids']);
            if (in_array(-1, $selUserGroupIds)) {
                $allUserGroups = true;
                $selUserGroupIds = array_keys($userGroups);
            } else {
                $allUserGroups = false;
            }
        } else {
            $allUserGroups = true;
            $selUserGroupIds = array_keys($userGroups);
        }

        /* @var $response XenForo_ControllerResponse_View */
        $response = parent::_getFieldAddEditResponse($field);

        $fieldModel = $this->getModelFromCache('Waindigo_CustomFields_Model_ResourceField');

        if ($response instanceof XenForo_ControllerResponse_View) {
            $addOnModel = $this->_getAddOnModel();
            $response->params = array_merge($response->params, array(
                'fieldGroupOptions' => $fieldModel->getResourceFieldGroupOptions(isset($field['field_group_id']) ? $field['field_group_id'] : 0),

                'allUserGroups' => $allUserGroups,
                'selUserGroupIds' => $selUserGroupIds,

                'userGroups' => $userGroups,

                'addOnOptions' => $addOnModel->getAddOnOptionsListIfAvailable(),
                'addOnSelected' => (isset($field['addon_id']) ? $field['addon_id'] : $addOnModel->getDefaultAddOnId()),
            ));
        }

        return $response;
    } /* END _getFieldAddEditResponse */

    /**
     *
     * @see XenResource_ControllerAdmin_Field::actionSave()
     */
    public function actionSave()
    {
        $GLOBALS['XenResource_ControllerAdmin_Field'] = $this;

        return parent::actionSave();
    } /* END actionSave */

    /**
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionExport()
    {
        return $this->responseReroute('Waindigo_CustomFields_ControllerAdmin_ResourceField', 'export');
    } /* END actionExport */

    /**
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionImport()
    {
        return $this->responseReroute('Waindigo_CustomFields_ControllerAdmin_ResourceField', 'import');
    } /* END actionImport */

    /**
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionQuickSet()
    {
        return $this->responseReroute('Waindigo_CustomFields_ControllerAdmin_ResourceField', 'quick-set');
    } /* END actionQuickSet */

    /**
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionGroups()
    {
        return $this->responseReroute('Waindigo_CustomFields_ControllerAdmin_ResourceField', 'groups');
    } /* END actionGroups */

    /**
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionAddGroup()
    {
        return $this->responseReroute('Waindigo_CustomFields_ControllerAdmin_ResourceField', 'add-group');
    } /* END actionAddGroup */

    /**
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionEditGroup()
    {
        return $this->responseReroute('Waindigo_CustomFields_ControllerAdmin_ResourceField', 'edit-group');
    } /* END actionEditGroup */

    /**
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionSaveGroup()
    {
        return $this->responseReroute('Waindigo_CustomFields_ControllerAdmin_ResourceField', 'save-group');
    } /* END actionSaveGroup */

    /**
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionDeleteGroup()
    {
        return $this->responseReroute('Waindigo_CustomFields_ControllerAdmin_ResourceField', 'delete-group');
    } /* END actionDeleteGroup */

    /**
     *
     * @return XenForo_Model_AddOn
     */
    protected function _getAddOnModel()
    {
        return $this->getModelFromCache('XenForo_Model_AddOn');
    } /* END _getAddOnModel */
}