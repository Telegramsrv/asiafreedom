<?php

class EWRmedio_ControllerPublic_Media extends XenForo_ControllerPublic_Abstract
{
	public $perms;

	public function actionIndex()
	{
		if ($mediaID = $this->_input->filterSingle('action_id', XenForo_Input::UINT))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media/media', array('media_id' => $mediaID)));
		}
		
		$this->canonicalizeRequestUrl(XenForo_Link::buildPublicLink('media'));
		
		$options = XenForo_Application::get('options');
		
		$listings = array();
		foreach ($options->EWRmedio_splash AS $key => $listing)
		{
			$listings[$key]['type'] = $listing['type'];
			$listings[$key]['name'] = new XenForo_Phrase($listing['sort'].'_'.$listing['type']);
			
			if ($listing['type'] == 'playlists')
			{
				$listings[$key]['bits'] = $this->getModelFromCache('EWRmedio_Model_Playlists')->getPlaylists(1, $listing['count'], $listing);
			}
			else
			{
				$listings[$key]['bits'] = $this->getModelFromCache('EWRmedio_Model_Lists')->getMediaList(1, $listing['count'], $listing);
			}
		}

		$viewParams = array(
			'perms' => $this->perms,
			'listings' => $listings,
			'sidebar' => $this->getModelFromCache('EWRmedio_Model_Parser')->parseSidebar(),
		);

