<?php

class EWRmedio_ControllerPublic_Media_Account extends XenForo_ControllerPublic_Abstract
{
	public $perms;

	public function actionAccount()
	{
		return $this->responseReroute(__CLASS__, 'account/media');
	}
	
	public function actionAccountMedia()
	{
		$input = $this->_input->filter(array(
			'sort' => XenForo_Input::STRING,
			'order' => XenForo_Input::STRING,
			'fuser' => XenForo_Input::STRING,
			'filter' => XenForo_Input::STRING,
			'u' => XenForo_Input::STRING,
			'f' => XenForo_Input::STRING,
			'c1' => XenForo_Input::STRING,
			'c2' => XenForo_Input::STRING,
			'c3' => XenForo_Input::STRING,
			'c4' => XenForo_Input::STRING,
			'c5' => XenForo_Input::STRING,
		));
		
		$sort = $input['sort'];
		$order = $input['order'];
		$fuser = !empty($input['u']) ? $input['u'].','.$input['fuser'] : $input['fuser'];
		$filter = !empty($input['f']) ? $input['f'].','.$input['filter'] : $input['filter'];
		
		list($fuser, $fuserText) = !empty($fuser) ? $this->getModelFromCache('EWRmedio_Model_Userlinks')->prepareUsernameFilter($fuser) : array(false, false);
		list($filter, $filterText) = !empty($filter) ? $this->getModelFromCache('EWRmedio_Model_Keywords')->prepareKeywordsFilter($filter) : array(false, false);
		
		$listParams = array(
			'sort' => $sort,
			'order' => $order,
			'type' => 'account',
			'where' => XenForo_Visitor::getUserId(),
			'fuser' => $fuser,
			'filter' => $filter,
			'c1' => $input['c1'],
			'c2' => $input['c2'],
			'c3' => $input['c3'],
			'c4' => $input['c4'],
			'c5' => $input['c5'],
		);
		
		$start = max(1, $this->_input->filterSingle('page', XenForo_Input::UINT));
		$stop = 50;
		$count = $this->getModelFromCache('EWRmedio_Model_Lists')->getMediaCount($listParams);
		$media = $this->getModelFromCache('EWRmedio_Model_Lists')->getMediaList($start, $stop, $listParams);

		$this->canonicalizeRequestUrl(XenForo_Link::buildPublicLink('media/account/media', '', array('page' => $start)));
		$this->canonicalizePageNumber($start, $stop, $count, 'media/account/media');
		
		$listParams['fuser'] = $fuserText;
		$listParams['filter'] = $filterText;
		unset($listParams['type']);
		unset($listParams['where']);
		
		$customParams = array(
			'media_custom1' => $listParams['c1'],
			'media_custom2' => $listParams['c2'],
			'media_custom3' => $listParams['c3'],
			'media_custom4' => $listParams['c4'],
			'media_custom5' => $listParams['c5'],
		);

		$viewParams = array(
			'perms' => $this->perms,
			'start' => $start,
			'stop' => $stop,
			'count' => $count,
			'fusers' => $fuser,
			'fuserText' => $fuserText,
			'filters' => $filter,
			'filterText' => $filterText,
			'customs' => $this->getModelFromCache('EWRmedio_Model_Custom')->getCustomOptions($customParams),
			'linkParams' => $listParams,
			'mediaList' => $media,
			'playlistList' => $this->getModelFromCache('EWRmedio_Model_Playlists')->getPlaylistByUserID(XenForo_Visitor::getUserId()),
		);

		return $this->_getWrapper('media', $this->responseView('EWRmedio_ViewPublic_AccountMedia', 'EWRmedio_AccountMedia', $viewParams));
	}
	
	public function actionAccountMediaBulk()
	{
		$this->_assertPostOnly();
		
		$input = $this->_input->filter(array(
			'goBulk' => XenForo_Input::STRING,
			'goPlaylist' => XenForo_Input::STRING,
			'bulk_option' => XenForo_Input::STRING,
			'playlist_id' => XenForo_Input::UINT,
			'media' => XenForo_Input::ARRAY_SIMPLE,
		));
		
		if (empty($input['media']))
		{
			throw new XenForo_Exception(new XenForo_Phrase('you_have_not_selected_valid_media'), true);
		}
		
		$medias = $this->getModelFromCache('EWRmedio_Model_Media')->getMediasByIDs($input['media']);
		$mediaIDs = array();
		
		foreach ($medias AS $key => $media)
		{
			if ($media['user_id'] == XenForo_Visitor::getUserId())
			{
				$mediaIDs[] = $media['media_id'];
			}
			else
			{
				unset($medias[$key]);
			}
		}
		
		if (empty($mediaIDs))
		{
			throw new XenForo_Exception(new XenForo_Phrase('you_have_not_selected_valid_media'), true);
		}
		
		if ($input['goPlaylist'])
		{
			if (!$input['playlist_id'] ||
				(!$playlist = $this->getModelFromCache('EWRmedio_Model_Playlists')->getPlaylistByID($input['playlist_id'])) ||
				($playlist['user_id'] !== XenForo_Visitor::getUserId()))
			{
				throw new XenForo_Exception(new XenForo_Phrase('you_have_not_selected_valid_playlist'), true);
			}
			
			$input['media_id'] = implode(',', array_reverse($mediaIDs));
			$this->getModelFromCache('EWRmedio_Model_Playlists')->addToPlaylist($playlist, $input);
		}
		
		if ($input['goBulk'])
		{
			if (!$input['bulk_option'])
			{
				throw new XenForo_Exception(new XenForo_Phrase('you_have_not_selected_valid_option'), true);
			}
			
			foreach ($medias AS $media)
			{
				switch ($input['bulk_option'])
				{
					case 'delete':
						$this->getModelFromCache('EWRmedio_Model_Media')->deleteMedia($media);
						break;
				}
			}
		}
		
		return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media/account/media'));
	}
	
