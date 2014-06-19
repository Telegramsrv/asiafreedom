<?php

class Waindigo_AdminImages_ControllerAdmin_AdminImage extends XenForo_ControllerAdmin_Abstract
{
	public function actionIndex()
	{
		$attachmentModel = $this->_getAttachmentModel();
	
		$page = $this->_input->filterSingle('page', XenForo_Input::UINT);
		$perPage = 50;

		$pageParams = array();

		$typeHandlers = $this->_getAdminImageModel()->getAdminImageHandlers();
		
		$contentTypes = array_keys($typeHandlers);
		$contentType = reset($contentTypes);
		
		$conditions = array(
			'content_type' => $contentType,
		);
		
		$visitor = XenForo_Visitor::getInstance();
		
		/* @var $adminModel XenForo_Model_Admin */
		$adminModel = XenForo_Model::create('XenForo_Model_Admin');
		$admin = $adminModel->getAdminById($visitor['user_id']);
		
		$nodeIds = array();
		if (!$visitor->isSuperAdmin() && isset($admin['is_content_admin']) && $admin['is_content_admin'])
		{
			$conditions['content_ids'] = $typeHandlers[$contentType]->getModeratedNodeIds();
		}
		
		$fetchOptions = array(
			'page' => $page,
			'perPage' => $perPage,
			'join' => XenForo_Model_Attachment::FETCH_USER
		);
	
		$attachments = $attachmentModel->getAttachments($conditions, $fetchOptions);
	
		$viewParams = array(
			'attachments' => $attachmentModel->prepareAttachments($attachments, true),
			
			'page' => $page,
			'perPage' => $perPage,
			'pageParams' => $pageParams,
			'total' => $attachmentModel->countAttachments($conditions)
		);
	
		return $this->responseView('Waindigo_AdminImages_ViewAdmin_AdminImage_List', 'waindigo_admin_image_list_adminimages', $viewParams);
	} /* END actionIndex */
	
	/**
	 * Displays a form to allow choice of content type and upload box.
	 *
	 * @return XenForo_ControllerResponse_Abstract
	 */
	public function actionUpload()
	{
		$adminImageModel = $this->_getAdminImageModel();
		
		$adminImages = XenForo_Upload::getUploadedFiles('admin_image');

		$input = $this->_input->filter(array(
				'type' => XenForo_Input::STRING,
				'type_id' => array(XenForo_Input::UINT, 'array' => true)
		));
		
		if (empty($adminImages) || !$input['type'] || !$input['type_id'])
		{
			$typeHandlers = $this->_getAdminImageModel()->getAdminImageHandlers();
			
			$contentTypes = array_keys($typeHandlers);
			if (!$input['type']) $input['type'] = reset($contentTypes);
		
			$viewParams = array(
					'type' => $input['type'],
					'typeId' => $input['type_id'],
					'typeHandlers' => $typeHandlers,
			);

			return $this->responseView('Waindigo_AdminImages_ViewAdmin_AdminImage_Upload', 'waindigo_admin_image_upload_adminimages', $viewParams);
		}
		
		$this->_assertPostOnly();
		
		$handler = $adminImageModel->getAdminImageHandlers($input['type']);
		$contentId = isset($input['type_id'][$input['type']]) ? $input['type_id'][$input['type']] : 0;
		
		if (!$handler->getContentTitle($contentId))
		{
			return $this->responseError(new XenForo_Phrase('waindigo_please_select_a_valid_attach_to_item_adminimages'), 404);
		}
		
		$adminImage = reset($adminImages);
		
		$attachmentModel = $this->_getAttachmentModel();
		
		if ($adminImage->isImage())
		{
			$dataId = $attachmentModel->insertUploadedAttachmentData($adminImage, XenForo_Visitor::getUserId());
			$attachmentId = $attachmentModel->insertTemporaryAttachment($dataId, 'adminimage-'.$input['type'].'-'.$input['type_id'][$input['type']]);
			$this->_getAdminImageModel()->associateAttachment($input['type'], $input['type_id'][$input['type']]);
		}
		else
		{
			throw $this->responseException($this->responseError(new XenForo_Phrase('waindigo_uploaded_file_is_not_an_image_adminimages')));
		}
		
		return $this->responseRedirect(
				XenForo_ControllerResponse_Redirect::SUCCESS,
				XenForo_Link::buildAdminLink('images')
		);
	} /* END actionUpload */
	
	/**
	 * @return Waindigo_AdminImages_Model_AdminImage
	 */
	protected function _getAdminImageModel()
	{
		return $this->getModelFromCache('Waindigo_AdminImages_Model_AdminImage');
	} /* END _getAdminImageModel */

	/**
	 * @return XenForo_Model_Attachment
	 */
	protected function _getAttachmentModel()
	{
		return $this->getModelFromCache('XenForo_Model_Attachment');
	} /* END _getAttachmentModel */
}