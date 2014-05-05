<?php

class EWRmedio_Model_Playlists extends XenForo_Model
{
	public function getPlaylistsCount($params = array())
	{
		$params['type']  = empty($params['type'])  ? null   : $params['type'];
		$params['where'] = empty($params['where']) ? null   : $params['where'];
		
		switch ($params['type'])
		{
			case "account":
			case "user":	$onlyWhere = "WHERE EWRmedio_playlists.user_id = ".$params['where'];	break;
			default:		$onlyWhere = "WHERE EWRmedio_playlists.playlist_state = 'public'";		break;
		}
		
		$visible = $params['type'] == 'account' ? '' : "AND EWRmedio_playlists.playlist_media <> '' AND EWRmedio_playlists.playlist_state = 'public'";

        $count = $this->_getDb()->fetchRow("
			SELECT COUNT(*) AS total
				FROM EWRmedio_playlists
			$onlyWhere
				$visible
		");

		return $count['total'];
	}
	
	public function getPlaylists($start, $stop, $params = array())
	{
		if (!$stop) { return array(); }
		
		$params['sort']  = empty($params['sort'])  ? 'update' : $params['sort'];
		$params['order'] = empty($params['order']) ? 'DESC'   : $params['order'];
		$params['type']  = empty($params['type'])  ? null     : $params['type'];
		$params['where'] = empty($params['where']) ? null     : $params['where'];
		
		$params['order'] = $params['order'] == 'ASC' ? 'ASC' : 'DESC';
		
		switch ($params['type'])
		{
			case "account":
			case "user":	$onlyWhere = "WHERE EWRmedio_playlists.user_id = ".$params['where'];	break;
			default:		$onlyWhere = "WHERE EWRmedio_playlists.playlist_state = 'public'";		break;
		}
		
		$visible = $params['type'] == 'account' ? '' : "AND EWRmedio_playlists.playlist_media <> '' AND EWRmedio_playlists.playlist_state = 'public'";
		
		switch ($params['sort'])
		{
			case "update": case "date":
			case "likes": case "views":
				$orderBy = 'EWRmedio_playlists.playlist_'.$params['sort'].' '.$params['order'];
				break;
			case "title":
				$orderBy = 'EWRmedio_playlists.playlist_name '.$params['order'];
				break;
			case "popular":
				$orderBy = 'popular_score DESC';
				break;
			case "trending":
				$orderBy = 'trending_score DESC';
				break;
			default:
				$orderBy = 'EWRmedio_playlists.playlist_update '.$params['order'];
		}
		
		$start = ($start - 1) * $stop;
		
		$options = XenForo_Application::get('options');
		
		$playlists = $this->_getDb()->fetchAll("
			SELECT EWRmedio_playlists.*, xf_user.*,
				ROUND((EWRmedio_playlists.playlist_likes * ?) + (EWRmedio_playlists.playlist_views * ?)) AS popular_score,
				ROUND(((EWRmedio_playlists.playlist_likes * ?) + (EWRmedio_playlists.playlist_views * ?)) / CEIL((? - EWRmedio_playlists.playlist_update) / ?), 2) AS trending_score
			FROM EWRmedio_playlists
				LEFT JOIN xf_user ON (xf_user.user_id = EWRmedio_playlists.user_id)
			$onlyWhere
				$visible
			ORDER BY $orderBy, EWRmedio_playlists.playlist_update DESC, EWRmedio_playlists.playlist_id DESC
			LIMIT ?, ?
		", array(
			$options->EWRmedio_trendlike, $options->EWRmedio_trendview,
			$options->EWRmedio_trendlike, $options->EWRmedio_trendview,
			XenForo_Application::$time, $options->EWRmedio_trendplist, $start, $stop
		));

		foreach ($playlists AS &$playlist)
		{
			$medias = explode(",", $playlist['playlist_media']);
			$playlist['count'] = empty($playlist['playlist_media']) ? 0 : count($medias);
			
			foreach ($medias AS $media)
			{
				$playlist['media'][]['media_id'] = $media;
			}
		}

        return $playlists;
	}