		return $this->responseView('EWRmedio_ViewPublic_Media', 'EWRmedio_Media', $viewParams);
	}
	
	public function actionMedias()
	{
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

		$this->canonicalizeRequestUrl(XenForo_Link::buildPublicLink('media/medias', array('page' => $start)));
		$this->canonicalizePageNumber($start, $stop, $count, 'media/medias');
		
		$listParams['fuser'] = $fuserText;
		$listParams['filter'] = $filterText;
		
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
			'booruKeys' => $options->EWRmedio_displaybooru ? $this->getModelFromCache('EWRmedio_Model_Keywords')->getKeywordsByMedias(array_keys($media)) : false,
			'booruUsers' => $options->EWRmedio_displaybooru ? $this->getModelFromCache('EWRmedio_Model_Userlinks')->getUsernamesByMedias(array_keys($media)) : false,
			'customs' => $this->getModelFromCache('EWRmedio_Model_Custom')->getCustomOptions($customParams),
			'linkParams' => $listParams,
			'media' => $media,
			'sidebar' => $this->getModelFromCache('EWRmedio_Model_Parser')->parseSidebar(),
		);

		return $this->responseView('EWRmedio_ViewPublic_Medias', 'EWRmedio_Medias', $viewParams);
	}
	
	public function actionPlaylists()
	{
		$options = XenForo_Application::get('options');
		$sort = $this->_input->filterSingle('sort', XenForo_Input::STRING);
		$order = $this->_input->filterSingle('order', XenForo_Input::STRING);
		
		$start = max(1, $this->_input->filterSingle('page', XenForo_Input::UINT));
		$stop = $options->EWRmedio_mediacount;
		$count = $this->getModelFromCache('EWRmedio_Model_Playlists')->getPlaylistsCount();

		$this->canonicalizeRequestUrl(XenForo_Link::buildPublicLink('media/playlists', array('page' => $start)));
		$this->canonicalizePageNumber($start, $stop, $count, 'media/playlists');
		
		$listParams = array(
			'sort' => $sort,
			'order' => $order,
		);
		
		$viewParams = array(
			'perms' => $this->perms,
			'start' => $start,
			'stop' => $stop,
			'count' => $count,
			'linkParams' => array('sort' => $sort, 'order' => $order),
			'playlists' => $this->getModelFromCache('EWRmedio_Model_Playlists')->getPlaylists($start, $stop, $listParams),
			'sidebar' => $this->getModelFromCache('EWRmedio_Model_Parser')->parseSidebar(),
		);

		return $this->responseView('EWRmedio_ViewPublic_Playlists', 'EWRmedio_Playlists', $viewParams);
	}
	
	public function actionQueue()
	{
		if (!$this->perms['mod']) { return $this->responseNoPermission(); }
		
		if ($this->_request->isPost())
		{
			$input = $this->_input->filter(array(
				'action_id' => XenForo_Input::UINT,
				'approve' => XenForo_Input::STRING,
				'delete' => XenForo_Input::STRING,
			));

			if (!$media = $this->getModelFromCache('EWRmedio_Model_Media')->getMediaByID($input['action_id']))
			{
				return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media/queue'));
			}
		
			if ($input['approve'])
			{
				$this->getModelFromCache('EWRmedio_Model_Media')->approveMedia($media);
			}
			else if ($input['delete'])
			{
				$this->getModelFromCache('EWRmedio_Model_Media')->deleteMedia($media);
				
				if ($this->getModelFromCache('XenForo_Model_Thread')->getThreadById($media['thread_id']))
				{
					$this->getModelFromCache('XenForo_Model_Thread')->deleteThread($media['thread_id'], 'hard');
				}
			}
		
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media/queue'));
		}
		
		$this->canonicalizeRequestUrl(XenForo_Link::buildPublicLink('media/queue'));

		$viewParams = array(
			'queues' => $this->getModelFromCache('EWRmedio_Model_Lists')->getMediaQueue(),
		);
		
		return $this->responseView('EWRmedio_ViewPublic_Queue', 'EWRmedio_Queue', $viewParams);
	}

	public function actionRebuildYouTubeThumbs()
	{
		if (!$this->perms['admin']) { return $this->responseNoPermission(); }

		$db = XenForo_Application::get('db');
		$start = $this->_input->filterSingle('start', XenForo_Input::UINT);
		$stop = 5;

		if (!$medias = $db->fetchAll("
			SELECT EWRmedio_media.*, EWRmedio_services.*
				FROM EWRmedio_media
				LEFT JOIN EWRmedio_services ON (EWRmedio_services.service_id = EWRmedio_media.service_id)
			WHERE EWRmedio_services.service_name = 'YouTube'
				AND EWRmedio_media.media_id > ?
			ORDER BY media_id ASC
			LIMIT ?
		", array($start, $stop)))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media'));
		}

		foreach ($medias AS &$media)
		{
			$start = $media['media_id'];

			echo $media['media_id']." - ".$media['media_title'];
			echo '<blockquote>';

			$client = new Zend_Http_Client('http://gdata.youtube.com/feeds/api/videos/'.$media['service_value'].'?v=2&alt=json');
			
			try
			{
				$feed = $client->request()->getBody();
			}
			catch (Exception $e)
			{
				echo "<b>ERROR</b> : ".new XenForo_Phrase('media_url_did_not_retrieve_valid_data')." - WAITING...</blockquote>";
				exit;
			}
			
			$json = json_decode($feed, true);
		
			if (empty($json))
			{
				echo "<b>ERROR</b> : ".new XenForo_Phrase('media_url_did_not_retrieve_valid_data')." - WAITING...</blockquote>";
				exit;
			}
			
			$e = !empty($json['entry']['app$control']['yt$state']) ? $json['entry']['app$control']['yt$state'] : null;
			$error = !empty($e) && $e['name'] != 'restricted' && $e['name'] != 'processing' ? $e['$t'] : null;

			if ($error)
			{
				echo "<b>ERROR</b> : ".$error." - WAITING...</blockquote>";
				exit;
			}
			
			$thumb = $json['entry']['media$group']['media$thumbnail']['2']['url'];

			if (!$thumb)
			{
				$this->getModelFromCache('EWRmedio_Model_Media')->deleteMedia($media);
				echo "<b>ERROR</b> : NO THUMB - DELETING MEDIA...</blockquote>";
				continue;
			}
		
			if ($json['entry']['media$group']['media$thumbnail']['3']['yt$name'] == 'sddefault')
			{
				$thumb = $json['entry']['media$group']['media$thumbnail']['3']['url'];
			}

			$this->getModelFromCache('EWRmedio_Model_Thumbs')->buildThumb($media['media_id'], $thumb);
			echo '<img src="'.XenForo_Link::buildPublicLink('full:data/media').'/'.$media['media_id'].'.jpg">';
			echo '</blockquote>';
		}

		echo '<meta http-equiv="refresh" content="2;url='.XenForo_Link::buildPublicLink('full:media/rebuild-youtube-thumbs').'?start='.($start).'">';
		exit;
	}

	public function actionRss()
	{
		$this->_routeMatch->setResponseType('rss');

		$viewParams = array(
			'rss' => $this->getModelFromCache('EWRmedio_Model_Sitemaps')->getRSSbyMedia(),
		);

		return $this->responseView('EWRmedio_ViewPublic_RSS', '', $viewParams);
	}

	public function actionCategories()
	{
		$viewParams = array(
			'perms' => $this->perms,
			'catList' => $this->getModelFromCache('EWRmedio_Model_Lists')->getCategoryList(),
			'sidebar' => $this->getModelFromCache('EWRmedio_Model_Parser')->parseSidebar(),
		);

		return $this->responseView('EWRmedio_ViewPublic_Categories', 'EWRmedio_Categories', $viewParams);
	}

	public function actionRandom()
	{
		$media = $this->getModelFromCache('EWRmedio_Model_Media')->getRandom();

		return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media/media', $media));
	}
	
	public function actionSubmitSmall()
	{
		if (!$this->perms['submit']) { return $this->responseNoPermission(); }
		
		return $this->responseView('EWRmedio_ViewPublic_SubmitSmall', 'EWRmedio_Submit_Small');
	}
	
	public function actionSubmitSmallBulk()
	{
		if (!$this->perms['bulk']) { return $this->responseNoPermission(); }
		
		return $this->responseView('EWRmedio_ViewPublic_SubmitSmallBulk', 'EWRmedio_Submit_SmallBulk');
	}

	public function actionSubmit()
	{
		if (!$this->perms['submit']) { return $this->responseNoPermission(); }

		if ($this->_request->isPost())
		{
			if ($source = $this->_input->filterSingle('source', XenForo_Input::STRING))
			{
				$media = $this->getModelFromCache('EWRmedio_Model_Submit')->fetchFeedInfo($source);
			}
			else
			{
				$input = $this->_input->filter(array(
					'category_id' => XenForo_Input::UINT,
					'service_id' => XenForo_Input::UINT,
					'service_value' => XenForo_Input::STRING,
					'service_value2' => XenForo_Input::STRING,
					'media_thumb' => XenForo_Input::STRING,
					'media_title' => XenForo_Input::STRING,
					'media_hours' => XenForo_Input::UINT,
					'media_minutes' => XenForo_Input::UINT,
					'media_seconds' => XenForo_Input::UINT,
					'media_keywords' => XenForo_Input::STRING,
					'media_keyarray' => XenForo_Input::ARRAY_SIMPLE,
					'c1' => XenForo_Input::STRING,
					'c2' => XenForo_Input::STRING,
					'c3' => XenForo_Input::STRING,
					'c4' => XenForo_Input::STRING,
					'c5' => XenForo_Input::STRING,
					'media_usernames' => XenForo_Input::STRING,
					'media_node' => XenForo_Input::UINT,
					'create_thread' => XenForo_Input::UINT,
					'submit' => XenForo_Input::STRING,
				));
				$input['bypass'] = $this->perms['bypass'];
				$input['media_description'] = $this->getHelper('Editor')->getMessageText('media_description', $this->_input);

				if (!empty($input['media_keyarray']))
				{
					$input['media_keywords'] = implode(',', $input['media_keyarray']);
				}

				$media = $this->getModelFromCache('EWRmedio_Model_Media')->updateMedia($input);
				return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media/media', $media));
			}
		}

		$options = XenForo_Application::get('options');
		$forums = array();

		foreach ($options->EWRmedio_autoforum AS $forum)
		{
			$forum = $this->getModelFromCache('XenForo_Model_Forum')->getForumById($forum);

			if ($forum && $this->getModelFromCache('XenForo_Model_Forum')->canPostThreadInForum($forum))
			{
				$forums[] = $forum;
			}
		}

		$viewParams = array(
			'media' => !empty($media) ? $media : false,
			'customs' => $this->getModelFromCache('EWRmedio_Model_Custom')->getCustomOptions(),
			'forums' => $forums,
			'checked' => $options->EWRmedio_autocheck ? 'checked' : '',
			'fullList' => $this->getModelFromCache('EWRmedio_Model_Lists')->getCategoryList(),
		);
		
		if (!$options->EWRmedio_newkeywords)
		{
			$viewParams['keywords'] = $this->getModelFromCache('EWRmedio_Model_Keywords')->getAllKeywords();
		}

		return $this->responseView('EWRmedio_ViewPublic_Submit', 'EWRmedio_Submit', $viewParams);
	}
	
	public function actionSubmitBulk()
	{
		if (!$this->perms['bulk']) { return $this->responseNoPermission(); }
		
		if ($this->_request->isPost())
		{
			if ($source = $this->_input->filterSingle('source', XenForo_Input::STRING))
			{
				$params = $this->_input->filter(array(
					'start' => XenForo_Input::UINT,
					'count' => XenForo_Input::UINT,
				));
				
				list($medias, $params) = $this->getModelFromCache('EWRmedio_Model_Submit')->fetchBulkInfo($source, $params);
			}
			else
			{
				$input = $this->_input->filter(array(
					'medias' => XenForo_Input::ARRAY_SIMPLE,
					'category_id' => XenForo_Input::UINT,
					'c1' => XenForo_Input::STRING,
					'c2' => XenForo_Input::STRING,
					'c3' => XenForo_Input::STRING,
					'c4' => XenForo_Input::STRING,
					'c5' => XenForo_Input::STRING,
					'playlist_id' => XenForo_Input::UINT,
					'submit' => XenForo_Input::STRING,
				));
				
				if (empty($input['category_id']))
				{
					throw new XenForo_Exception(new XenForo_Phrase('select_default_bulk_category'), true);
				}
				
				$playlist = $this->getModelFromCache('EWRmedio_Model_Playlists')->getPlaylistByID($input['playlist_id']);
				
				foreach ($input['medias'] AS $media)
				{
					if (empty($media['submit'])) { continue; }
				
					$media['category_id'] = $media['category_id'] ? $media['category_id'] : $input['category_id'];
					$media['c1'] = !empty($media['c1']) ? $media['c1'] : $input['c1'];
					$media['c2'] = !empty($media['c2']) ? $media['c2'] : $input['c2'];
					$media['c3'] = !empty($media['c3']) ? $media['c3'] : $input['c3'];
					$media['c4'] = !empty($media['c4']) ? $media['c4'] : $input['c4'];
					$media['c5'] = !empty($media['c5']) ? $media['c5'] : $input['c5'];
					$media['bypass'] = $this->perms['bypass'];

					if (!empty($media['media_keyarray']))
					{
						$media['media_keywords'] = implode(',', $media['media_keyarray']);
					}

					$media = $this->getModelFromCache('EWRmedio_Model_Media')->updateMedia($media);
			
					if ($playlist)
					{
						$addTo = array('media_id' => $media['media_id']);
						$playlist = $this->getModelFromCache('EWRmedio_Model_Playlists')->addToPlaylist($playlist, $addTo);
					}
				}
				
				if ($playlist)
				{
					return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media/playlist', $playlist));
				}
				else
				{
					return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media/medias'));
				}
			}
		}

		$options = XenForo_Application::get('options');

		$viewParams = array(
			'source' => !empty($source) ? $source : false,
			'params' => !empty($params) ? $params : false,
			'medias' => !empty($medias) ? $medias : false,
			'customs' => $this->getModelFromCache('EWRmedio_Model_Custom')->getCustomOptions(),
			'fullList' => $this->getModelFromCache('EWRmedio_Model_Lists')->getCategoryList(),
			'playlistList' => $this->getModelFromCache('EWRmedio_Model_Playlists')->getPlaylistByUserID(),
		);
		
		if (!$options->EWRmedio_newkeywords)
		{
			$viewParams['keywords'] = $this->getModelFromCache('EWRmedio_Model_Keywords')->getAllKeywords();
		}

		return $this->responseView('EWRmedio_ViewPublic_SubmitBulk', 'EWRmedio_Submit_Bulk', $viewParams);
	}

	public static function getSessionActivityDetailsForList(array $activities)
	{
        $output = array();
        foreach ($activities as $key => $activity)
		{
			$output[$key] = new XenForo_Phrase('viewing_media_library');
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