<?php

/**
 * Node-specific attachment handler.
 *
 * @package XenForo_Attachment
 */
class Waindigo_AdminImages_AttachmentHandler_Node extends XenForo_AttachmentHandler_Abstract
{
	protected $_forumModel = null;

	/**
	 * Key of primary content in content data array.
	 *
	 * @var string
	 */
	protected $_contentIdKey = 'node_id';

	/**
	 * Route to get to a node
	 *
	 * @var string
	 */
	protected $_contentRoute = 'forums';

	/**
	 * Name of the phrase that describes the content type
	 *
	 * @var string
	 */
	protected $_contentTypePhraseKey = 'node';

	/**
	 * Determines if attachments and be uploaded and managed in this context.
	 *
	 * @see XenForo_AttachmentHandler_Abstract::_canUploadAndManageAttachments()
	 */
	protected function _canUploadAndManageAttachments(array $contentData, array $viewingUser)
	{
		$forumModel = $this->_getForumModel();

		if (!empty($contentData['node_id']))
		{
			$forum = $forumModel->getForumById($contentData['node_id'], array(
				'permissionCombinationId' => $viewingUser['permission_combination_id']
			));
			if ($forum)
			{
				$permissions = XenForo_Permission::unserializePermissions($forum['node_permission_cache']);

				return (
					$forumModel->canViewForum($forum, $null, $permissions, $viewingUser)
					&& $forumModel->canUploadAndManageAttachment($forum, $null, $permissions, $viewingUser)
				);
			}
		}

		return false; // invalid content data
	} /* END _canUploadAndManageAttachments */

	/**
	 * Determines if the specified attachment can be viewed.
	 *
	 * @see XenForo_AttachmentHandler_Abstract::_canViewAttachment()
	 */
	protected function _canViewAttachment(array $attachment, array $viewingUser)
	{
		return true;
	} /* END _canViewAttachment */

	/**
	 * Code to run after deleting an associated attachment.
	 *
	 * @see XenForo_AttachmentHandler_Abstract::attachmentPostDelete()
	 */
	public function attachmentPostDelete(array $attachment, Zend_Db_Adapter_Abstract $db)
	{
		// do nothing
	} /* END attachmentPostDelete */

	/**
	 * @return XenForo_Model_Forum
	 */
	protected function _getForumModel()
	{
		if (!$this->_forumModel)
		{
			$this->_forumModel = XenForo_Model::create('XenForo_Model_Forum');
		}

		return $this->_forumModel;
	} /* END _getForumModel */

	/**
	 * @see XenForo_AttachmentHandler_Abstract::_getContentRoute()
	 */
	protected function _getContentRoute()
	{
		return 'forums';
	} /* END _getContentRoute */
}