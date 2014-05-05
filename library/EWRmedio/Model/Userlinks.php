<?php

class EWRmedio_Model_Userlinks extends XenForo_Model
{
	public function getUserLinks($media)
	{
		$userlinks = $this->_getDb()->fetchAll("
			SELECT EWRmedio_userlinks.*, xf_user.*, tag.username AS tagger
			FROM EWRmedio_userlinks
				INNER JOIN xf_user ON (xf_user.user_id = EWRmedio_userlinks.username_id)
				LEFT JOIN xf_user AS tag ON (tag.user_id = EWRmedio_userlinks.user_id)
			WHERE media_id = ?
			ORDER BY xf_user.username ASC
		", $media['media_id']);

        return $userlinks;
	}
	
	public function getUsernamesByMedias($mediaIDs)
	{
		if (empty($mediaIDs)) { return array(); }
	
		if (!$userIDs = $this->_getDb()->fetchAll("
			SELECT username_id
				FROM EWRmedio_userlinks
			WHERE media_id IN (" . $this->_getDb()->quote($mediaIDs) . ")
			GROUP BY username_id
		"))
		{
			return array();
		}

		if (!$users = $this->_getDb()->fetchAll("
			SELECT *
			FROM
			(
				SELECT xf_user.*, COUNT(EWRmedio_userlinks.userlink_id) AS count
				FROM xf_user
					LEFT JOIN EWRmedio_userlinks ON (EWRmedio_userlinks.username_id = xf_user.user_id)
				WHERE xf_user.user_id IN (" . $this->_getDb()->quote($userIDs) . ")
				GROUP BY xf_user.user_id
				ORDER BY count DESC
				LIMIT ?
			) t
			ORDER BY username ASC
		", XenForo_Application::get('options')->EWRmedio_displaybooru))
		{
			return array();
		}

        return $users;
	}
	
	public function prepareUsernameFilter($text)
	{
		$usernames = explode(',', $text);
		$users = $this->getModelFromCache('XenForo_Model_User')->getUsersByNames($usernames);
		
		$newText = array();
		foreach ($users AS $user)
		{
			$newText[] = $user['username'];
		}
		$filter = implode(',', $newText);
		
		foreach ($users AS &$user)
		{
			$remove = array();
			
			foreach ($newText AS $word)
			{
				if ($word != $user['username'])
				{
					$remove[] = $word;
				}
			}
		
			$user['remove'] = implode(',', $remove);
		}
		
		return array($users, $filter);
	}
	
	public function getUserlinkByID($linkID)
	{
		if (!$userlink = $this->_getDb()->fetchRow("
			SELECT *
				FROM EWRmedio_userlinks
			WHERE userlink_id = ?
		", $linkID))
		{
			return false;
		}

        return $userlink;
	}

	public function getUserlinkByUserAndMedia($userID, $mediaID)
	{
		if (!$userlink = $this->_getDb()->fetchRow("
			SELECT *
				FROM EWRmedio_userlinks
			WHERE username_id = ? AND media_id = ?
		", array($userID, $mediaID)))
		{
			return false;
		}

        return $userlink;
	}

	public function updateUserlinks($newusers, $media)
	{
		foreach ($newusers AS $userID)
		{
			if (!$link = $this->getUserlinkByUserAndMedia($userID, $media['media_id']))
			{
				$dw = XenForo_DataWriter::create('EWRmedio_DataWriter_Userlinks');
				$dw->set('username_id', $userID);
				$dw->set('media_id', $media['media_id']);
				$dw->save();
			}
		}

		return true;
	}

	public function deleteUserlinks($oldlinks, $newlinks)
	{
		foreach ($oldlinks AS $key => $value)
		{
			if (!array_key_exists($key, $newlinks))
			{
				$dw = XenForo_DataWriter::create('EWRmedio_DataWriter_Userlinks');
				$dw->setExistingData(array('userlink_id' => $key));
				$dw->delete();
			}
		}

		return true;
	}
}