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

	public function getYouTube(){
		$fields = $this->_getDb()->fetchAll('
			SELECT user_id, field_value "youtubeUploads" from xf_user_field_value WHERE field_id="youtubeUploads"
		');

		$access = $this->_getDb()->fetchAll('
			SELECT user_id, field_value "access_token" from xf_user_field_value WHERE field_id="access_token"
		');

		$id = $this->_getDb()->fetchAll('
			SELECT user_id, field_value "youtube_id" from xf_user_field_value WHERE field_id="youtube_id"
		');

		for($i=0; $i<sizeof($fields); $i++)
			for($j=0; $j<sizeof($access); $j++)
				if($fields[$i]['user_id']==$access[$j]['user_id']) 
					$fields[$i]['access_token'] = $access[$j]['access_token'];

		for($i=0; $i<sizeof($fields); $i++)
			for($j=0; $j<sizeof($id); $j++)
				if($fields[$i]['user_id']==$id[$j]['user_id']) 
					$fields[$i]['youtube_id'] = $id[$j]['youtube_id'];

		return $fields;
	}

	public function filterTwitchStreams($values) {
		$toReturn = array();
		foreach($values['twitchStreams'] as $value) {
			$toFilter = $value['value'];
			$toFilter = explode(',', $toFilter);
			foreach ($toFilter as $key => $value2) {
				$val = rtrim(ltrim($value2));
				if(strlen($val)) {
					$val = $this->getUserFromURL($val);
					$toReturn[$val] = $value['user']['username'].'.'.$value['user']['user_id'];
				}
			}
		}

		return $toReturn;
	}

	public function getUserFromURL($url) {
		$tokens = explode('/', $url);
		$tokens = array_values(array_filter($tokens));
		return $tokens[count($tokens)-1];
	}
}