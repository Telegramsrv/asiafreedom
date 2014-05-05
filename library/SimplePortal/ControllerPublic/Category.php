<?php

class SimplePortal_ControllerPublic_Category extends SimplePortal_ControllerPublic_Abstract
{

	protected function _preDispatch($action)
	{
		if (! SimplePortal_Static::canManageCategories()) {
			throw $this->getNoPermissionResponseException();
		}
	}


	public function actionIndex()
	{
        return $this->responseView('SimplePortal_ViewPublic_CategoriesList', 'el_portal_categories_list',
			array('categories' => $this->getAllCategories())
		);
	}

	public function actionCreate()
	{
        return $this->getAddEditResponse($this->getCategoryModel()->getDefaultCategory(),'SimplePortal_ViewPublic_CategoryAdd');
	}


    public function actionEdit()
    {
        return $this->getAddEditResponse($this->getCategoryOrError(),'SimplePortal_ViewPublic_CategoryEdit');
    }


    protected function getAddEditResponse($category,
                                          $viewName = 'SimplePortal_ViewPublic_CategoryAdd',
                                          $templateName = 'el_portal_categories_edit'){
        return $this->responseView($viewName, $templateName, array('category' => $category, 'styles' => 	$styles = $this->getModelFromCache('XenForo_Model_Style')->getAllStylesAsFlattenedTree()));
    }


	public function actionSave()
	{
        /** @var SimplePortal_DataWriter_Category $dw */
		$dw = XenForo_DataWriter::create('SimplePortal_DataWriter_Category');
        $this->setCategoryInputFieldToDataWriter($dw);
		$dw->save();

		return $this->responseRedirect(
			XenForo_ControllerResponse_Redirect::SUCCESS,
			XenForo_Link::buildPublicLink('portal/manage-categories')
		);
	}

    /**
     * @param SimplePortal_DataWriter_Category $dw
     * @return SimplePortal_DataWriter_Category
     */
    protected function setCategoryInputFieldToDataWriter(SimplePortal_DataWriter_Category &$dw){
        $data = $this->getCategoryDataFromInput();

        if ($data['category_id']) {
            $dw->setExistingData($data['category_id']);
        }
        $dw->bulkSet($data);
        return $dw;
    }

    protected function getCategoryDataFromInput(){
        $data = $this->_input->filter(array(
            'category_id' => XenForo_Input::UINT,
            'title' => XenForo_Input::STRING,
            'display_order' => XenForo_Input::UINT,
            'style_id' => XenForo_Input::UINT
        ));
        return $data;
    }


	public function actionDelete()
	{
		$category = $this->getCategoryOrError();

		if ($this->isConfirmedPost()) {
			return $this->_deleteData('SimplePortal_DataWriter_Category', 'category_id', $this->getDynamicRedirect(false));
		}
		return $this->responseView('SimplePortal_ViewPublic_CategoryDelete', 'el_portal_category_deleteconfirm', array('category' => $category));
	}

}