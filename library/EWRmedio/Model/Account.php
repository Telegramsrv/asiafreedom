<?php

class EWRmedio_Model_Account extends XenForo_Model
{
	public function getMediaLikesForContentUser($userId, array $fetchOptions = array())
	{
		$limitOptions = $this->prepareLimitFetchOptions($fetchOptions);

		return $this->fetchAllKeyed($this->limitQueryResults(
			'
				SELECT xf_liked_content.*,
					user.*
				FROM xf_liked_content
				INNER JOIN xf_user AS user ON (user.user_id = xf_liked_content.like_user_id)
				WHERE xf_liked_content.content_user_id = ?
					AND (xf_liked_content.content_type = \'media\' OR xf_liked_content.content_type = \'media_playlist\')
				ORDER BY xf_liked_content.like_date DESC
			', $limitOptions['limit'], $limitOptions['offset']
		), 'like_id', $userId);
	}
	
	public function countMediaLikesForContentUser($userId)
	{
		return $this->_getDb()->fetchOne('
			SELECT COUNT(*)
				FROM xf_liked_content
			WHERE content_user_id = ?
				AND (xf_liked_content.content_type = \'media\' OR xf_liked_content.content_type = \'media_playlist\')
		', $userId);
	}
}