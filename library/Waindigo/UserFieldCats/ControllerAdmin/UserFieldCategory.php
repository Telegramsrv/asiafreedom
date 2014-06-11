<?php

/**
 * Admin controller for handling actions on user field categories.
 */
class Waindigo_UserFieldCats_ControllerAdmin_UserFieldCategory extends XenForo_ControllerAdmin_Abstract
{
    /**
     * Shows a list of user field categories.
     *
     * @return XenForo_ControllerResponse_View
     */
    public function actionIndex()
    {
        $userFieldCategoryModel = $this->_getUserFieldCategoryModel();
        $userFieldCategories = $userFieldCategoryModel->getUserFieldCategories();
        $viewParams = array(
            'userFieldCategories' => $userFieldCategories,
        );
        return $this->responseView('Waindigo_UserFieldCats_ViewAdmin_UserFieldCategory_List', 'waindigo_user_field_category_list_userfieldcats', $viewParams);
    } /* END actionIndex */

    /**
     * Helper to get the user field category add/edit form controller response.
     *
     * @param array $userFieldCategory
     *
     * @return XenForo_ControllerResponse_View
     */
    protected function _getUserFieldCategoryAddEditResponse(array $userFieldCategory)
    {
        $userFieldCategory['userGroupIds'] = array();
        if (isset($userFieldCategory['user_group_ids']) && $userFieldCategory['user_group_ids']) {
            $userFieldCategory['userGroupIds'] = explode(',', $userFieldCategory['user_group_ids']);
        }

        /* @var $userGroupModel XenForo_Model_UserGroup */
        $userGroupModel = $this->getModelFromCache('XenForo_Model_UserGroup');

        $viewParams = array(
            'userFieldCategory' => $userFieldCategory,
            'userGroups' => $userGroupModel->getAllUserGroups()
        );

        return $this->responseView('Waindigo_UserFieldCats_ViewAdmin_UserFieldCategory_Edit', 'waindigo_user_field_category_edit_userfieldcats', $viewParams);
    } /* END _getUserFieldCategoryAddEditResponse */

    /**
     * Displays a form to add a new user field category.
     *
     * @return XenForo_ControllerResponse_View
     */
    public function actionAdd()
    {
        $userFieldCategory = $this->_getUserFieldCategoryModel()->getDefaultUserFieldCategory();

        return $this->_getUserFieldCategoryAddEditResponse($userFieldCategory);
    } /* END actionAdd */

    /**
     * Displays a form to edit an existing user field category.
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionEdit()
    {
        $userFieldCategoryId = $this->_input->filterSingle('user_field_category_id', XenForo_Input::STRING);

        if (!$userFieldCategoryId) {
            return $this->responseReroute('Waindigo_UserFieldCats_ControllerAdmin_UserFieldCategory', 'add');
        }

        $userFieldCategory = $this->_getUserFieldCategoryOrError($userFieldCategoryId);

        return $this->_getUserFieldCategoryAddEditResponse($userFieldCategory);
    } /* END actionEdit */

    /**
     * Inserts a new user field category or updates an existing one.
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionSave()
    {
        $this->_assertPostOnly();

        $userFieldCategoryId = $this->_input->filterSingle('user_field_category_id', XenForo_Input::STRING);

        $input = $this->_input->filter(array(
            'title' => XenForo_Input::STRING,
            'user_group_ids' => XenForo_Input::ARRAY_SIMPLE,
        ));

        $input['user_group_ids'] = implode(',', $input['user_group_ids']);

        $writer = XenForo_DataWriter::create('Waindigo_UserFieldCats_DataWriter_UserFieldCategory');
        if ($userFieldCategoryId) {
            $writer->setExistingData($userFieldCategoryId);
        }
        $writer->bulkSet($input);
        $writer->save();

        if ($this->_input->filterSingle('reload', XenForo_Input::STRING)) {
            return $this->responseRedirect(
                XenForo_ControllerResponse_Redirect::RESOURCE_UPDATED,
                XenForo_Link::buildAdminLink('user-field-categories/edit', $writer->getMergedData())
            );
        } else {
            return $this->responseRedirect(
                XenForo_ControllerResponse_Redirect::SUCCESS,
                XenForo_Link::buildAdminLink('user-field-categories') . $this->getLastHash($writer->get('user_field_category_id'))
            );
        }
    } /* END actionSave */

    /**
     * Deletes a user field category.
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionDelete()
    {
        $userFieldCategoryId = $this->_input->filterSingle('user_field_category_id', XenForo_Input::STRING);
        $userFieldCategory = $this->_getUserFieldCategoryOrError($userFieldCategoryId);

        $writer = XenForo_DataWriter::create('Waindigo_UserFieldCats_DataWriter_UserFieldCategory');
        $writer->setExistingData($userFieldCategory);

        if ($this->isConfirmedPost()) { // delete user field category
            $writer->delete();

            return $this->responseRedirect(
                    XenForo_ControllerResponse_Redirect::SUCCESS,
                    XenForo_Link::buildAdminLink('user-field-categories')
            );
        } else { // show delete confirmation prompt
            $writer->preDelete();
            $errors = $writer->getErrors();
            if ($errors) {
                return $this->responseError($errors);
            }

            $viewParams = array(
                'userFieldCategory' => $userFieldCategory
            );

            return $this->responseView('Waindigo_UserFieldCats_ViewAdmin_UserFieldCategory_Delete', 'waindigo_user_field_category_delete_userfieldcats', $viewParams);
        }
    } /* END actionDelete */

    /**
     * Gets a valid user field category or throws an exception.
     *
     * @param string $userFieldCategoryId
     *
     * @return array
     */
    protected function _getUserFieldCategoryOrError($userFieldCategoryId)
    {
        $userFieldCategory = $this->_getUserFieldCategoryModel()->getUserFieldCategoryById($userFieldCategoryId);
        if (!$userFieldCategory) {
            throw $this->responseException($this->responseError(new XenForo_Phrase('waindigo_requested_user_field_category_not_found_userfieldcats'), 404));
        }

        return $userFieldCategory;
    } /* END _getUserFieldCategoryOrError */

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