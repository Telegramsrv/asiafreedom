<?php

class EWRmedio_ControllerPublic_Media_Playlist extends XenForo_ControllerPublic_Abstract
{
	public $perms;

	public function actionPlaylist()
	{
		$playID = $this->_input->filterSingle('action_id', XenForo_Input::UINT);

		if (!$playlist = $this->getModelFromCache('EWRmedio_Model_Playlists')->getPlaylistByID($playID))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media/playlists'));
		}

		$this->canonicalizeRequestUrl(XenForo_Link::buildPublicLink('media/playlist', $playlist));
		
		if ($playlist['playlist_state'] == 'private' && $playlist['user_id'] != XenForo_Visitor::getUserId())
		{
			return $this->responseNoPermission();
		}
		
		$options = XenForo_Application::get('options');
		$media = $this->getModelFromCache('EWRmedio_Model_Playlists')->getMediaByPlaylist($playlist);

		$viewParams = array(
			'perms' => $this->perms,
			'playlist' => $this->getModelFromCache('EWRmedio_Model_Playlists')->updateViews($playlist),
			'mediaList' => $media,
			'booruKeys' => $options->EWRmedio_displaybooru ? $this->getModelFromCache('EWRmedio_Model_Keywords')->getKeywordsByMedias(array_keys($media)) : false,
			'booruUsers' => $options->EWRmedio_displaybooru ? $this->getModelFromCache('EWRmedio_Model_Userlinks')->getUsernamesByMedias(array_keys($media)) : false,
		);

