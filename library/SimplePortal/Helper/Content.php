<?php
class SimplePortal_Helper_Content{

    CONST CATEGORY_ID = 'extraportal_categoryId';
    CONST DISPLAY_ORDER = 'extraportal_display_order';
    CONST ATTACHMENT_ID = 'extraportal_attachmentId';


    public static function getDefaultFormData(array $input){
        $data = array(
            SimplePortal_Helper_Content::CATEGORY_ID => $input['category_id'],
            SimplePortal_Helper_Content::DISPLAY_ORDER => $input['display_order'],
        );

        return $data;
    }


    /**
     * @param $contentType
     * @param $contentId
     * @param array $additionalData
     * @return array returned datawriter data
     */
    public static function promote($contentType, $contentId, array $additionalData = array()){

        $dm = XenForo_DataWriter::create('SimplePortal_DataWriter_PortalItem');
        $handler = SimplePortal_Static::getItemModel()->getPortalItemHandlerClass($contentType);

        $dm->set('content_type', $contentType);
        $dm->set('content_id', $contentId);

        if (!isset($additionalData['content_id'])){
            $additionalData['content_id'] = $contentId;
        }

        if (isset($additionalData[self::CATEGORY_ID])){
            $dm->set('category_id',$additionalData[self::CATEGORY_ID]);
        }

        if (isset($additionalData[self::DISPLAY_ORDER])){
            $dm->set('display_order',$additionalData[self::DISPLAY_ORDER]);
        }
        // TODO move to handler?
        if (isset($additionalData[self::ATTACHMENT_ID])){
            $dm->set('attachment_id', $additionalData[self::ATTACHMENT_ID]);
        }

        $handler->processAdditonalSaveData($dm, $additionalData);
        $dm->save();


        return $dm->getMergedData();
    }

    public static function demote($contentType, $contentId){
        $condition = array(
            'content_type' =>$contentType,
            'content_id' => $contentId
        );

        $portalItem = SimplePortal_Static::getItemModel()->getPortalItem($condition);

        if ($portalItem) {
            $dw = XenForo_DataWriter::create('SimplePortal_DataWriter_PortalItem');
            $dw->setExistingData($portalItem);
            $dw->delete();
        }
    }

    /**
     * @param $message
     * @return array|bool|false
     */
    public static function findInlineAttachment($message){
        if (preg_match_all('#\[attach(=[^\]]*)?\](?P<id>\d+)\[/attach\]#i', $message, $matches)) {
            if ($matches['id'][0]) {
                $attachModel = XenForo_Model::create('XenForo_Model_Attachment');
                $attach = $attachModel->getAttachmentById($matches['id'][0]);
                return $attach;
            }
        }
        return false;
    }
}