<?php

class SimplePortal_Model_Attachment extends XenForo_Model_Attachment
{

    public function getPreviewImages($contentType, $contentId)
    {
        return $this->removeNonImageAttachments($this->prepareAttachments(
            $this->getAttachmentsByContentId($contentType, $contentId)
        ));
    }


    public function removeNonImageAttachments(array $attachments)
    {
        foreach ($attachments AS $id => $attachment) {
            if (!isset($attachment['thumbnailUrl']) OR $attachment['thumbnailUrl'] == '') {
                unset($attachments[$id]);
            }
        }
        return $attachments;
    }
}