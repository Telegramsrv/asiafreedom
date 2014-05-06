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

	//unfeatures the user
	public function action0() {
		$url = $this->_input->getInput();
		$url = array_filter(explode('/', $url['_origRoutePath']));
		list($userName, $userId) = explode('.', $url[4]);
		$id = $url[5];

		$values = array(
			'active' 	=> 0
		);

		$where = array(
			'id' 		=> $id
		);

		$mydb = XenForo_Application::get('db');
		$mydb->update('anytv_user_featured', $values);
		
		header('Location: admin.php?anytv/feature');
		exit;
	}

	//features the users
	public function action1() {
		$url = $this->_input->getInput();
		$url = array_filter(explode('/', $url['_origRoutePath']));
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
		//return $this->actionIndex();
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