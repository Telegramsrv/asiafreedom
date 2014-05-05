<?php

class SimplePortal_Model_InlineMod extends XenForo_Model{

    public function demote(array $itemIds, array $options = array(), &$errorKey = '', array $viewingUser = null){

        foreach ($itemIds AS $itemId){
            $dw = $this->getPortalItemDw();
            if (!$dw->setExistingData($itemId))
            {
                continue;
            }

            $contentType = $dw->get('content_type');
            $dw->delete();
            $this->getModelFromCache('SimplePortal_Model_PortalItem')->logModerationAction($contentType, $itemId, SimplePortal_Static::MOD_ACTION_DEMOTE);
        }
        return true;
    }


    public function changeCategory(array $itemIds, array $options = array(), &$errorKey = '', array $viewingUser = null){

        if(!isset($options['category_id'])){
            $errorKey = 'el_no_category_selected';
            return false;
        }
        $categoryId = $options['category_id'];

        foreach($itemIds AS $itemId){
            $dw = $this->getPortalItemDw();
            $dw->setExistingData($itemId);
            $dw->set('category_id', $categoryId);
            $dw->save();
            unset($dw);
        }
        return true;
    }


    protected function getPortalItemDw(){
        return XenForo_DataWriter::create('SimplePortal_DataWriter_PortalItem');
    }


    /**
     * @return SimplePortal_Model_InlineMod
     */
    public function getPortalModel(){
        return $this->getModelFromCache('SimplePortal_Model_InlineMod');
    }
}