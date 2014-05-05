<?php

class SimplePortal_ControllerPublic_Item extends SimplePortal_ControllerPublic_Abstract
{
    protected function _preDispatch($action)
    {
        parent::_preDispatch($action);

        if (!SimplePortal_Static::getItemModel()->canPromoteItem(null, array())) {
            throw $this->getNoPermissionResponseException();
        };
    }


    public function actionIndex()
    {
        $conditions = array();
        $fetchOptions = SimplePortal_Static::getItemModel()->getDefaultFetchOptions();

        $items = SimplePortal_Static::getItemModel()->getPortalItems($conditions, $fetchOptions);
        $items = SimplePortal_Static::getItemModel()->fetchPortalItemsData($items);

        $categories = $this->getModelFromCache('SimplePortal_Model_Category')->getAllCategories();

        $viewParams = array(
            'items' => $items,
            'categories' => $categories
        );

        return $this->responseView('SimplePortal_ViewPublic_ItemManage', 'el_portal_items_manage', $viewParams);
    }


    public function actionEdit()
    {
        /*  @var $handler SimplePortal_ItemHandler_Abstract */
        $form = new SimplePortal_Form_Item($this->_input);
        $input = $form->getValidatedInputFields();
        $conditions = $form->getConditions($input);

        if (!$item = SimplePortal_Static::getItemModel()->getPortalItem($conditions)) {
            $item = SimplePortal_Static::getItemModel()->getDefaultItem();
            if (!isset($conditions['content_id'],$conditions['content_type'])){
                return $this->responseMessage('Invalid portal item condition');
            }
            $item['content_id'] = $conditions['content_id'];
            $item['content_type'] = $conditions['content_type'];
        }

        $handler = SimplePortal_Static::getItemModel()->getPortalItemHandlerClass($item['content_type']);

        if ($this->isConfirmedPost()) {
            if (isset($item['portalItem_id'])) {
                $data = $handler->getItemById($item['content_id']);
                if ($deleteItem = $this->_input->filterSingle('delete_item', XenForo_Input::UINT)) {
                    return $this->deleteItem($item, $data, $input);
                }

                $this->saveItem($item, $input, $handler, $data);
                return $this->responseRedirect(
                    XenForo_ControllerResponse_Redirect::SUCCESS,
                    $this->getDynamicRedirect(false)
                );
            }

            $dm = XenForo_DataWriter::create('SimplePortal_DataWriter_PortalItem');
            $handler->processAdditonalSaveData($dm, $input);
            $this->setDwFieldsFromInput($dm, $input);
            $dm->save();
            $data = $handler->getItemById($input['content_id']);
            XenForo_Model_Log::logModeratorAction($input['content_type'], $data, SimplePortal_Static::MOD_ACTION_PROMOTE);

            return $this->responseRedirect(
                XenForo_ControllerResponse_Redirect::SUCCESS,
                $this->getDynamicRedirect(false)
            );
        }

        $viewParams = array(
            'categories' => $this->getModelFromCache('SimplePortal_Model_Category')->getAllCategories(),
            'content_type' => $item['content_type'],
            'content_id' => $item['content_id'],
            'item' => $item,
            'attachments' => $handler->getAttachmentsForContent($input['content_id'])
        );

        return $this->responseView('SimplePortal_ViewPublic_Manage', 'el_portal_confirm', $viewParams);
    }


    protected function setDwFieldsFromInput(SimplePortal_DataWriter_PortalItem &$dw, array $input)
    {
        $dw->set('attachment_id', $input['attachment_id']);
        $dw->set('content_type', $input['content_type']);
        $dw->set('content_id', $input['content_id']);
        $dw->set('category_id', $input['category_id']);
        $dw->set('display_order', $input['display_order']);
        return $dw;
    }

    public function saveItem($existingItem = false, $input, SimplePortal_ItemHandler_Abstract $handler, $data, $modAction = SimplePortal_Static::MOD_ACTION_EDIT)
    {
        $dm = XenForo_DataWriter::create('SimplePortal_DataWriter_PortalItem');
        if ($existingItem) {
            $dm->setExistingData($existingItem);
        }

        $this->setDwFieldsFromInput($dm, $input);
        $handler->processAdditonalSaveData($dm, $input);
        $dm->save();
        XenForo_Model_Log::logModeratorAction($input['content_type'], $data, $modAction);
        return $dm;
    }

    protected function deleteItem(array $item, array $data, array $input)
    {
        XenForo_Model_Log::logModeratorAction($input['content_type'], $data, 'portal_remove');
        SimplePortal_Helper_Content::demote($item['content_type'], $item['content_id']);
        $redirectLink = XenForo_Link::buildPublicLink('portal/manage-items');
        return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, $redirectLink);
    }
}