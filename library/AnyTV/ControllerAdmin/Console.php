<?php

class AnyTV_ControllerAdmin_Console extends XenForo_ControllerAdmin_User
{
	public function actionIndex()
	{
		
		$consoles = XenForo_Model::create('XenForo_Model_UserField');
		$consoles = unserialize($consoles->getUserFieldById('navLinks')['field_choices']);

		$mydb = XenForo_Application::get('db');
		$tags = $mydb->fetchAll("
			SELECT *
			FROM `anytv_console_tags`");

		foreach ($consoles as $key => $value) {
        	$consoles[$key] = array();
        	$consoles[$key]['id'] = $key;
        	$consoles[$key]['value'] = $value;
        }

        if(!empty($tags))
        	foreach ($tags as $key => $value) 
        		$consoles[$value['console_id']]['tags'] = $value['tags'];

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
			'action' => 'consoles',
			'consoles' => $consoles,
			'totalVideos' => $totalVideos,
			'showingAll' => $showingAll,
			'showAll' => (!$showingAll && $totalVideos <= 5000),

			'linkParams' => array('criteria' => $criteria, 'order' => $order, 'direction' => $direction),
			'page' => $page,
			'videosPerPage' => $videosPerPage,

			'filterView' => $filterView,
			'filterMore' => ($filterView && $totalVideos > $videosPerPage)
		);

		/*echo "<pre>";
		print_r($viewParams);
		exit;*/
		return $this->responseView('AnyTV_ViewAdmin_Featured', 'anytv_console_tags', $viewParams);
	}

	public function actionSave() {
		$url = $this->_input->getInput();
		$url = array_filter(explode('/', $url['_origRoutePath']));
		$url = explode('.', $url[2]);
		
		$mydb = XenForo_Application::get('db');
		$tags = $mydb->fetchAll("
			SELECT *
			FROM `anytv_console_tags`
			WHERE `console_id` = '".$url[0]."'");

		if( empty($tags) ){
			$values = array(
				'console_id'	=> $url[0],
				'tags' 			=> htmlspecialchars($url[1])
			);
			$mydb->insert('anytv_console_tags', $values);
		} else{
			$where = array('id='.$tags[0]['id']);
			$values = array('tags' => htmlspecialchars($url[1]));
			$mydb->update('anytv_console_tags', $values, $where);
		}

		header('Location: admin.php?anytv/console');
		exit;
	}
}