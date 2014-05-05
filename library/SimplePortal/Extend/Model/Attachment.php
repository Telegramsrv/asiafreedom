<?php


/**
 * @xfcp XenForo_Model_Attachment
 *
 */
class SimplePortal_Extend_Model_Attachment extends
	XFCP_SimplePortal_Extend_Model_Attachment
{
	public function canViewAttachment(array $attachment, $tempHash = '', array $viewingUser = null)
	{
		$parentReturn = parent::canViewAttachment($attachment, $tempHash, $viewingUser);
		if ($parentReturn == false && $this->_getDb()->fetchRow('SELECT * from xf_portalitem where attachment_id = ?', array($attachment['attachment_id']))) {
			return true;
		}

		return $parentReturn;
	}

}