<?php

class EWRmedio_ControllerPublic_Media_Service extends XenForo_ControllerPublic_Abstract
{
	public $perms;

	public function actionService()
	{
		$serviceID = $this->_input->filterSingle('action_id', XenForo_Input::STRING);

		if (!$service = $this->getModelFromCache('EWRmedio_Model_Services')->getServiceById($serviceID))
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
			'type' => 'service',
			'where' => $service['service_id'],
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

		$this->canonicalizeRequestUrl(XenForo_Link::buildPublicLink('media/service', $service, array('page' => $start)));
		$this->canonicalizePageNumber($start, $stop, $count, 'media/service', $service);
		
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
			'service' => $service,
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
			'sidebar' => $this->getModelFromCache('EWRmedio_Model_Parser')->parseSidebar(),
		);

		return $this->responseView('EWRmedio_ViewPublic_ServiceView', 'EWRmedio_ServiceView', $viewParams);
	}

	public function actionServiceRss()
	{
		$serviceID = $this->_input->filterSingle('action_id', XenForo_Input::STRING);

		if (!$service = $this->getModelFromCache('EWRmedio_Model_Services')->getServiceById($serviceID))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media'));
		}

		$this->_routeMatch->setResponseType('rss');

		$viewParams = array(
			'rss' => $this->getModelFromCache('EWRmedio_Model_Sitemaps')->getRSSbyMedia(null, 'service', $service['service_id']),
		);

		return $this->responseView('EWRmedio_ViewPublic_RSS', '', $viewParams);
	}

	public function actionServiceEdit()
	{
		if (!$this->perms['admin']) { return $this->responseNoPermission(); }

		$serviceID = $this->_input->filterSingle('action_id', XenForo_Input::STRING);

		if (!$service = $this->getModelFromCache('EWRmedio_Model_Services')->getServiceById($serviceID))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media/admin/services'));
		}

		if ($this->_request->isPost())
		{
			$input = $this->_input->filter(array(
				'service_name' => XenForo_Input::STRING,
				'service_media' => XenForo_Input::STRING,
				'service_regex' => XenForo_Input::STRING,
				'service_playlist' => XenForo_Input::STRING,
				'service_url' => XenForo_Input::STRING,
				'service_callback' => XenForo_Input::STRING,
				'service_width' => XenForo_Input::UINT,
				'service_height' => XenForo_Input::UINT,
				'service_embed' => XenForo_Input::STRING,
				'service_local' => XenForo_Input::UINT,
			));
			$input['service_id'] = $service['service_id'];
			
			$this->getModelFromCache('EWRmedio_Model_Services')->updateService($input);
			
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media/admin/services'));
		}

		$viewParams = array(
			'service' => $service,
		);

		return $this->responseView('EWRmedio_ViewPublic_ServiceEdit', 'EWRmedio_ServiceEdit', $viewParams);
	}

	public function actionServiceDelete()
	{
		if (!$this->perms['admin']) { return $this->responseNoPermission(); }

		$serviceID = $this->_input->filterSingle('action_id', XenForo_Input::STRING);

		if ($service = $this->getModelFromCache('EWRmedio_Model_Services')->getServiceById($serviceID))
		{
			if ($this->_request->isPost())
			{
				$this->getModelFromCache('EWRmedio_Model_Services')->deleteService($service);
			}
			else
			{
				return $this->responseView('EWRmedio_ViewPublic_ServiceDelete', 'EWRmedio_ServiceDelete', array('service' => $service));
			}
		}

		return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media/admin/services'));
	}

	public function actionServiceExport()
	{
		if (!$this->perms['admin']) { return $this->responseNoPermission(); }

		$serviceID = $this->_input->filterSingle('action_id', XenForo_Input::STRING);

		if (!$service = $this->getModelFromCache('EWRmedio_Model_Services')->getServiceById($serviceID))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media/admin/services'));
		}

		$this->_routeMatch->setResponseType('xml');

		$viewParams = array(
			'service' => $service,
			'xml' => $this->getModelFromCache('EWRmedio_Model_Services')->exportService($service),
		);

		return $this->responseView('EWRmedio_ViewPublic_ServiceExport', '', $viewParams);
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