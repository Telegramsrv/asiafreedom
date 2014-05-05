<?php

/**
 * @proxy XenForo_Model_Thread
 * class SimplePortal_Extend_XenForo_Model_Thread
 */
class SimplePortal_Extend_Model_Thread extends
    XFCP_SimplePortal_Extend_Model_Thread

{
    public function addInlineModOptionToThread(array &$thread, array $forum, array $nodePermissions = null, array $viewingUser = null)
    {
        $parentReturn = parent::addInlineModOptionToThread($thread, $forum, $nodePermissions, $viewingUser);
        $parentReturn['promote'] = SimplePortal_Static::getItemModel()->canPromoteItem('thread', $thread);
        return $parentReturn;
    }
}