	public function getPlaylistByID($playID)
	{
		if (!$playlist = $this->_getDb()->fetchRow("
			SELECT EWRmedio_playlists.*, xf_user.*, xf_liked_content.like_date
				FROM EWRmedio_playlists
				LEFT JOIN xf_user ON (xf_user.user_id = EWRmedio_playlists.user_id)
				LEFT JOIN xf_liked_content
					ON (xf_liked_content.content_type = 'media_playlist'
						AND xf_liked_content.content_id = EWRmedio_playlists.playlist_id
						AND xf_liked_content.like_user_id = " .$this->_getDb()->quote(XenForo_Visitor::getUserId()) . ")
			WHERE EWRmedio_playlists.playlist_id = ?
		", $playID))
		{
			return false;
		}

		if ($playlist['likes'] = $playlist['playlist_likes'])
		{
			$playlist['likeUsers'] = unserialize($playlist['playlist_like_users']);
		}

        return $playlist;
	}

	public function getPlaylistsByIDs($playIDs)
	{
		if (!$playlists = $this->fetchAllKeyed("
			SELECT EWRmedio_playlists.*, xf_user.*
				FROM EWRmedio_playlists
				LEFT JOIN xf_user ON (xf_user.user_id = EWRmedio_playlists.user_id)
			WHERE playlist_id IN (" . $this->_getDb()->quote($playIDs) . ")
		", 'playlist_id'))
		{
			return array();
		}

        return $playlists;
	}

	public function getPlaylistByUserID($userID = 0)
	{
		$userID = $userID ? $userID : XenForo_Visitor::getUserId();

		if (!$playlists = $this->_getDb()->fetchAll("
			SELECT *
				FROM EWRmedio_playlists
			WHERE user_id = ?
			ORDER BY playlist_update DESC
		", $userID))
		{
			return false;
		}

        return $playlists;
	}

	public function getMediaByPlaylist($playlist)
	{
		if (!$playlist['playlist_media']) { return array(); }

		$medias = $this->fetchAllKeyed("
			SELECT EWRmedio_media.*, EWRmedio_categories.*, EWRmedio_services.*, xf_user.*,
				IF(NOT ISNULL(xf_user.user_id), xf_user.username, EWRmedio_media.username) AS username
				FROM EWRmedio_media
				LEFT JOIN EWRmedio_categories ON (EWRmedio_categories.category_id = EWRmedio_media.category_id)
				LEFT JOIN EWRmedio_services ON (EWRmedio_services.service_id = EWRmedio_media.service_id)
				LEFT JOIN xf_user ON (xf_user.user_id = EWRmedio_media.user_id)
			WHERE EWRmedio_media.media_id IN (".$playlist['playlist_media'].")
			ORDER BY FIELD(EWRmedio_media.media_id, ".$playlist['playlist_media'].")
		", 'media_id');

		foreach ($medias AS &$media)
		{
			$media = $this->getModelFromCache('EWRmedio_Model_Media')->getDuration($media);
		}

        return $medias;
	}

	public function getPlaylistWithMedia($playlist, $media)
	{
		$mediaIDs = explode(',',$playlist['playlist_media']);

		if (in_array($media['media_id'], $mediaIDs))
		{
			$medias = $this->_getDb()->fetchAll("
				SELECT EWRmedio_media.*, EWRmedio_services.service_name
					FROM EWRmedio_media
					LEFT JOIN EWRmedio_services ON (EWRmedio_services.service_id = EWRmedio_media.service_id)
				WHERE EWRmedio_media.media_id IN (".$playlist['playlist_media'].")
				ORDER BY FIELD(EWRmedio_media.media_id, ".$playlist['playlist_media'].")
			");

			foreach ($medias AS $key => $exists)
			{
				if ($exists['media_id'] == $media['media_id'])
				{
					$now = $key;
					break;
				}
			}
			$playlist['count'] = count($medias);

			if (!empty($medias[$now-1]))
			{
				$playlist['prev'] = $medias[$now-1];
				$playlist['prev'] = $this->getModelFromCache('EWRmedio_Model_Media')->getDuration($playlist['prev']);
			}

			if (!empty($medias[$now+1]))
			{
				$playlist['next'] = $medias[$now+1];
				$playlist['next'] = $this->getModelFromCache('EWRmedio_Model_Media')->getDuration($playlist['next']);
			}
		}
		else
		{
			return false;
		}

		return $playlist;
	}

	public function updatePlaylist($input)
	{
		$dw = XenForo_DataWriter::create('EWRmedio_DataWriter_Playlists');

		if (!empty($input['playlist_id']) && $playlist = $this->getPlaylistByID($input['playlist_id']))
		{
			$dw->setExistingData($playlist);
			$dw->set('playlist_media', implode(',',$input['playlist_media']));
		}
		else
		{
			if (!empty($input['media_id']))
			{
				$dw->set('playlist_media', $input['media_id']);
			}
		}
		
		$dw->bulkSet(array(
			'playlist_name' => $input['playlist_name'],
			'playlist_state' => $input['playlist_state'],
			'playlist_description' => XenForo_Helper_String::autoLinkBbCode($input['playlist_description']),
		));
		$dw->save();
		
		$input['playlist_id'] = $dw->get('playlist_id');
		$input['playlist_media'] = $dw->get('playlist_media');

		return $input;
	}

	public function updateViews($playlist)
	{
		if (empty($_COOKIE['EWRmedio_playlist_'.$playlist['playlist_id']]))
		{
			$this->_getDb()->query("
				UPDATE EWRmedio_playlists
				SET playlist_views = playlist_views+1
				WHERE playlist_id = ?
			", $playlist['playlist_id']);

			$playlist['playlist_views']++;
			
			setcookie('EWRmedio_playlist_'.$playlist['playlist_id'], '1', time()+86400);
		}

		return $playlist;
	}

	public function deletePlaylist($input)
	{
		$contentIds = array($input['playlist_id']);

		$this->getModelFromCache('XenForo_Model_Like')->deleteContentLikes('media_playlist', $contentIds);
		$this->getModelFromCache('XenForo_Model_NewsFeed')->delete('media_playlist', $input['playlist_id']);
		
		$dw = XenForo_DataWriter::create('EWRmedio_DataWriter_Playlists');
		$dw->setExistingData($input);
		$dw->delete();

		return true;
	}
	
	public function addToPlaylist($playlist, $input)
	{
		$mediaIDs = explode(',',$playlist['playlist_media']);
		$mediaIDs[] = $input['media_id'];
		
		foreach ($mediaIDs AS $key => $mediaID)
		{
			if (empty($mediaID))
			{
				unset($mediaIDs[$key]);
			}
		}
		
		$playlist['playlist_media'] = implode(',',$mediaIDs);
		
		$dw = XenForo_DataWriter::create('EWRmedio_DataWriter_Playlists');
		$dw->setExistingData($playlist);
		$dw->set('playlist_media', $playlist['playlist_media']);
		$dw->save();

		return $playlist;
	}
}