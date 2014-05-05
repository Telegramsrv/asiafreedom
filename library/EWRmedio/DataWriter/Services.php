<?php

class EWRmedio_DataWriter_Services extends XenForo_DataWriter
{
	protected $_existingDataErrorPhrase = 'requested_service_not_found';

	protected function _getFields()
	{
		return array(
			'EWRmedio_services' => array(
				'service_id'			=> array('type' => self::TYPE_UINT, 'autoIncrement' => true),
				'service_name'			=> array('type' => self::TYPE_STRING, 'required' => true),
				'service_media'			=> array('type' => self::TYPE_STRING, 'required' => true, 'default' => 'video',
					'allowedValues' => array('video', 'gallery')
				),
				'service_regex'			=> array('type' => self::TYPE_STRING, 'required' => true),
				'service_playlist'		=> array('type' => self::TYPE_STRING, 'required' => true),
				'service_url'			=> array('type' => self::TYPE_STRING, 'required' => true),
				'service_callback'		=> array('type' => self::TYPE_STRING, 'required' => true),
				'service_width'			=> array('type' => self::TYPE_UINT, 'required' => true),
				'service_height'		=> array('type' => self::TYPE_UINT, 'required' => true),
				'service_embed'			=> array('type' => self::TYPE_STRING, 'required' => true),
				'service_local'			=> array('type' => self::TYPE_UINT, 'required' => true),
			)
		);
	}

	protected function _getExistingData($data)
	{
		if (!$srvID = $this->_getExistingPrimaryKey($data, 'service_id'))
		{
			return false;
		}

		return array('EWRmedio_services' => $this->getModelFromCache('EWRmedio_Model_Services')->getServiceByID($srvID));
	}

	protected function _getUpdateCondition($tableName)
	{
		return 'service_id = ' . $this->_db->quote($this->getExisting('service_id'));
	}

	protected function _verifyNull(&$field)
	{
		if (!$field) { $field = 'null'; }
		return true;
	}

	protected function _preSave()
	{
		if ($this->isChanged('service_callback'))
		{
			$callback = $this->get('service_callback');

			if (!method_exists($callback, 'dumpMedia'))
			{
				$this->error(new XenForo_Phrase('please_enter_valid_callback_class'), 'service_callback');
			}
		}
	}
}