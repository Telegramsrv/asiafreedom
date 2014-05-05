<?php


class SimplePortal_ItemHandler_Thread extends SimplePortal_ItemHandler_Abstract
{
    /** returns the url to the create new item form, if no permissions or not available => false */
    function getCreateNewUrl()
    {
        return false;
    }

    function getContentTypeKeyPhrase()
    {
        $phrase = new XenForo_Phrase('thread');
        return $phrase->render();
    }

    function canIncludeAttachments()
    {
        return true;
    }

    function getItemUrl(array $item)
    {
        return XenForo_Link::buildPublicLink('canonical:threads', $item);
    }

    function getContentTypeKey()
    {
        return 'thread';
    }


    public function canPromote(array $item = array(), array $viewingUser = null)
    {
        $perm = SimplePortal_Static::hasPermission('promoteThreadToPortal');
        return $perm;
    }

    /**
     * allows you to set additional data to the item datawriter
     * e.g. to set the item attachment
     *
     * @param XenForo_DataWriter $itemDataWriter
     * @param                    $itemData
     *
     * @return mixed
     */
    function processAdditonalSaveData(XenForo_DataWriter &$itemDataWriter, $itemData)
    {
        //SimplePortal_Helper_Content::findInlineAttachment()
    }


    public function getFetchOptions(array $extraData = array())
    {
        return array(
            'join' => XenForo_Model_Thread::FETCH_FIRSTPOST | XenForo_Model_Thread::FETCH_USER
        );
    }

    public function getItemById($id)
    {
        return $this->getThreadModel()->getThreadById($id, $this->getFetchOptions());
    }

    public function getItemsByIds(array $ids)
    {
        $threads = $this->getThreadModel()->getThreadsByIds($ids, $this->getFetchOptions());
        return $threads;
    }

    /**
     * @return XenForo_Model_Thread
     */
    protected function getThreadModel()
    {
        return XenForo_Model::create('XenForo_Model_Thread');
    }


    /**
     * the parent method can't be used, because the attachment is associated to the first post and not to the thread
     * @param $contentId
     * @return array
     */
    public function getAttachmentsForContent($contentId)
    {
        $threadData = $this->getThreadModel()->getThreadById($contentId);
        /** @var SimplePortal_Model_Attachment $model */
        $model = XenForo_Model::create('SimplePortal_Model_Attachment');
        return $model->getPreviewImages('post', $threadData['first_post_id']);
    }


    /**
     * @param array $item
     */
    protected function _prepareContent(array &$item)
    {
       if ($item['attach_count'] > 0){

           $attachmentModel = XenForo_Model::create('XenForo_Model_Attachment');

           $attachments = $attachmentModel->getAttachmentsByContentIds('post', array($item['first_post_id']));

           foreach ($attachments AS $attachment)
           {
               $item['attachments'][$attachment['attachment_id']] = $attachmentModel->prepareAttachment($attachment);
           }
       }
    }

}