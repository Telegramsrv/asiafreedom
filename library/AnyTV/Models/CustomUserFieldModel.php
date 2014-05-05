<?php

/**
 * Model for custom user fields.
 */
class AnyTV_Models_CustomUserFieldModel extends XenForo_Model_UserField
{
	public function getFieldValuesByFieldId($fieldId) {
		$userModel = XenForo_Model::create('XenForo_Model_User');
		$fields = $this->_getDb()->fetchAll('
			SELECT value.*, field.field_type
			FROM xf_user_field_value AS value
			INNER JOIN xf_user_field AS field ON (field.field_id = value.field_id)
			WHERE CONVERT(value.field_id USING utf8) = "'.$fieldId.'"
		');

		$values = array();
		foreach ($fields AS $field)
		{
			if ($field['field_type'] == 'checkbox' || $field['field_type'] == 'multiselect')
			{
				$values[$fieldId][] = array(
					'user' 	=> $userModel->getUserById($field['user_id']),
					'value' =>	@unserialize($field['field_value'])
				);
			}
			else
			{
				$values[$fieldId][] = array(
					'user' 	=> $userModel->getUserById($field['user_id']),
					'value' => $field['field_value']
				);
			}
		}

		return $values;
	}
}