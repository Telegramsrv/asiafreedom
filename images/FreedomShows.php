<?php 

class EWRmedio_ControllerPublic_Media_FreedomShows extends XenForo_ControllerPublic_Abstract
{
		public $perms;

		public function actionFreedom() {

				$userID = $this->_input->filterSingle('action_id', XenForo_Input::UINT);

				$user = $this->getModelFromCache('XenForo_Model_User')->getUserById($userID);

			$options = XenForo_Application::get('options');
			$u = $this->_input->filterSingle('u', XenForo_Input::STRING);
			$f = $this->_input->filterSingle('f', XenForo_Input::STRING);
			$fuser = $this->_input->filterSingle('fuser', XenForo_Input::STRING);
			$filter = $this->_input->filterSingle('filter', XenForo_Input::STRING);
			$sort = $this->_input->filterSingle('sort', XenForo_Input::STRING);
			$order = $this->_input->filterSingle('order', XenForo_Input::STRING);
			$category = $this->_input->filterSingle('cat', XenForo_Input::STRING);

			$fuser = !empty($u) ? $u.','.$fuser : $fuser;
			list($fuser, $fuserText) = !empty($fuser) ? $this->getModelFromCache('EWRmedio_Model_Userlinks')->prepareUsernameFilter($fuser) : array(false, false);
			$filter = !empty($f) ? $f.','.$filter : $filter;
			list($filter, $filterText) = !empty($filter) ? $this->getModelFromCache('EWRmedio_Model_Keywords')->prepareKeywordsFilter($filter) : array(false, false);
		
			$cat = 'Shows';

			$listParams = array(
			'sort' => $sort,
			'order' => $order,
			'cat' => $cat,
			'type' => 'user',
			'where' => $user['user_id'],
			'fuser' => $fuser,
			'filter' => $filter
			);

			$start = max(1, $this->_input->filterSingle('page', XenForo_Input::UINT));
			$stop = '8';
			//$stop = $options->EWRmedio_mediacount;
			$count = $this->getModelFromCache('EWRmedio_Model_Lists')->getMediaCount($listParams);
			$media = $this->getModelFromCache('EWRmedio_Model_Lists')->getMediaList($start, $stop, $listParams);

			$this->canonicalizeRequestUrl(XenForo_Link::buildPublicLink('shows/freedom', $user, array('page' => $start)));
			$this->canonicalizePageNumber($start, $stop, $count, 'shows/freedom', $user);

			$viewParams = array(
					'perms' => $this->perms,
					'user' => $user,
					'start' => $start,
					'stop' => $stop,
					'count' => $count,
					'fusers' => $fuser,
					'fuserText' => $fuserText,
					'filters' => $filter,
					'pages' => 'freedom',
					'cat' => $cat,
					'filterText' => $filterText,
					'booruKeys' => $options->EWRmedio_displaybooru ? $this->getModelFromCache('EWRmedio_Model_Keywords')->getKeywordsByMedias(array_keys($media)) : false,
					'booruUsers' => $options->EWRmedio_displaybooru ? $this->getModelFromCache('EWRmedio_Model_Userlinks')->getUsernamesByMedias(array_keys($media)) : false,
					'mediaList' => $media,
					'test' => $medias,
					//'media' => $this->getModelFromCache('EWRmedio_Model_Media')->updateViews($media),
					'comments' => $this->getModelFromCache('EWRmedio_Model_Comments')->getComments($medias, $start, $stop),			
					'linkParams' => array('sort' => $sort, 'order' => $order, 'fuser' => $fuserText, 'filter' => $filterText),			
			);
			return $this->responseView('EWRmedio_ViewPublic_UserView', 'EWRmedio_FreedomShows', $viewParams);
		}
}