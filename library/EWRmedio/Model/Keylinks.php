<?php

class EWRmedio_Model_Keylinks extends XenForo_Model
{
	public function getKeywordLinks($media)
	{
		$keywords = $this->_getDb()->fetchAll("
			SELECT EWRmedio_keywords.*, EWRmedio_keylinks.*, xf_user.*
			FROM EWRmedio_keywords
				LEFT JOIN EWRmedio_keylinks ON (EWRmedio_keylinks.keyword_id = EWRmedio_keywords.keyword_id)
				LEFT JOIN xf_user ON (xf_user.user_id = EWRmedio_keylinks.user_id)
			WHERE EWRmedio_keylinks.media_id = ?
			ORDER BY EWRmedio_keywords.keyword_text ASC
		", $media['media_id']);

		return $keywords;
	}

	public function getKeywordNolinks($media)
	{
		$keywords = $this->_getDb()->fetchAll("
			SELECT EWRmedio_keywords.*
				FROM EWRmedio_keywords
				LEFT JOIN EWRmedio_keylinks ON (EWRmedio_keylinks.keyword_id = EWRmedio_keywords.keyword_id AND EWRmedio_keylinks.media_id = ?)
			WHERE EWRmedio_keylinks.media_id IS NULL
		", $media['media_id']);

		return $keywords;
	}
	
	public function getKeylinkByID($linkID)
	{
		if (!$keylink = $this->_getDb()->fetchRow("
			SELECT *
				FROM EWRmedio_keylinks
			WHERE keylink_id = ?
		", $linkID))
		{
			return false;
		}

        return $keylink;
	}

	public function getKeylinkByKeywordAndMedia($keywordID, $mediaID)
	{
		if (!$keylink = $this->_getDb()->fetchRow("
			SELECT *
				FROM EWRmedio_keylinks
			WHERE keyword_id = ? AND media_id = ?
		", array($keywordID, $mediaID)))
		{
			return false;
		}

        return $keylink;
	}

	public function updateKeylinks($newkeys, $media)
	{
		foreach ($newkeys AS $keywordID)
		{
			if (!$link = $this->getKeylinkByKeywordAndMedia($keywordID, $media['media_id']))
			{
				$dw = XenForo_DataWriter::create('EWRmedio_DataWriter_Keylinks');
				$dw->set('keyword_id', $keywordID);
				$dw->set('media_id', $media['media_id']);
				$dw->save();
			}
		}

		return true;
	}

	public function deleteKeylinks($oldlinks, $keylinks)
	{
		foreach ($oldlinks AS $key => $value)
		{
			if (!array_key_exists($key, $keylinks))
			{
				$dw = XenForo_DataWriter::create('EWRmedio_DataWriter_Keylinks');
				$dw->setExistingData(array('keylink_id' => $key));
				$dw->delete();
			}
		}

		return true;
	}
}