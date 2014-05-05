<?php

class EWRmedio_DataWriter_Userlinks extends XenForo_DataWriter
{
	protected $_existingDataErrorPhrase = 'requested_userlink_not_found';

	protected function _getFields()
	{
		return array(
			'EWRmedio_userlinks' => array(
				'username_id'	=> array('type' => self::TYPE_UINT, 'required' => true),
				'media_id'		=> array('type' => self::TYPE_UINT, 'required' => true),
				'user_id'		=> array('type' => self::TYPE_UINT, 'required' => true),
				'userlink_id'	=> array('type' => self::TYPE_UINT, 'autoIncrement' => true),
				'userlink_date'	=> array('type' => self::TYPE_UINT, 'required' => true),
			)
		);
	}

	protected function _getExistingData($data)
	{
		if (!$linkID = $this->_getExistingPrimaryKey($data, 'userlink_id'))
		{
			return false;
		}

		return array('EWRmedio_userlinks' => $this->getModelFromCache('EWRmedio_Model_Userlinks')->getUserlinkByID($linkID));
	}

	protected function _getUpdateCondition($tableName)
	{
		return 'userlink_id = ' . $this->_db->quote($this->getExisting('userlink_id'));
	}

	protected function _preSave()
	{
		if (!$this->_existingData)
		{
			$this->set('user_id', XenForo_Visitor::getUserId());
			$this->set('userlink_date', XenForo_Application::$time);
		}
	}
}