	public function actionAccountPlaylists()
	{
		$sort = $this->_input->filterSingle('sort', XenForo_Input::STRING);
		$order = $this->_input->filterSingle('order', XenForo_Input::STRING);
		
		$listParams = array(
			'sort' => $sort,
			'order' => $order,
			'type' => 'account',
			'where' => XenForo_Visitor::getUserId()
		);
		
		$start = max(1, $this->_input->filterSingle('page', XenForo_Input::UINT));
		$stop = 50;
		$count = $this->getModelFromCache('EWRmedio_Model_Playlists')->getPlaylistsCount($listParams);

		$this->canonicalizeRequestUrl(XenForo_Link::buildPublicLink('media/account/playlists', '', array('page' => $start)));
		$this->canonicalizePageNumber($start, $stop, $count, 'media/account/playlists');

		$viewParams = array(
			'perms' => $this->perms,
			'start' => $start,
			'stop' => $stop,
			'count' => $count,
			'linkParams' => array('sort' => $sort, 'order' => $order),
			'playlists' => $this->getModelFromCache('EWRmedio_Model_Playlists')->getPlaylists($start, $stop, $listParams),
		);

		return $this->_getWrapper('playlists', $this->responseView('EWRmedio_ViewPublic_AccountPlaylists', 'EWRmedio_AccountPlaylists', $viewParams));
	}
	
	public function actionAccountPlaylistsBulk()
	{
		$this->_assertPostOnly();
		
		$input = $this->_input->filter(array(
			'goBulk' => XenForo_Input::STRING,
			'bulk_option' => XenForo_Input::STRING,
			'playlists' => XenForo_Input::ARRAY_SIMPLE,
		));
			
		if (!$this->perms['playlist'] && $input['bulk_option'] == 'public')
		{
			$input['bulk_option'] = 'unlisted';
		}
		
		if (empty($input['playlists']))
		{
			throw new XenForo_Exception(new XenForo_Phrase('you_have_not_selected_valid_playlist'), true);
		}
		
		$playlists = $this->getModelFromCache('EWRmedio_Model_Playlists')->getPlaylistsByIDs($input['playlists']);
		$playlistIDs = array();
		
		foreach ($playlists AS $key => &$playlist)
		{
			if ($playlist['user_id'] == XenForo_Visitor::getUserId())
			{
				$playlist['playlist_media'] = explode(',', $playlist['playlist_media']);
				$playlistIDs[] = $playlist['playlist_id'];
			}
			else
			{
				unset($playlists[$key]);
			}
		}
		
		if (empty($playlistIDs))
		{
			throw new XenForo_Exception(new XenForo_Phrase('you_have_not_selected_valid_playlist'), true);
		}
		
		if ($input['goBulk'])
		{
			if (!$input['bulk_option'])
			{
				throw new XenForo_Exception(new XenForo_Phrase('you_have_not_selected_valid_option'), true);
			}
			
			foreach ($playlists AS &$playlist)
			{
				switch ($input['bulk_option'])
				{
					case 'unlisted':
					case 'private':
					case 'public':
						$playlist['playlist_state'] = $input['bulk_option'];
						$this->getModelFromCache('EWRmedio_Model_Playlists')->updatePlaylist($playlist);
						break;
					case 'delete':
						$this->getModelFromCache('EWRmedio_Model_Playlists')->deletePlaylist($playlist);
						break;
				}
			}
		}
		
		return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media/account/playlists'));
	}
	
	public function actionAccountLikes()
	{
		$userId = XenForo_Visitor::getUserId();
		$page = $this->_input->filterSingle('page', XenForo_Input::UINT);
		$perPage = 20;

		$likes = $this->getModelFromCache('EWRmedio_Model_Account')->getMediaLikesForContentUser($userId, array(
			'page' => $page,
			'perPage' => $perPage
		));
		$likes = $this->getModelFromCache('XenForo_Model_Like')->addContentDataToLikes($likes);

		$viewParams = array(
			'likes' => $likes,
			'totalLikes' => $this->getModelFromCache('EWRmedio_Model_Account')->countMediaLikesForContentUser($userId),
			'page' => $page,
			'likesPerPage' => $perPage
		);

		return $this->_getWrapper('likes', $this->responseView('EWRmedio_ViewPublic_AccountLikes', 'EWRmedio_AccountLikes', $viewParams));
	}

	public static function getSessionActivityDetailsForList(array $activities)
	{
		return new XenForo_Phrase('viewing_media_library');
	}

	protected function _getWrapper($selected, XenForo_ControllerResponse_View $subView)
	{
		$viewParams = array('selected' => $selected);

		$wrapper = $this->responseView('EWRmedio_ViewPublic_AccountWrapper', 'EWRmedio_AccountWrapper', $viewParams);
		$wrapper->subView = $subView;

		return $wrapper;
	}

	protected function _preDispatch($action)
	{
		parent::_preDispatch($action);

		$this->perms = $this->getModelFromCache('EWRmedio_Model_Perms')->getPermissions();

		if (!XenForo_Visitor::getUserId()) { throw $this->getNoPermissionResponseException(); }
	}
}