		return $this->responseView('EWRmedio_ViewPublic_PlaylistView', 'EWRmedio_PlaylistView', $viewParams);
	}
	
	public function actionPlaylistPlay()
	{
		$playID = $this->_input->filterSingle('action_id', XenForo_Input::UINT);

		if (!$playlist = $this->getModelFromCache('EWRmedio_Model_Playlists')->getPlaylistByID($playID))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media/playlists'));
		}
		
		if ($media = $this->getModelFromCache('EWRmedio_Model_Playlists')->getMediaByPlaylist($playlist))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media/media', reset($media), array('id' => $playlist['playlist_id'])));
		}
		
		return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media/playlist', $playlist));
	}

	public function actionPlaylistEdit()
	{
		if (!XenForo_Visitor::getUserId()) { throw $this->getNoPermissionResponseException(); }
		
		$playID = $this->_input->filterSingle('action_id', XenForo_Input::UINT);

		if (!$playlist = $this->getModelFromCache('EWRmedio_Model_Playlists')->getPlaylistByID($playID))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media'));
		}
		
		if (!$this->perms['mod'] && $playlist['user_id'] !== XenForo_Visitor::getUserId())
		{
			return $this->responseNoPermission();
		}

		if ($this->_request->isPost())
		{
			$input = $this->_input->filter(array(
				'playlist_name' => XenForo_Input::STRING,
				'playlist_state' => XenForo_Input::STRING,
				'playlist_media' => XenForo_Input::ARRAY_SIMPLE,
				'submit' => XenForo_Input::STRING,
			));
			$input['playlist_id'] = $playlist['playlist_id'];
			$input['playlist_description'] = $this->getHelper('Editor')->getMessageText('playlist_description', $this->_input);
			
			if (!$this->perms['playlist'] && $input['playlist_state'] == 'public')
			{
				$input['playlist_state'] = 'unlisted';
			}

			$playlist = $this->getModelFromCache('EWRmedio_Model_Playlists')->updatePlaylist($input);
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media/playlist', $playlist));
		}

		$viewParams = array(
			'perms' => $this->perms,
			'playlist' => $playlist,
			'mediaList' => $this->getModelFromCache('EWRmedio_Model_Playlists')->getMediaByPlaylist($playlist),
		);

		return $this->responseView('EWRmedio_ViewPublic_PlaylistEdit', 'EWRmedio_PlaylistEdit', $viewParams);
	}

	public function actionPlaylistDelete()
	{
		if (!XenForo_Visitor::getUserId()) { throw $this->getNoPermissionResponseException(); }
		
		$playID = $this->_input->filterSingle('action_id', XenForo_Input::UINT);

		if ($playlist = $this->getModelFromCache('EWRmedio_Model_Playlists')->getPlaylistByID($playID))
		{
			if (!$this->perms['mod'] && $playlist['user_id'] !== XenForo_Visitor::getUserId())
			{
				return $this->responseNoPermission();
			}
		
			if ($this->_request->isPost())
			{
				$this->getModelFromCache('EWRmedio_Model_Playlists')->deletePlaylist($playlist);
			}
			else
			{
				return $this->responseView('EWRmedio_ViewPublic_PlaylistDelete', 'EWRmedio_PlaylistDelete', array('playlist' => $playlist));
			}
		}

		return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media'));
	}

	public function actionPlaylistAddto()
	{
		if (!XenForo_Visitor::getUserId()) { throw $this->getNoPermissionResponseException(); }
		
		if ($this->_request->isPost())
		{
			$input = $this->_input->filter(array(
				'playlist_id' => XenForo_Input::UINT,
				'media_id' => XenForo_Input::UINT,
				'media_url' => XenForo_Input::STRING,
				'submit' => XenForo_Input::STRING,
			));

			if ($input['media_url'])
			{
				$input['playlist_id'] = $this->_input->filterSingle('action_id', XenForo_Input::UINT);
		
				if (preg_match('#/[^/]*?([\d]+)/media#i', $input['media_url'], $matches))
				{
					$input['media_id'] = $matches[1];
				}
			}
			
			if (!$playlist = $this->getModelFromCache('EWRmedio_Model_Playlists')->getPlaylistByID($input['playlist_id']))
			{
				return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media/playlist/create', false, array('id' => $input['media_id'])));
			}
			
			if (!$this->perms['mod'] && $playlist['user_id'] !== XenForo_Visitor::getUserId())
			{
				return $this->responseNoPermission();
			}

			if ($input['media_id'])
			{
				$this->getModelFromCache('EWRmedio_Model_Playlists')->addToPlaylist($playlist, $input);
			}

			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media/playlist', $playlist));
		}

		return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media'));
	}

	public function actionPlaylistLikes()
	{
		$playID = $this->_input->filterSingle('action_id', XenForo_Input::UINT);

		if (!$playlist = $this->getModelFromCache('EWRmedio_Model_Playlists')->getPlaylistByID($playID))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media/playlists'));
		}

		$likes = $this->getModelFromCache('XenForo_Model_Like')->getContentLikes('media_playlist', $playlist['playlist_id']);
		if (!$likes)
		{
			return $this->responseError(new XenForo_Phrase('no_one_has_liked_this_post_yet'));
		}

		$viewParams = array(
			'playlist' => $playlist,
			'likes' => $likes
		);

		return $this->responseView('EWRmedio_ViewPublic_PlaylistLikes', 'EWRmedio_PlaylistLikes', $viewParams);
	}

	public function actionPlaylistLike()
	{
		if (!$this->perms['like']) { return $this->responseNoPermission(); }
		
		$playID = $this->_input->filterSingle('action_id', XenForo_Input::UINT);

		if (!$playlist = $this->getModelFromCache('EWRmedio_Model_Playlists')->getPlaylistByID($playID))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media/playlists'));
		}

		$viewingID = XenForo_Visitor::getUserId();

		if ($playlist['user_id'] == $viewingID)
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media/playlist', $playlist));
		}

		$existingLike = $this->getModelFromCache('XenForo_Model_Like')->getContentLikeByLikeUser('media_playlist', $playlist['playlist_id'], $viewingID);

		if ($this->_request->isPost())
		{
			if ($existingLike)
			{
				$latestUsers = $this->getModelFromCache('XenForo_Model_Like')->unlikeContent($existingLike);
			}
			else
			{
				$latestUsers = $this->getModelFromCache('XenForo_Model_Like')->likeContent('media_playlist', $playlist['playlist_id'], $playlist['user_id']);
			}

			$liked = ($existingLike ? false : true);

			if ($this->_noRedirect() && $latestUsers !== false)
			{
				$playlist['likeUsers'] = $latestUsers;
				$playlist['likes'] += ($liked ? 1 : -1);
				$playlist['like_date'] = ($liked ? XenForo_Application::$time : 0);

				$viewParams = array(
					'playlist' => $playlist,
					'liked' => $liked,
				);

				return $this->responseView('EWRmedio_ViewPublic_PlaylistLikeConfirmed', '', $viewParams);
			}
			else
			{
				return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media/playlist', $playlist));
			}
		}
		else
		{
			$viewParams = array(
				'playlist' => $playlist,
				'like' => $existingLike,
			);

			return $this->responseView('EWRmedio_ViewPublic_PlaylistLike', 'EWRmedio_PlaylistLike', $viewParams);
		}
	}

	public function actionPlaylistCreate()
	{
		if (!XenForo_Visitor::getUserId()) { throw $this->getNoPermissionResponseException(); }
		
		if ($this->_request->isPost())
		{
			$input = $this->_input->filter(array(
				'playlist_name' => XenForo_Input::STRING,
				'playlist_state' => XenForo_Input::STRING,
				'media_id' => XenForo_Input::UINT,
				'submit' => XenForo_Input::STRING,
			));
			$input['playlist_description'] = $this->getHelper('Editor')->getMessageText('playlist_description', $this->_input);
			
			if (!$this->perms['playlist'] && $input['playlist_state'] == 'public')
			{
				$input['playlist_state'] = 'unlisted';
			}

			$playlist = $this->getModelFromCache('EWRmedio_Model_Playlists')->updatePlaylist($input);
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media/playlist', $playlist));
		}

		$viewParams = array(
			'perms' => $this->perms,
			'mediaID' => $this->_input->filterSingle('id', XenForo_Input::UINT),
		);

		return $this->responseView('EWRmedio_ViewPublic_PlaylistCreate', 'EWRmedio_PlaylistCreate', $viewParams);
	}

	public static function getSessionActivityDetailsForList(array $activities)
	{
		$playIDs = array();
		foreach ($activities AS $activity)
		{
			if (!empty($activity['params']['playlist_id']))
			{
				$playIDs[$activity['params']['playlist_id']] = $activity['params']['playlist_id'];
			}
		}

		$playlistData = array();
		if ($playIDs)
		{
			$playModel = XenForo_Model::create('EWRmedio_Model_Playlists');
			$playlists = $playModel->getPlaylistsByIDs($playIDs);

			foreach ($playlists AS $playlist)
			{
				$playlistData[$playlist['playlist_id']] = array(
					'title' => $playlist['playlist_name'],
					'url' => XenForo_Link::buildPublicLink('media/playlist', $playlist)
				);
			}
		}

        $output = array();
        foreach ($activities as $key => $activity)
		{
			$playlist = false;
			if (!empty($activity['params']['playlist_id']))
			{
				$playIDs = $activity['params']['playlist_id'];
				if (isset($playlistData[$playIDs]))
				{
					$playlist = $playlistData[$playIDs];
				}
			}

			if ($playlist)
			{
				$output[$key] = array(new XenForo_Phrase('viewing_media_playlist'), $playlist['title'], $playlist['url'], false);
			}
			else
			{
				$output[$key] = new XenForo_Phrase('viewing_media_library');
			}
        }

        return $output;
	}

	protected function _preDispatch($action)
	{
		parent::_preDispatch($action);

		$this->perms = $this->getModelFromCache('EWRmedio_Model_Perms')->getPermissions();

		if (!$this->perms['browse']) { throw $this->getNoPermissionResponseException(); }
	}
}