<?php

class EWRmedio_ControllerPublic_Media_User extends XenForo_ControllerPublic_Abstract
{
	public $perms;

	public function actionUser()
	{
		$userID = $this->_input->filterSingle('action_id', XenForo_Input::UINT);

		if (!$user = $this->getModelFromCache('XenForo_Model_User')->getUserById($userID))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media'));
		}

		$options = XenForo_Application::get('options');
		
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
			'type' => 'user',
			'where' => $user['user_id'],
			'sort' => $sort,
			'order' => $order,
			'fuser' => $fuser,
			'filter' => $filter,
			'c1' => $input['c1'],
			'c2' => $input['c2'],
			'c3' => $input['c3'],
			'c4' => $input['c4'],
			'c5' => $input['c5'],
		);
		
		$start = max(1, $this->_input->filterSingle('page', XenForo_Input::UINT));
		$stop = $options->EWRmedio_mediacount;
		$count = $this->getModelFromCache('EWRmedio_Model_Lists')->getMediaCount($listParams);
		$media = $this->getModelFromCache('EWRmedio_Model_Lists')->getMediaList($start, $stop, $listParams);

		$this->canonicalizeRequestUrl(XenForo_Link::buildPublicLink('media/user', $user, array('page' => $start)));
		$this->canonicalizePageNumber($start, $stop, $count, 'media/user', $user);
		
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
			'user' => $user,
			'start' => $start,
			'stop' => $stop,
			'count' => $count,
			'fusers' => $fuser,
			'fuserText' => $fuserText,
			'filters' => $filter,
			'filterText' => $filterText,
			'booruKeys' => $options->EWRmedio_displaybooru ? $this->getModelFromCache('EWRmedio_Model_Keywords')->getKeywordsByMedias(array_keys($media)) : false,
			'booruUsers' => $options->EWRmedio_displaybooru ? $this->getModelFromCache('EWRmedio_Model_Userlinks')->getUsernamesByMedias(array_keys($media)) : false,
			'customs' => $this->getModelFromCache('EWRmedio_Model_Custom')->getCustomOptions($customParams),
			'linkParams' => $listParams,
			'mediaList' => $media,
		);

		if ($this->_noRedirect())
		{
			return $this->responseView('EWRmedio_ViewPublic_UserView', 'EWRmedio_UserView_Simple', $viewParams);
		}
		else
		{
			$viewParams['sidebar'] = $this->getModelFromCache('EWRmedio_Model_Parser')->parseSidebar();
			return $this->responseView('EWRmedio_ViewPublic_UserView', 'EWRmedio_UserView', $viewParams);
		}
	}
	
	public function actionUserPlaylists()
	{
		$userID = $this->_input->filterSingle('action_id', XenForo_Input::UINT);

		if (!$user = $this->getModelFromCache('XenForo_Model_User')->getUserById($userID))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media'));
		}
		
		$options = XenForo_Application::get('options');
		$sort = $this->_input->filterSingle('sort', XenForo_Input::STRING);
		$order = $this->_input->filterSingle('order', XenForo_Input::STRING);
		
		$listParams = array(
			'sort' => $sort,
			'order' => $order,
			'type' => 'user',
			'where' => $user['user_id']
		);
		
		$start = max(1, $this->_input->filterSingle('page', XenForo_Input::UINT));
		$stop = $options->EWRmedio_mediacount;
		$count = $this->getModelFromCache('EWRmedio_Model_Playlists')->getPlaylistsCount($listParams);

		$this->canonicalizeRequestUrl(XenForo_Link::buildPublicLink('media/user/playlists', $user, array('page' => $start)));
		$this->canonicalizePageNumber($start, $stop, $count, 'media/user/playlists', $user);
		
		$viewParams = array(
			'perms' => $this->perms,
			'user' => $user,
			'start' => $start,
			'stop' => $stop,
			'count' => $count,
			'linkParams' => array('sort' => $sort, 'order' => $order),
			'playlists' => $this->getModelFromCache('EWRmedio_Model_Playlists')->getPlaylists($start, $stop, $listParams),
			'sidebar' => $this->getModelFromCache('EWRmedio_Model_Parser')->parseSidebar(),
		);

		return $this->responseView('EWRmedio_ViewPublic_UserPlaylists', 'EWRmedio_UserPlaylists', $viewParams);
	}

	public function actionUserRss()
	{
		$userID = $this->_input->filterSingle('action_id', XenForo_Input::UINT);

		if (!$user = $this->getModelFromCache('XenForo_Model_User')->getUserById($userID))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media'));
		}

		$this->_routeMatch->setResponseType('rss');

		$viewParams = array(
			'rss' => $this->getModelFromCache('EWRmedio_Model_Sitemaps')->getRSSbyMedia(null, 'user', $user['user_id']),
		);

		return $this->responseView('EWRmedio_ViewPublic_RSS', '', $viewParams);
	}

	public static function getSessionActivityDetailsForList(array $activities)
	{
		return new XenForo_Phrase('viewing_media_library');
	}

	protected function _preDispatch($action)
	{
		parent::_preDispatch($action);

		$this->perms = $this->getModelFromCache('EWRmedio_Model_Perms')->getPermissions();

		if (!$this->perms['browse']) { throw $this->getNoPermissionResponseException(); }
	}
}