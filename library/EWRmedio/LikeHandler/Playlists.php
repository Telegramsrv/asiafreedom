<?php

class EWRmedio_LikeHandler_Playlists extends XenForo_LikeHandler_Abstract
{
	public function incrementLikeCounter($contentId, array $latestLikes, $adjustAmount = 1)
	{
		$dw = XenForo_DataWriter::create('EWRmedio_DataWriter_Playlists');
		$dw->setExistingData($contentId);
		$dw->set('playlist_likes', $dw->get('playlist_likes') + $adjustAmount);
		$dw->set('playlist_like_users', $latestLikes);
		$dw->save();
	}

	public function getContentData(array $contentIds, array $viewingUser)
	{
		$playlistModel = XenForo_Model::create('EWRmedio_Model_Playlists');
		$playlists = $playlistModel->getPlaylistsByIDs($contentIds);

		return $playlists;
	}

	public function getListTemplateName()
	{
		return 'news_feed_item_media_playlist_like';
	}
}