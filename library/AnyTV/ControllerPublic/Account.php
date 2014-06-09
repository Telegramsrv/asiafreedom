<?php

/**
 * Model for custom user fields.
 */
class AnyTV_ControllerPublic_Account extends XFCP_AnyTV_ControllerPublic_Account
{
	public function actionPersonalDetailsSave()
	{
		$this->_assertPostOnly();

		if (!XenForo_Visitor::getInstance()->canEditProfile())
		{
			return $this->responseNoPermission();
		}

		$settings = $this->_input->filter(array(
			'gender'     => XenForo_Input::STRING,
			'custom_title' => XenForo_Input::STRING,
			// user_profile
			'status'     => XenForo_Input::STRING,
			'homepage'   => XenForo_Input::STRING,
			'location'   => XenForo_Input::STRING,
			'occupation' => XenForo_Input::STRING,
			'dob_day'    => XenForo_Input::UINT,
			'dob_month'  => XenForo_Input::UINT,
			'dob_year'   => XenForo_Input::UINT,
			// user_option
			'show_dob_year' => XenForo_Input::UINT,
			'show_dob_date' => XenForo_Input::UINT,
		));
		$settings['about'] = $this->getHelper('Editor')->getMessageText('about', $this->_input);
		$settings['about'] = XenForo_Helper_String::autoLinkBbCode($settings['about']);

		$visitor = XenForo_Visitor::getInstance();
		if ($visitor['dob_day'] && $visitor['dob_month'] && $visitor['dob_year'])
		{
			// can't change dob if set
			unset($settings['dob_day'], $settings['dob_month'], $settings['dob_year']);
		}

		if (!$visitor->hasPermission('general', 'editCustomTitle'))
		{
			unset($settings['custom_title']);
		}

		$status = $settings['status'];
		unset($settings['status']); // see below for status update

		if ($status !== '')
		{
			$this->assertNotFlooding('post');
		}

		$customFields = $this->_input->filterSingle('custom_fields', XenForo_Input::ARRAY_SIMPLE);
		$customFieldsShown = $this->_input->filterSingle('custom_fields_shown', XenForo_Input::STRING, array('array' => true));

		$youtube = strlen($customFields['youtube_id']) ? $customFields['youtube_id'] : '';
		$youtube = explode('/', $youtube);
		if(!is_array($youtube)) {
			$youtube = array($youtube);
		}
		$channelId = array();
		foreach ($youtube as $key => $value) {
			if($value && strlen($value)) {
				$channelId[] = $value;
			}
		}

		$channelId = count($channelId) ? $channelId[count($channelId)-1] : "";

		$customFields['youtubeUploads'] = "";
		$customFieldsShown[] = 'youtubeUploads';
		if(strlen($channelId)) {
			$url = 'https://gdata.youtube.com/feeds/api/users/'.$channelId.'/uploads';
			$params = array( 'v' => 2, 'alt' => 'json' );
			$res = $this->curlGet($url, $params, false);

			$channelId = $res['feed']['entry'][0]['media$group']['yt$uploaderId']['$t'];
			$customFields['youtube_id'] = $channelId;

			$playlist = "";
			$url = 'https://www.googleapis.com/youtube/v3/channels';//?id='.$channelId.'&key=AIzaSyAP14m25_1uScfmZObKqRI4lCwveb9E8Vk&part=contentDetails';
			$params = array(
				'id' 	=> $channelId,
				'key'	=> 'AIzaSyAP14m25_1uScfmZObKqRI4lCwveb9E8Vk',
				'part'	=> 'contentDetails'
			);
			$res = $this->curlGet($url, $params);
			if(isset($res['items'], 	$res['items'][0], 	$res['items'][0]['contentDetails'],
					$res['items'][0]['contentDetails']['relatedPlaylists'], $res['items'][0]['contentDetails']['relatedPlaylists']['uploads'])) {
				$playlist = $res['items'][0]['contentDetails']['relatedPlaylists']['uploads'];
			}

			$customFields['youtubeUploads'] = $playlist;
			$customFieldsShown[] = 'youtubeUploads';
		}

		$writer = XenForo_DataWriter::create('XenForo_DataWriter_User');
		$writer->setExistingData(XenForo_Visitor::getUserId());
		$writer->bulkSet($settings);
		$writer->setCustomFields($customFields, $customFieldsShown);

		$spamModel = $this->_getSpamPreventionModel();

		if ($settings['about'] && !$writer->hasErrors() && $spamModel->visitorRequiresSpamCheck())
		{
			$spamResult = $spamModel->checkMessageSpam($settings['about'], array(), $this->_request);
			switch ($spamResult)
			{
				case XenForo_Model_SpamPrevention::RESULT_MODERATED:
				case XenForo_Model_SpamPrevention::RESULT_DENIED;
					$spamModel->logSpamTrigger('user_about', XenForo_Visitor::getUserId());
					$writer->error(new XenForo_Phrase('your_content_cannot_be_submitted_try_later'));
					break;
			}
		}

		$writer->preSave();

		if ($dwErrors = $writer->getErrors())
		{
			return $this->responseError($dwErrors);
		}

		$writer->save();

		$redirectParams = array();

		if ($status !== '' && $visitor->canUpdateStatus())
		{
			$this->getModelFromCache('XenForo_Model_UserProfile')->updateStatus($status);
			$redirectParams['status'] = $status;
		}

		if ($this->_noRedirect())
		{
			$user = $writer->getMergedData();

			// send new avatar URLs if the user's gender has changed
			if (!$user['avatar_date'] && !$user['gravatar'] && $writer->isChanged('gender'))
			{
				return $this->responseView('XenForo_ViewPublic_Account_GenderChange', '', array('user' => $user));
			}

		}

		return $this->responseRedirect(
			XenForo_ControllerResponse_Redirect::SUCCESS,
			XenForo_Link::buildPublicLink('account/personal-details'),
			null,
			$redirectParams
		);
	}

	public function curlGet($url, $params = array(), $verify = true) {
		$url.=('?'.http_build_query($params));
		$res = file_get_contents($url);

		return json_decode($res, true);
	}
}
