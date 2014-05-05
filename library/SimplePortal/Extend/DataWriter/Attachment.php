<?php


/**
 * @xfcp XenForo_DataWriter_Attachment
 *
 */
class SimplePortal_Extend_DataWriter_Attachment extends
	XFCP_SimplePortal_Extend_DataWriter_Attachment
{
	protected function _postDelete()
	{
		parent::_postDelete();

		$this->_db->update('xf_portalitem',
					array('attachment' => '', 'attachment_id' => 0),
											'attachment_id = ' . $this->get('attachment_id'));
	}

}