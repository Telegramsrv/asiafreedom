<?php


class SimplePortal_ControllerPublic_CreateNew extends SimplePortal_ControllerPublic_Abstract
{
    protected function _preDispatch($action){
        parent::_preDispatch($action);
        if (!SimplePortal_Static::hasPermission('useCreateNew')){
            return $this->responseNoPermission();
        }
    }

    public function actionIndex(){

        /** @var $forumModel xenForo_Model_Forum */
        $forumModel = $this->getModelFromCache('XenForo_Model_Forum');
        /** @var $nodeModel XenForo_Model_Node */
        $nodeModel = $this->getModelFromCache('XenForo_Model_Node');

        $forums = $nodeModel->getViewableNodeList();

        foreach ($forums AS $forumId => $forum){
            if (
                $forum['node_type_id'] == 'Forum' &&
                $forumModel->canPostThreadInForum($forumModel->getForumById($forumId,array(XenForo_Model_Thread::FETCH_FORUM_OPTIONS))))
            {
                $forums[$forumId]['is_forum'] = true;
            }
            else if( $forum['node_type_id']  != 'Category')
            {
                unset($forums[$forumId]);
            }
        }

        $viewParams = array(
            'forums'  => $forums,
            'additionalTypes' => SimplePortal_Static::getItemModel()->getAdditonalTypesForCreateNewForm()
        );

        return $this->responseView('SimplePortal_ViewPublic_CreateNewContent', 'el_portal_create_new', $viewParams);
    }
}