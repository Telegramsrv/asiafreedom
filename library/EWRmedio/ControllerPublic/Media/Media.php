<?php

class EWRmedio_ControllerPublic_Media_Media extends XenForo_ControllerPublic_Abstract
{
	public $perms;

	public function actionMedia()
	{
		$mediaID = $this->_input->filterSingle('action_id', XenForo_Input::UINT);

		if (!$media = $this->getModelFromCache('EWRmedio_Model_Media')->getMediaByID($mediaID))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media'));
		}

		if ($id = $this->_input->filterSingle('id', XenForo_Input::UINT))
		{
			if ($playlist = $this->getModelFromCache('EWRmedio_Model_Playlists')->getPlaylistByID($id))
			{
				$playlist = $this->getModelFromCache('EWRmedio_Model_Playlists')->getPlaylistWithMedia($playlist, $media);
			}
		}

		$options = XenForo_Application::get('options');
		$start = max(1, $this->_input->filterSingle('page', XenForo_Input::UINT));
		$stop = $options->EWRmedio_commentcount;
		$count = $this->getModelFromCache('EWRmedio_Model_Comments')->getCommentCount($media);

		$this->canonicalizeRequestUrl(XenForo_Link::buildPublicLink('media/media', $media, array('page' => $start)));
		$this->canonicalizePageNumber($start, $stop, $count, 'media/media', $media);

		$category = $this->getModelFromCache('EWRmedio_Model_Categories')->getCategoryByID($media['category_id']);

		$viewParams = array(
			'perms' => $this->perms,
			'start' => $start,
			'stop' => $stop,
			'playlist' => !empty($playlist) ? $this->getModelFromCache('EWRmedio_Model_Playlists')->updateViews($playlist) : false,
			'media' => $this->getModelFromCache('EWRmedio_Model_Media')->updateViews($media),
			'customs' => $this->getModelFromCache('EWRmedio_Model_Custom')->getCustomValues($media),
			'keywords' => $this->getModelFromCache('EWRmedio_Model_Keylinks')->getKeywordLinks($media),
			'users' => $this->getModelFromCache('EWRmedio_Model_Userlinks')->getUserLinks($media),
			'playlistList' => $this->getModelFromCache('EWRmedio_Model_Playlists')->getPlaylistByUserID(),
			'count' => $count,
			'comments' => $this->getModelFromCache('EWRmedio_Model_Comments')->getComments($media, $start, $stop),
			'breadCrumbs' => array_reverse($this->getModelFromCache('EWRmedio_Model_Lists')->getCrumbs($category)),
		);

