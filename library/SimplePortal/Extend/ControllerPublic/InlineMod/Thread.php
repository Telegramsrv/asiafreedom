<?php

/**
 * @proxy XenForo_ControllerPublic_InlineMod_Thread
 * class SimplePortal_Extend_ControllerPublic_InlineMod_Thread
 */
class SimplePortal_Extend_ControllerPublic_InlineMod_Thread extends
    XFCP_SimplePortal_Extend_ControllerPublic_InlineMod_Thread

{
    public function actionPromote()
    {
        $handler = SimplePortal_Static::getItemModel()->getPortalItemHandlerClass('thread');

        if ($handler && $handler->canPromote()) {
            if ($this->isConfirmedPost())
            {
                $itemIds = $this->getInlineModIds(false);

                $categoryId = $this->_input->filterSingle('category_id', XenForo_Input::UINT);
                $additionalData = array();
                if ($categoryId) {
                    $additionalData = array(
                        SimplePortal_Helper_Content::CATEGORY_ID => $categoryId
                    );
                }

                foreach ($itemIds AS $threadId) {
                    if (!$handler->isAlreadyPromoted('thread', $threadId)){
                        SimplePortal_Helper_Content::promote('thread', $threadId, $additionalData);
                        $this->getModelFromCache('SimplePortal_Model_PortalItem')->logModerationAction('thread', $threadId, SimplePortal_Static::MOD_ACTION_PROMOTE);
                    }
                }
                $this->clearCookie();

                return $this->responseRedirect(
                    XenForo_ControllerResponse_Redirect::SUCCESS,
                    $this->getDynamicRedirect()
                );
            } else // show confirmation dialog
            {
                $threadIds = $this->getInlineModIds();
                $redirect = $this->getDynamicRedirect();

                if (!$threadIds)
                {
                    return $this->responseRedirect(
                        XenForo_ControllerResponse_Redirect::SUCCESS,
                        $redirect
                    );
                }
                $conditions = array('content_type' => 'thread',
                                    'content_id' => $threadIds);

                $alreadyPromoted = SimplePortal_Static::getItemModel()->getPortalItems($conditions,array(),'content_id');
                $threadIds = array_diff($threadIds, array_keys($alreadyPromoted));

                $redirect = $this->getDynamicRedirect();

                if (!$threadIds) {
                    return $this->responseRedirect(
                        XenForo_ControllerResponse_Redirect::SUCCESS,
                        $redirect
                    );
                }

                $viewParams = array(
                    'threadIds' => $threadIds,
                    'threadCount' => count($threadIds),
                    'redirect' => $redirect,
                    'categories' => $this->getModelFromCache('SimplePortal_Model_Category')->getAllCategories(),
                    'contentType' => new XenForo_Phrase('threads'),
                    'alreadyPromoted' => count($alreadyPromoted),
                );

                return $this->responseView('SimplePortal_ViewPublic_InlineMod_Thread', 'el_portal_inline_mod_thread_promote', $viewParams);
            }
        }

        return $this->responseNoPermission();


    }
}