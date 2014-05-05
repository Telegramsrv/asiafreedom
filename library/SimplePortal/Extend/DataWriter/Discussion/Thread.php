<?php


class SimplePortal_Extend_DataWriter_Discussion_Thread extends
    XFCP_SimplePortal_Extend_DataWriter_Discussion_Thread
{

    CONST OPTION_PROMOTE_TO_PORTAL = 'promoteToPortal';
    CONST OPTION_ATTACH_ATTACHMENT_TO_PORTAL = 'attachAttachmentToPortal';


    protected function _getDefaultOptions()
    {
        $parent = parent::_getDefaultOptions();
        $parent[self::OPTION_PROMOTE_TO_PORTAL] = false;
        $parent[self::OPTION_ATTACH_ATTACHMENT_TO_PORTAL] = true;
        return $parent;
    }


    protected function _discussionPostDelete()
    {
        parent::_discussionPostDelete();
        $this->_deletePortalItem();
    }

    protected function _discussionPreSave()
    {
        parent::_discussionPreSave(); // TODO: Change the autogenerated stub

        if (in_array($this->get('node_id'), SimplePortal_Static::option('autopromotenodes')) OR (XenForo_Application::isRegistered('extraportal.promoteThread'))) {
            $this->setOption(self::OPTION_PROMOTE_TO_PORTAL, true);
        }

    }


    protected function _discussionPostSave()
    {
        parent::_discussionPostSave();

        if ($this->isInsert() && $this->get('discussion_state') == 'visible' && $this->getOption(self::OPTION_PROMOTE_TO_PORTAL)) {
            $this->_promote();
        }

        if ($this->isUpdate() && $this->get('discussion_state') != 'visible') {
            $this->_deletePortalItem();
        }
    }

    protected function _promote()
    {
        $threadId = $this->get('thread_id');
        if (XenForo_Application::isRegistered('extraportal.promoteThread')) {
            $input = XenForo_Application::get('extraportal.promoteThread');
        } else {
            // autopromotion
            $input = array();
        }

        $data = array();

        if (isset($input['category_id'])) {
            $data[SimplePortal_Helper_Content::CATEGORY_ID] = $input['category_id'];
        }
        if (isset($input['display_order'])) {
            $data[SimplePortal_Helper_Content::DISPLAY_ORDER] = $input['display_order'];
        }
        if ($this->getOption(self::OPTION_ATTACH_ATTACHMENT_TO_PORTAL)) {
            $attachment = SimplePortal_Helper_Content::findInlineAttachment($this->getFirstMessageDw()->get('message'));
            $attachmentId = $attachment['attachment_id'];
            $data[SimplePortal_Helper_Content::ATTACHMENT_ID] = $attachmentId;
        }
        SimplePortal_Helper_Content::promote('thread', $threadId, $data);
    }


    protected function _deletePortalItem()
    {
        $threadId = $this->get('thread_id');
        SimplePortal_Helper_Content::demote('thread', $threadId);
    }

}