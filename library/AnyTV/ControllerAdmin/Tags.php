<?php

class AnyTV_ControllerAdmin_Tags extends XenForo_ControllerAdmin_User
{
	public function actionIndex()
	{
		$games = AnyTV_Games::getGames();
        $tags = AnyTV_Games::getTags();

        foreach ($tags as $key => $value) {
        	$games[$value['game_id']]['tags'] = $value['tags'];
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

		return $this->responseView('AnyTV_ViewAdmin_Featured', 'anytv_tags', $viewParams);
	}

	public function actionSave() {
		$url = $this->_input->getInput();
		$url = array_filter(explode('/', $url['_origRoutePath']));
		$url = explode('.', $url[2]);
		
		$mydb = XenForo_Application::get('db');
		$tags = $mydb->fetchAll("
			SELECT *
			FROM `anytv_game_tags`
			WHERE `game_id` = '".$url[0]."'");

		if( empty($tags) ){
			$values = array(
				'game_id'	=> $url[0],
				'tags' 		=> htmlspecialchars($url[1])
			);
			$mydb->insert('anytv_game_tags', $values);
		} else{
			$where = array('id='.$tags[0]['id']);
			$values = array('tags' => htmlspecialchars($url[1]));
			$mydb->update('anytv_game_tags', $values, $where);
		}

		header('Location: admin.php?anytv/tags');
		exit;
	}
}