		return $this->responseView('EWRmedio_ViewPublic_MediaView', 'EWRmedio_MediaView', $viewParams);
	}

	public function actionMediaRss()
	{
		$mediaID = $this->_input->filterSingle('action_id', XenForo_Input::UINT);

		if (!$media = $this->getModelFromCache('EWRmedio_Model_Media')->getMediaByID($mediaID))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media'));
		}

		$this->_routeMatch->setResponseType('rss');

		$viewParams = array(
			'rss' => $this->getModelFromCache('EWRmedio_Model_Sitemaps')->getRSSbyMedia($media),
		);

		return $this->responseView('EWRmedio_ViewPublic_RSS', '', $viewParams);
	}

	public function actionMediaPopout()
	{
		$mediaID = $this->_input->filterSingle('action_id', XenForo_Input::UINT);

		if (!$media = $this->getModelFromCache('EWRmedio_Model_Media')->getMediaByID($mediaID))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media'));
		}

		$options = XenForo_Application::get('options');
		
		if ($options->EWRmedio_commentbbcode)
		{
			$start = max(1, $this->_input->filterSingle('page', XenForo_Input::UINT));
			$stop = $options->EWRmedio_commentcount;
			$count = $this->getModelFromCache('EWRmedio_Model_Comments')->getCommentCount($media);

			$viewParams = array(
				'perms' => $this->perms,
				'start' => $start,
				'stop' => $stop,
				'count' => $count,
				'comments' => $this->getModelFromCache('EWRmedio_Model_Comments')->getComments($media, $start, $stop),
				'media' => $this->getModelFromCache('EWRmedio_Model_Media')->updateViews($media),
			);
		}
		else
		{
			$viewParams = array(
				'perms' => $this->perms,
				'media' => $this->getModelFromCache('EWRmedio_Model_Media')->updateViews($media),
			);
		}

		return $this->responseView('EWRmedio_ViewPublic_MediaPopout', 'EWRmedio_MediaPopout', $viewParams);
	}

	public function actionMediaPopoutComments()
	{
		$mediaID = $this->_input->filterSingle('action_id', XenForo_Input::UINT);

		if (!$media = $this->getModelFromCache('EWRmedio_Model_Media')->getMediaByID($mediaID))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media'));
		}

		$options = XenForo_Application::get('options');
		$start = max(1, $this->_input->filterSingle('page', XenForo_Input::UINT));
		$stop = $options->EWRmedio_commentcount;
		$count = $this->getModelFromCache('EWRmedio_Model_Comments')->getCommentCount($media);

		$viewParams = array(
			'perms' => $this->perms,
			'start' => $start,
			'stop' => $stop,
			'media' => $media,
			'count' => $count,
			'comments' => $this->getModelFromCache('EWRmedio_Model_Comments')->getComments($media, $start, $stop),
		);

		return $this->responseView('EWRmedio_ViewPublic_MediaPopoutComments', 'EWRmedio_MediaPopoutComments', $viewParams);
	}

	public function actionMediaEdit()
	{
		$mediaID = $this->_input->filterSingle('action_id', XenForo_Input::UINT);

		if (!$media = $this->getModelFromCache('EWRmedio_Model_Media')->getMediaByID($mediaID))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media'));
		}

		if (!$this->perms['mod'] && $media['user_id'] !== XenForo_Visitor::getUserId()) { return $this->responseNoPermission(); }
		if ($this->perms['admin'] || $media['user_id'] == XenForo_Visitor::getUserId()) { $this->perms['alter'] = true; }

		if ($this->_request->isPost())
		{
			$input = $this->_input->filter(array(
				'category_id' => XenForo_Input::UINT,
				'media_title' => XenForo_Input::STRING,
				'media_hours' => XenForo_Input::UINT,
				'media_minutes' => XenForo_Input::UINT,
				'media_seconds' => XenForo_Input::UINT,
				'media_keywords' => XenForo_Input::STRING,
				'media_keyarray' => XenForo_Input::ARRAY_SIMPLE,
				'media_keylinks' => XenForo_Input::ARRAY_SIMPLE,
				'media_oldlinks' => XenForo_Input::ARRAY_SIMPLE,
				'c1' => XenForo_Input::STRING,
				'c2' => XenForo_Input::STRING,
				'c3' => XenForo_Input::STRING,
				'c4' => XenForo_Input::STRING,
				'c5' => XenForo_Input::STRING,
				'media_usernames' => XenForo_Input::STRING,
				'media_newusers' => XenForo_Input::ARRAY_SIMPLE,
				'media_oldusers' => XenForo_Input::ARRAY_SIMPLE,
				'submit' => XenForo_Input::STRING,
			));
			$input['media_id'] = $media['media_id'];
			$input['media_description'] = $this->getHelper('Editor')->getMessageText('media_description', $this->_input);
			$input['bypass'] =  $this->perms['bypass'];

			if (!empty($input['media_keyarray']))
			{
				$input['media_keywords'] = implode(',', $input['media_keyarray']);
			}

			$media = $this->getModelFromCache('EWRmedio_Model_Media')->updateMedia($input);
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media/media', $media));
		}

		$category = $this->getModelFromCache('EWRmedio_Model_Categories')->getCategoryByID($media['category_id']);

		$viewParams = array(
			'perms' => $this->perms,
			'media' => $media,
			'customs' => $this->getModelFromCache('EWRmedio_Model_Custom')->getCustomOptions($media),
			'keylinks' => $this->getModelFromCache('EWRmedio_Model_Keylinks')->getKeywordLinks($media),
			'userlinks' => $this->getModelFromCache('EWRmedio_Model_Userlinks')->getUserLinks($media),
			'fullList' => $this->getModelFromCache('EWRmedio_Model_Lists')->getCategoryList(),
			'services' => $this->getModelFromCache('EWRmedio_Model_Services')->getServices(),
			'breadCrumbs' => array_reverse($this->getModelFromCache('EWRmedio_Model_Lists')->getCrumbs($category)),
		);
		
		if (!(XenForo_Application::get('options')->EWRmedio_newkeywords))
		{
			$viewParams['keywords'] = $this->getModelFromCache('EWRmedio_Model_Keylinks')->getKeywordNolinks($media);
		}

		return $this->responseView('EWRmedio_ViewPublic_MediaEdit', 'EWRmedio_MediaEdit', $viewParams);
	}

	public function actionMediaDelete()
	{
		$mediaID = $this->_input->filterSingle('action_id', XenForo_Input::UINT);

		if ($media = $this->getModelFromCache('EWRmedio_Model_Media')->getMediaByID($mediaID))
		{
			if (!$this->perms['mod'] && $media['user_id'] !== XenForo_Visitor::getUserId()) { return $this->responseNoPermission(); }

			if ($this->_request->isPost())
			{
				$this->getModelFromCache('EWRmedio_Model_Media')->deleteMedia($media);
			}
			else
			{
				return $this->responseView('EWRmedio_ViewPublic_MediaDelete', 'EWRmedio_MediaDelete', array('media' => $media));
			}
		}

		return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media'));
	}

	public function actionMediaAlter()
	{
		$mediaID = $this->_input->filterSingle('action_id', XenForo_Input::UINT);

		$this->_assertPostOnly();

		if ($media = $this->getModelFromCache('EWRmedio_Model_Media')->getMediaByID($mediaID))
		{
			if (!$this->perms['admin'] && $media['user_id'] !== XenForo_Visitor::getUserId()) { return $this->responseNoPermission(); }

			$input = $this->_input->filter(array(
				'service_id' => XenForo_Input::UINT,
				'service_value' => XenForo_Input::STRING,
				'service_value2' => XenForo_Input::STRING,
				'submit' => XenForo_Input::STRING,
			));
			$input['media_id'] = $media['media_id'];
			if ($this->perms['admin']) { $input['bypass'] = true; }

			$this->getModelFromCache('EWRmedio_Model_Media')->alterMedia($input);
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media/media', $media));
		}

		return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media'));
	}
	
	public function actionMediaUsers()
	{
		if (!$this->perms['keyword']) { return $this->responseNoPermission(); }

		$this->_assertPostOnly();
		
		$mediaID = $this->_input->filterSingle('action_id', XenForo_Input::UINT);

		if (!$media = $this->getModelFromCache('EWRmedio_Model_Media')->getMediaByID($mediaID))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media'));
		}
		
		$usernames = explode(',', $this->_input->filterSingle('users', XenForo_Input::STRING));
		$users = $this->getModelFromCache('XenForo_Model_User')->getUsersByNames($usernames);
		$userIDs = array();
		
		foreach ($users AS $user)
		{
			$userIDs[] = $user['user_id'];
		}
		
		$this->getModelFromCache('EWRmedio_Model_Userlinks')->updateUserlinks($userIDs, $media);

		if ($this->_noRedirect())
		{
			$viewParams = array(
				'media' => $media,
				'users' => $this->getModelFromCache('EWRmedio_Model_Userlinks')->getUserLinks($media)
			);

			return $this->responseView('EWRmedio_ViewPublic_MediaUsers', 'EWRmedio_Bit_Users', $viewParams);
		}
		else
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media/media', $media));
		}
	}
	
	public function actionMediaKeywords()
	{
		if (!$this->perms['keyword']) { return $this->responseNoPermission(); }

		$this->_assertPostOnly();
		
		$mediaID = $this->_input->filterSingle('action_id', XenForo_Input::UINT);

		if (!$media = $this->getModelFromCache('EWRmedio_Model_Media')->getMediaByID($mediaID))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media'));
		}
		
		$keywords = $this->_input->filterSingle('keywords', XenForo_Input::STRING);
		
		$newkeys = $this->getModelFromCache('EWRmedio_Model_Keywords')->updateKeywords($keywords);
		$this->getModelFromCache('EWRmedio_Model_Keylinks')->updateKeylinks($newkeys, $media);
		$media['media_keywords'] =  $this->getModelFromCache('EWRmedio_Model_Media')->updateKeywords($media);

		if ($this->_noRedirect())
		{
			$viewParams = array(
				'media' => $media,
				'keywords' => $this->getModelFromCache('EWRmedio_Model_Keylinks')->getKeywordLinks($media)
			);

			return $this->responseView('EWRmedio_ViewPublic_MediaKeywords', 'EWRmedio_Bit_Keywords', $viewParams);
		}
		else
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media/media', $media));
		}
	}

	public function actionMediaThumb()
	{
		$this->_assertPostOnly();

		$mediaID = $this->_input->filterSingle('action_id', XenForo_Input::UINT);

		if (!$media = $this->getModelFromCache('EWRmedio_Model_Media')->getMediaByID($mediaID))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media'));
		}

		if (!$this->perms['mod'] && $media['user_id'] !== XenForo_Visitor::getUserId()) { return $this->responseNoPermission(); }

		$fileTransfer = new Zend_File_Transfer_Adapter_Http();

		if ($fileTransfer->isUploaded('upload_file'))
		{
			$fileInfo = $fileTransfer->getFileInfo('upload_file');
			$fileName = $fileInfo['upload_file']['tmp_name'];

			$this->getModelFromCache('EWRmedio_Model_Thumbs')->buildThumb($media['media_id'], $fileName);
		}

		return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media/media/edit', $media));
	}

	public function actionMediaLikes()
	{
		$mediaID = $this->_input->filterSingle('action_id', XenForo_Input::UINT);

		if (!$media = $this->getModelFromCache('EWRmedio_Model_Media')->getMediaByID($mediaID))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media'));
		}

		$likes = $this->getModelFromCache('XenForo_Model_Like')->getContentLikes('media', $media['media_id']);
		if (!$likes)
		{
			return $this->responseError(new XenForo_Phrase('no_one_has_liked_this_post_yet'));
		}

		$category = $this->getModelFromCache('EWRmedio_Model_Categories')->getCategoryByID($media['category_id']);

		$viewParams = array(
			'media' => $media,
			'breadCrumbs' => array_reverse($this->getModelFromCache('EWRmedio_Model_Lists')->getCrumbs($category)),
			'likes' => $likes
		);

		return $this->responseView('EWRmedio_ViewPublic_MediaLikes', 'EWRmedio_MediaLikes', $viewParams);
	}

	public function actionMediaLike()
	{
		if (!$this->perms['like']) { return $this->responseNoPermission(); }

		$mediaID = $this->_input->filterSingle('action_id', XenForo_Input::UINT);

		if (!$media = $this->getModelFromCache('EWRmedio_Model_Media')->getMediaByID($mediaID))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media'));
		}

		$viewingID = XenForo_Visitor::getUserId();

		if ($media['user_id'] == $viewingID)
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media/media', $media));
		}

		$existingLike = $this->getModelFromCache('XenForo_Model_Like')->getContentLikeByLikeUser('media', $media['media_id'], $viewingID);

		if ($this->_request->isPost())
		{
			if ($existingLike)
			{
				$latestUsers = $this->getModelFromCache('XenForo_Model_Like')->unlikeContent($existingLike);
			}
			else
			{
				$latestUsers = $this->getModelFromCache('XenForo_Model_Like')->likeContent('media', $media['media_id'], $media['user_id']);
			}

			$liked = ($existingLike ? false : true);

			if ($this->_noRedirect() && $latestUsers !== false)
			{
				$media['likeUsers'] = $latestUsers;
				$media['likes'] += ($liked ? 1 : -1);
				$media['like_date'] = ($liked ? XenForo_Application::$time : 0);

				$viewParams = array(
					'media' => $media,
					'liked' => $liked,
				);

				return $this->responseView('EWRmedio_ViewPublic_MediaLikeConfirmed', '', $viewParams);
			}
			else
			{
				return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media/media', $media));
			}
		}
		else
		{
			$category = $this->getModelFromCache('EWRmedio_Model_Categories')->getCategoryByID($media['category_id']);

			$viewParams = array(
				'media' => $media,
				'like' => $existingLike,
				'breadCrumbs' => array_reverse($this->getModelFromCache('EWRmedio_Model_Lists')->getCrumbs($category)),
			);

			return $this->responseView('EWRmedio_ViewPublic_MediaLike', 'EWRmedio_MediaLike', $viewParams);
		}
	}

	public function actionMediaReport()
	{
		if (!$this->perms['report']) { return $this->responseNoPermission(); }

		$mediaID = $this->_input->filterSingle('action_id', XenForo_Input::UINT);

		if (!$media = $this->getModelFromCache('EWRmedio_Model_Media')->getMediaByID($mediaID))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media'));
		}

		if ($this->_request->isPost())
		{
			$message = $this->_input->filterSingle('message', XenForo_Input::STRING);
			if (!$message)
			{
				return $this->responseError(new XenForo_Phrase('please_enter_reason_for_reporting_this_message'));
			}

			$this->getModelFromCache('XenForo_Model_Report')->reportContent('media', $media, $message);

			$controllerResponse = $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media/media', $media));
			$controllerResponse->redirectMessage = new XenForo_Phrase('thank_you_for_reporting_this_message');
			return $controllerResponse;
		}

		$viewParams = array(
			'media' => $media,
		);

		return $this->responseView('EWRmedio_ViewPublic_MediaReport', 'EWRmedio_MediaReport', $viewParams);
	}

	public function actionMediaComments()
	{
		$mediaID = $this->_input->filterSingle('action_id', XenForo_Input::UINT);

		if (!$media = $this->getModelFromCache('EWRmedio_Model_Media')->getMediaByID($mediaID))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media'));
		}

		$options = XenForo_Application::get('options');
		$start = max(1, $this->_input->filterSingle('page', XenForo_Input::UINT));
		$stop = $options->EWRmedio_commentcount;
		$count = $this->getModelFromCache('EWRmedio_Model_Comments')->getCommentCount($media);

		$viewParams = array(
			'perms' => $this->perms,
			'start' => $start,
			'stop' => $stop,
			'media' => $media,
			'count' => $count,
			'comments' => $this->getModelFromCache('EWRmedio_Model_Comments')->getComments($media, $start, $stop),
		);

		return $this->responseView('EWRmedio_ViewPublic_MediaComments', 'EWRmedio_MediaComments', $viewParams);
	}

	public function actionMediaComment()
	{
		if (!$this->perms['comment']) { return $this->responseNoPermission(); }

		$this->_assertPostOnly();

		$mediaID = $this->_input->filterSingle('action_id', XenForo_Input::UINT);

		if (!$media = $this->getModelFromCache('EWRmedio_Model_Media')->getMediaByID($mediaID))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media'));
		}

		$input['username'] = $this->_input->filterSingle('username', XenForo_Input::STRING);
		$input['message'] = $this->getHelper('Editor')->getMessageText('message', $this->_input);
		$this->getModelFromCache('EWRmedio_Model_Comments')->postComment($input, $media);

		if ($this->_noRedirect())
		{
			return $this->responseReroute(__CLASS__, 'MediaComments');
		}

		return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media/media', $media));
	}
	
	public function actionMediaWatchConfirm()
	{
		if (!XenForo_Visitor::getUserId()) { return $this->responseNoPermission(); }
		
		$mediaID = $this->_input->filterSingle('action_id', XenForo_Input::UINT);
		if (!$media = $this->getModelFromCache('EWRmedio_Model_Media')->getMediaByID($mediaID))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media'));
		}
		
		$mediaWatch = $this->getModelFromCache('EWRmedio_Model_MediaWatch')->getUserMediaWatchByMediaId(XenForo_Visitor::getUserId(), $media['media_id']);

		$viewParams = array(
			'media' => $media,
			'mediaWatch' => $mediaWatch,
		);

		return $this->responseView('EWRmedio_ViewPublic_MediaWatch', 'EWRmedio_MediaWatch', $viewParams);
	}
	
	public function actionMediaWatch()
	{
		if (!XenForo_Visitor::getUserId()) { return $this->responseNoPermission(); }
		
		$this->_assertPostOnly();
		
		$mediaID = $this->_input->filterSingle('action_id', XenForo_Input::UINT);
		if (!$media = $this->getModelFromCache('EWRmedio_Model_Media')->getMediaByID($mediaID))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media'));
		}

		if ($this->_input->filterSingle('stop', XenForo_Input::STRING))
		{
			$newState = '';
		}
		else if ($this->_input->filterSingle('email_subscribe', XenForo_Input::UINT))
		{
			$newState = 'watch_email';
		}
		else
		{
			$newState = 'watch_no_email';
		}
		
		$this->getModelFromCache('EWRmedio_Model_MediaWatch')->setMediaWatchState(XenForo_Visitor::getUserId(), $mediaID, $newState);

		return $this->responseRedirect(
			XenForo_ControllerResponse_Redirect::SUCCESS,
			XenForo_Link::buildPublicLink('media/media', $media),
			null,
			array('linkPhrase' => ($newState ? new XenForo_Phrase('unwatch_media') : new XenForo_Phrase('watch_media')))
		);
	}

	public static function getSessionActivityDetailsForList(array $activities)
	{
		$mediaIDs = array();
		foreach ($activities AS $activity)
		{
			if (!empty($activity['params']['media_id']))
			{
				$mediaIDs[$activity['params']['media_id']] = $activity['params']['media_id'];
			}
		}

		$mediaData = array();
		if ($mediaIDs)
		{
			$mediaModel = XenForo_Model::create('EWRmedio_Model_Media');
			$medias = $mediaModel->getMediasByIDs($mediaIDs);

			foreach ($medias AS $media)
			{
				$mediaData[$media['media_id']] = array(
					'title' => $media['media_title'],
					'url' => XenForo_Link::buildPublicLink('media/media', $media)
				);
			}
		}

        $output = array();
        foreach ($activities as $key => $activity)
		{
			$media = false;
			if (!empty($activity['params']['media_id']))
			{
				$mediaID = $activity['params']['media_id'];
				if (isset($mediaData[$mediaID]))
				{
					$media = $mediaData[$mediaID];
				}
			}

			if ($media)
			{
				$output[$key] = array(new XenForo_Phrase('viewing_media'), $media['title'], $media['url'], false);
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

		if (!$this->perms['view']) { throw $this->getNoPermissionResponseException(); }
	}
}