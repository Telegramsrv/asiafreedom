<?php

class AnyTV_ControllerAdmin_Featured extends XenForo_ControllerAdmin_User
{
	public function actionIndex()
	{
		$criteria = $this->_input->filterSingle('criteria', XenForo_Input::JSON_ARRAY);
		$criteria = $this->_filterUserSearchCriteria($criteria);

		$filter = $this->_input->filterSingle('_filter', XenForo_Input::ARRAY_SIMPLE);
		if ($filter && isset($filter['value']))
		{
			$criteria['username2'] = array($filter['value'], empty($filter['prefix']) ? 'lr' : 'r');
			$filterView = true;
		}
		else
		{
			$filterView = false;
		}

		$order = $this->_input->filterSingle('order', XenForo_Input::STRING);
		$direction = $this->_input->filterSingle('direction', XenForo_Input::STRING);

		$page = $this->_input->filterSingle('page', XenForo_Input::UINT);
		$usersPerPage = 20;

		$showingAll = $this->_input->filterSingle('all', XenForo_Input::UINT);
		if ($showingAll)
		{
			$page = 1;
			$usersPerPage = 5000;
		}

		$fetchOptions = array(
			'perPage' => $usersPerPage,
			'page' => $page,

			'order' => $order,
			'direction' => $direction
		);

		$userModel = $this->_getUserModel();

		$criteriaPrepared = $this->_prepareUserSearchCriteria($criteria);

		$totalUsers = $userModel->countUsers($criteriaPrepared);
		if (!$totalUsers)
		{
			return $this->responseError(new XenForo_Phrase('no_users_matched_specified_criteria'));
		}

		$users = $userModel->getUsers($criteriaPrepared, $fetchOptions);

		if ($totalUsers == 1 && ($user = reset($users)) && !$filterView)
		{
			return $this->responseRedirect(
				XenForo_ControllerResponse_Redirect::SUCCESS,
				XenForo_Link::buildAdminLink('users/edit', $user)
			);
		}

		// TODO: show more structured info: username, email, last activity, messages?
		$userIds = array_map(function($user) {
			return $user['user_id'];
		}, $users);

		$mydb = XenForo_Application::get('db');
		$featured = $mydb->fetchAll("
			SELECT *
			FROM `anytv_user_featured`
			WHERE `active` = 1
			AND user_id in (".implode(',', $userIds).")");

		foreach ($featured as $key => $value) {
			$users[$value['user_id']]['featured'] = true;
			$users[$value['user_id']]['featured_id'] = $value['id'];
		}

		$viewParams = array(
			'action' => 'users',
			'users' => $users,
			'totalUsers' => $totalUsers,
			'showingAll' => $showingAll,
			'showAll' => (!$showingAll && $totalUsers <= 5000),

			'linkParams' => array('criteria' => $criteria, 'order' => $order, 'direction' => $direction),
			'page' => $page,
			'usersPerPage' => $usersPerPage,

			'filterView' => $filterView,
			'filterMore' => ($filterView && $totalUsers > $usersPerPage)
		);

		return $this->responseView('AnyTV_ViewAdmin_Featured', 'anytv_featured', $viewParams);
	}

	public function actionVideos() {
		$host = XenForo_Application::get('db')->getConfig()['host'];
   		$m = new MongoClient($host); // connect
        $db = $m->selectDB("asiafreedom_youtubers");

        $mydb = XenForo_Application::get('db');
        $featured = $mydb->fetchAll("
			SELECT *
			FROM `anytv_video_featured`
			WHERE `active` = 1");

        $featuredVideos = array();
        $featuredVideosMap = array();

        foreach($featured as $data) {
        	$featuredVideos[] = new MongoID($data['video_id']);
        	$featuredVideosMap[$data['video_id']] = $data;
        }

        $featuredMongo = $db->videos->find(array('_id' => array('$in' => $featuredVideos)));
        $featuredMongo = iterator_to_array($featuredMongo);

        foreach ($featuredMongo as $key => &$value) {
        	$value['featured_id'] = $featuredVideosMap[$key]['id'];
        	$value['featured'] = 1;
        }

        $where = array(
        	'_id' => array(
            	'$not' => array('$in' => $featuredVideos)
        	)
        );

        $criteria = $this->_input->filterSingle('criteria', XenForo_Input::JSON_ARRAY);
		$criteria = $this->_filterUserSearchCriteria($criteria);
		$order = $this->_input->filterSingle('order', XenForo_Input::STRING);
		$direction = $this->_input->filterSingle('direction', XenForo_Input::STRING);
		$page = $this->_input->filterSingle('page', XenForo_Input::UINT) ? $this->_input->filterSingle('page', XenForo_Input::UINT) : 1 ;
		$videosPerPage = 20;
		$showingAll = $this->_input->filterSingle('all', XenForo_Input::UINT);
		if ($showingAll)
		{
			$page = 1;
			$videosPerPage = 5000;
		}

		$filter = $this->_input->filterSingle('_filter', XenForo_Input::ARRAY_SIMPLE);
		if ($filter && isset($filter['value']))
		{
			$where['snippet.title']  = new MongoRegex('/'.$filter['value'].'/i');
			$filterView = true;
		}
		else
		{
			$filterView = false;
		}


        $totalVideos = $db->videos->count($where);

		$videos = $db->videos
            ->find(
            	$where
            )->sort(array('snippet.publishedAt'=>-1))->skip(($page-1)*($videosPerPage-count($featuredMongo)))->limit($videosPerPage-count($featuredMongo));
        $videos = array_merge($featuredMongo, iterator_to_array($videos));        

		$viewParams = array(
			'action' => 'videos',
			'videos' => $videos,
			'totalVideos' => $totalVideos,
			'showingAll' => $showingAll,
			'showAll' => (!$showingAll && $totalVideos <= 5000),

			'linkParams' => array('criteria' => $criteria, 'order' => $order, 'direction' => $direction),
			'page' => $page,
			'videosPerPage' => $videosPerPage,

			'filterView' => $filterView,
			'filterMore' => ($filterView && $totalVideos > $videosPerPage)
		);

		return $this->responseView('AnyTV_ViewAdmin_Featured', 'anytv_featured_videos', $viewParams);
	}

	public function actionGames() {
		$games = AnyTV_Games::getGames();
        $featuredGames = AnyTV_Games::getFeatured();

        foreach ($featuredGames as $key => $value) {
        	$games[$key] = $value;
        }

        $criteria = $this->_input->filterSingle('criteria', XenForo_Input::JSON_ARRAY);
		$criteria = $this->_filterUserSearchCriteria($criteria);
		$order = $this->_input->filterSingle('order', XenForo_Input::STRING);
		$direction = $this->_input->filterSingle('direction', XenForo_Input::STRING);
		$page = $this->_input->filterSingle('page', XenForo_Input::UINT) ? $this->_input->filterSingle('page', XenForo_Input::UINT) : 1 ;
		$videosPerPage = 20;
		$showingAll = $this->_input->filterSingle('all', XenForo_Input::UINT);
		if ($showingAll)
		{
			$page = 1;
			$videosPerPage = 5000;
		}

		$filter = $this->_input->filterSingle('_filter', XenForo_Input::ARRAY_SIMPLE);
		if ($filter && isset($filter['value']))
		{
			$where['snippet.title']  = new MongoRegex('/'.$filter['value'].'/i');
			$filterView = true;
		}
		else
		{
			$filterView = false;
		}


        $totalVideos = 100;

		$videos = array();       

		$viewParams = array(
			'action' => 'games',
			'games' => $games,
			'totalVideos' => $totalVideos,
			'showingAll' => $showingAll,
			'showAll' => (!$showingAll && $totalVideos <= 5000),

			'linkParams' => array('criteria' => $criteria, 'order' => $order, 'direction' => $direction),
			'page' => $page,
			'videosPerPage' => $videosPerPage,

			'filterView' => $filterView,
			'filterMore' => ($filterView && $totalVideos > $videosPerPage)
		);

		return $this->responseView('AnyTV_ViewAdmin_Featured', 'anytv_featured_games', $viewParams);
	}

	//unfeatures
	public function action0() {
		$url = $this->_input->getInput();
		$url = array_filter(explode('/', $url['_origRoutePath']));

		$method = 'unfeature'.ucfirst($url[2]);
		$this->$method($url);
	}

	//features
	public function action1() {
		$url = $this->_input->getInput();
		$url = array_filter(explode('/', $url['_origRoutePath']));
		
		$method = 'feature'.ucfirst($url[2]);
		$this->$method($url);
	}

	public function featureUser($url) {
		list($userName, $userId) = explode('.', $url[4]);

		$values = array(
			'user_id' 		=> $userId,
			'active' 		=> 1,
			'featured_date' => date('Y-m-d')
		);

		$mydb = XenForo_Application::get('db');
		$mydb->insert('anytv_user_featured', $values);

		header('Location: admin.php?anytv/feature');
		exit;
	}

	public function unfeatureUser($url) {
		list($userName, $userId) = explode('.', $url[4]);
		$id = $url[5];

		$values = array(
			'active' 	=> 0
		);

		$where = array('id='.$id);

		$mydb = XenForo_Application::get('db');
		$mydb->update('anytv_user_featured', $values, $where);
		
		header('Location: admin.php?anytv/feature');
		exit;
	}

	public function featureGames($url) {
		$id = $url[4];

		$values = array(
			'game_id' 		=> $id,
			'active' 		=> 1,
			'featured_date' => date('Y-m-d')
		);

		$mydb = XenForo_Application::get('db');
		$mydb->insert('anytv_game_featured', $values);

		header('Location: admin.php?anytv/feature/games');
		exit;
	}

	public function unfeatureGames($url) {
		$id = $url[5];

		$values = array(
			'active' 	=> 0
		);

		$where = array('id='.$id);

		$mydb = XenForo_Application::get('db');
		$mydb->update('anytv_game_featured', $values, $where);
		
		header('Location: admin.php?anytv/feature/games');
		exit;
	}

	public function featureVideo($url) {
		$id = $url[4];

		$values = array(
			'video_id' 		=> $id,
			'active' 		=> 1,
			'featured_date' => date('Y-m-d')
		);

		$mydb = XenForo_Application::get('db');
		$mydb->insert('anytv_video_featured', $values);

		header('Location: admin.php?anytv/feature/videos');
		exit;
	}

	public function unfeatureVideo($url) {
		$id = $url[5];

		$values = array(
			'active' 	=> 0
		);

		$where = array('id='.$id);

		$mydb = XenForo_Application::get('db');
		$mydb->update('anytv_video_featured', $values, $where);
		
		header('Location: admin.php?anytv/feature/videos');
		exit;
	}

	public function actionEdit() {
		return $this->responseView('AnyTV_ViewAdmin_Featured', 'anytv_featured', array('adin'=>'pogi'));
	}

	protected function _filterUserSearchCriteria(array $criteria)
	{
		return $this->_getCriteriaHelper()->filterUserSearchCriteria($criteria);
	}

	protected function _getCriteriaHelper()
	{
		return $this->getHelper('UserCriteria');
	}
}