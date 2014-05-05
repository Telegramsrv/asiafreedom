<?php

class SimplePortal_ControllerPublic_InlineModeration extends XenForo_ControllerPublic_InlineMod_Abstract
{
    protected function _preDispatch($action)
    {
        parent::_preDispatch($action);

        if (!SimplePortal_Static::getItemModel()->canPromoteItem(null,array())){
            throw $this->getNoPermissionResponseException();
        }
    }

    protected function getDataFromForm()
    {
        $itemIds = $this->getInlineModIds();
        $redirect = $this->getDynamicRedirect();

        if (!$itemIds) {
            return $this->responseRedirect(
                XenForo_ControllerResponse_Redirect::SUCCESS,
                $redirect
            );
        }

        return array($itemIds, $redirect);
    }


    public function actionDemote()
    {
        if ($this->isConfirmedPost()) {
            return $this->executeInlineModAction('demote',array('ignoreDeleteError'=> true));
        }

        return $this->responseView('XenResource_ViewPublic_ResourceInlineMod_Delete', 'el_portal_inline_mod_demote', $this->getViewParams());
    }

    public function actionChangeCategory()
    {
        if ($this->isConfirmedPost()) {
            $options = array();
            $categoryId = $this->_input->filterSingle('category_id', XenForo_Input::UINT);
            $options['category_id'] = $categoryId;
            return $this->executeInlineModAction('changeCategory', $options);
        }

        return $this->responseView('XenResource_ViewPublic_ResourceInlineMod_Delete',
            'el_portal_inline_mod_category', $this->getViewParams());
    }


    protected function getViewParams()
    {
        list($itemIds, $redirect) = $this->getDataFromForm();
        $viewParams = array(
            'itemIds' => $itemIds,
            'itemCount' => count($itemIds),
            'redirect' => $redirect,
            'categories' => $this->getModelFromCache('SimplePortal_Model_Category')->getAllCategories()
        );

        return $viewParams;
    }


    public $inlineModKey = 'elportal';

    /**
     * Gets the inline mod model for the specific type.
     *
     * @return SimplePortal_Model_InlineMod
     */
    public function getInlineModTypeModel()
    {
        return $this->getModelFromCache('SimplePortal_Model_InlineMod');
    }
}