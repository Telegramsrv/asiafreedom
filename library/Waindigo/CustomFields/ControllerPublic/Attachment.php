<?php

class Waindigo_CustomFields_ControllerPublic_Attachment extends XenForo_ControllerPublic_Abstract
{

    /**
     *
     * @return XenForo_ControllerResponse_Reroute
     */
    public function actionIndex()
    {
        $attachmentModel = $this->_getAttachmentModel();

        $fieldAttachmentId = $this->_input->filterSingle('field_attachment_id', XenForo_Input::UINT);

        $attachments = $attachmentModel->getAttachmentsByContentId('custom_field', $fieldAttachmentId);

        $attachment = reset($attachments);

        if (isset($attachment['attachment_id'])) {
            $this->_request->setParam('attachment_id', $attachment['attachment_id']);
        }

        return $this->responseReroute('XenForo_ControllerPublic_Attachment', '');
    } /* END actionIndex */

    /**
     *
     * @return XenForo_Model_Attachment
     */
    protected function _getAttachmentModel()
    {
        return $this->getModelFromCache('XenForo_Model_Attachment');
    } /* END _getAttachmentModel */
}