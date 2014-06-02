<?php

/**
 * Model for custom user fields.
 */
class AnyTV_ControllerPublic_Member extends XFCP_AnyTV_ControllerPublic_Member
{
	public function actionMember()
	{
		if ($this->_input->filterSingle('card', XenForo_Input::UINT))
		{
			return $this->responseReroute(__CLASS__, 'card');
		}

		$visitor = XenForo_Visitor::getInstance();

		$userId = $this->_input->filterSingle('user_id', XenForo_Input::UINT);
		$userFetchOptions = array(
			'join' => XenForo_Model_User::FETCH_LAST_ACTIVITY | XenForo_Model_User::FETCH_USER_PERMISSIONS
		);
		$user = $this->getHelper('UserProfile')->assertUserProfileValidAndViewable($userId, $userFetchOptions);

		// get last activity details
		$user['activity'] = ($user['view_date'] ? $this->getModelFromCache('XenForo_Model_Session')->getSessionActivityDetails($user) : false);

		$userModel = $this->_getUserModel();
		$userProfileModel = $this->_getUserProfileModel();

		// profile posts
		$page = $this->_input->filterSingle('page', XenForo_Input::UINT);
		$profilePostsPerPage = XenForo_Application::get('options')->messagesPerPage;

		$this->canonicalizeRequestUrl(
			XenForo_Link::buildPublicLink('members', $user, array('page' => $page))
		);

		$profilePostModel = $this->_getProfilePostModel();

		if ($userProfileModel->canViewProfilePosts($user))
		{
			$profilePostConditions = $profilePostModel->getPermissionBasedProfilePostConditions($user);
			$profilePostFetchOptions = array(
				'join' => XenForo_Model_ProfilePost::FETCH_USER_POSTER,
				'likeUserId' => XenForo_Visitor::getUserId(),
				'perPage' => $profilePostsPerPage,
				'page' => $page
			);
			if (!empty($profilePostConditions['deleted']))
			{
				$profilePostFetchOptions['join'] |= XenForo_Model_ProfilePost::FETCH_DELETION_LOG;
			}

			$totalProfilePosts = $profilePostModel->countProfilePostsForUserId($userId, $profilePostConditions);

			$profilePosts = $profilePostModel->getProfilePostsForUserId($userId, $profilePostConditions, $profilePostFetchOptions);
			$profilePosts = $profilePostModel->prepareProfilePosts($profilePosts, $user);
			$inlineModOptions = $profilePostModel->addInlineModOptionToProfilePosts($profilePosts, $user);

			$ignoredNames = $this->_getIgnoredContentUserNames($profilePosts);

			$profilePosts = $profilePostModel->addProfilePostCommentsToProfilePosts($profilePosts, array(
				'join' => XenForo_Model_ProfilePost::FETCH_COMMENT_USER
			));
			foreach ($profilePosts AS &$profilePost)
			{
				if (empty($profilePost['comments']))
				{
					continue;
				}

				foreach ($profilePost['comments'] AS &$comment)
				{
					$comment = $profilePostModel->prepareProfilePostComment($comment, $profilePost, $user);
				}
				$ignoredNames += $this->_getIgnoredContentUserNames($profilePost['comments']);
			}

			$canViewProfilePosts = true;
			if ($user['user_id'] == $visitor['user_id'])
			{
				$canPostOnProfile = $visitor->canUpdateStatus();
			}
			else
			{
				$canPostOnProfile = $userProfileModel->canPostOnProfile($user);
			}
		}
		else
		{
			$totalProfilePosts = 0;
			$profilePosts = array();
			$inlineModOptions = array();

			$ignoredNames = array();

			$canViewProfilePosts = false;
			$canPostOnProfile = false;
		}

		// custom fields
		$fieldModel = XenForo_Model::create('AnyTV_Models_CustomUserFieldModel');
		$customFields = $fieldModel->prepareUserFields($fieldModel->getUserFields(
			array(),
			array('valueUserId' => $user['user_id'])
		));

		foreach ($customFields AS $key => $field)
		{
			if ((!$field['viewableProfile'] && $key!="youtubeUploads") || !$field['hasValue'])
			{
				unset($customFields[$key]);
			}
		}

		$customFieldsGrouped = $fieldModel->groupUserFields($customFields);
		if (!$userProfileModel->canViewIdentities($user))
		{
			$customFieldsGrouped['contact'] = array();
		}

		// misc
		if ($user['following'])
		{
			$followingToShowCount = 6;
			$followingCount = substr_count($user['following'], ',') + 1;

			$following = $userModel->getFollowedUserProfiles($userId, $followingToShowCount, 'RAND()');

			if (($followingCount >= $followingToShowCount && count($following) < $followingToShowCount)
				|| ($followingCount < $followingToShowCount && $followingCount != count($following)))
			{
				// following count is off, rebuild it
				$user['following'] = $userModel->getFollowingDenormalizedValue($user['user_id']);
				$userModel->updateFollowingDenormalizedValue($user['user_id'], $user['following']);

				$followingCount = substr_count($user['following'], ',') + 1;
			}
		}
		else
		{
			$followingCount = 0;

			$following = array();
		}

		$followersCount = $userModel->countUsersFollowingUserId($userId);
		$followers = $userModel->getUsersFollowingUserId($userId, 6, 'RAND()');

		$birthday = $userProfileModel->getUserBirthdayDetails($user);
		$user['age'] = $birthday['age'];

		$user['isFollowingVisitor'] = $userModel->isFollowing($visitor['user_id'], $user);

		if ($userModel->canViewWarnings())
		{
			$canViewWarnings = true;
			$warningCount = $this->getModelFromCache('XenForo_Model_Warning')->countWarningsByUser($user['user_id']);
		}
		else
		{
			$canViewWarnings = false;
			$warningCount = 0;
		}

		$viewParams = array_merge($profilePostModel->getProfilePostViewParams($profilePosts, $user), array(
			'user' => $user,
			'canViewOnlineStatus' => $userModel->canViewUserOnlineStatus($user),
			'canIgnore' => $this->_getIgnoreModel()->canIgnoreUser($visitor['user_id'], $user),
			'canCleanSpam' => (XenForo_Permission::hasPermission($visitor['permissions'], 'general', 'cleanSpam') && $userModel->couldBeSpammer($user)),
			'canBanUsers' => ($visitor['is_admin'] && $visitor->hasAdminPermission('ban') && $user['user_id'] != $visitor->getUserId() && !$user['is_admin'] && !$user['is_moderator']),
			'canEditUser' => $userModel->canEditUser($user),
			'canViewIps' => $userModel->canViewIps(),
			'canReport' => $this->_getUserModel()->canReportUser($user),
			'latestVideo' => AnyTV_Helpers::getLatestVideo($customFieldsGrouped['personal']['youtube_id']['field_value']),

			'warningCount' => $warningCount,
			'canViewWarnings' => $canViewWarnings,
			'canWarn' => $userModel->canWarnUser($user),

			'followingCount' => $followingCount,
			'followersCount' => $followersCount,

			'following' => $following,
			'followers' => $followers,

			'birthday' => $birthday,

			'customFieldsGrouped' => $customFieldsGrouped,

			'canStartConversation' => $userModel->canStartConversationWithUser($user),

			'canViewProfilePosts' => $canViewProfilePosts,
			'canPostOnProfile' => $canPostOnProfile,
			'inlineModOptions' => $inlineModOptions,
			'page' => $page,
			'profilePostsPerPage' => $profilePostsPerPage,
			'totalProfilePosts' => $totalProfilePosts,

			'ignoredNames' => $ignoredNames,

			'showRecentActivity' => $userProfileModel->canViewRecentActivity($user),
		));

		return $this->responseView('XenForo_ViewPublic_Member_View', 'member_view', $viewParams);
	}
}