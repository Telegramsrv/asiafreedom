<?php

/**
 *
 * @see XenForo_ControllerAdmin_UserField
 */
class Waindigo_UserFieldCats_Extend_XenForo_ControllerAdmin_UserField extends XFCP_Waindigo_UserFieldCats_Extend_XenForo_ControllerAdmin_UserField
{

    /**
     *
     * @see XenForo_ControllerAdmin_UserField::actionIndex()
     */
    public function actionIndex()
    {
        $response = parent::actionIndex();

        if ($response instanceof XenForo_ControllerResponse_View) {
            if (isset($response->params['fieldsGrouped']['custom'])) {
                foreach ($response->params['fieldsGrouped']['custom'] as $fieldId => $field) {
                    $response->params['fieldsGrouped']['custom' . $field['user_field_category_id']][$fieldId] = $field;
                }
                unset($response->params['fieldsGrouped']['custom']);

                $userFieldCategories = Waindigo_UserFieldCats_Model_UserFieldCategory::getUserFieldCategoryTitles();

                foreach ($userFieldCategories as $userFieldCategoryId => $userFieldCategoryTitle) {
                    $response->params['fieldGroups']['custom' . $userFieldCategoryId] = array(
                    	'value' => 'custom' . $userFieldCategoryId,
                        'label' => $userFieldCategoryTitle
                    );
                }
            }


        }

        return $response;
    } /* END actionIndex */

    /**
     *
     * @see XenForo_ControllerAdmin_UserField::_getFieldAddEditResponse()
     */
    protected function _getFieldAddEditResponse(array $field)
    {
        $response = parent::_getFieldAddEditResponse($field);

        if ($response instanceof XenForo_ControllerResponse_View) {
            $response->params['userFieldCategories'] = Waindigo_UserFieldCats_Model_UserFieldCategory::getUserFieldCategoryTitles();
        }

        return $response;
    } /* END _getFieldAddEditResponse */

    /**
     *
     * @see XenForo_ControllerAdmin_UserField::actionSave()
     */
    public function actionSave()
    {
        $GLOBALS['XenForo_ControllerAdmin_UserField'] = $this;

        return parent::actionSave();
    } /* END actionSave */
}