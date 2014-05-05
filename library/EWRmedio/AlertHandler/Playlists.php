<?php

class EWRmedio_AlertHandler_Playlists extends XenForo_AlertHandler_Abstract
{
	public function getContentByIds(array $contentIds, $model, $userId, array $viewingUser)
	{
		return $model->getModelFromCache('EWRmedio_Model_Playlists')->getPlaylistsByIDs($contentIds);
	}
}