<?php

class EWRmedio_ControllerPublic_Media_Category extends XenForo_ControllerPublic_Abstract
{
	public $perms;

	public function actionCategory()
	{
		$catID = $this->_input->filterSingle('action_id', XenForo_Input::UINT);

		if (!$category = $this->getModelFromCache('EWRmedio_Model_Categories')->getCategoryByID($catID))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media/categories'));
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
			'type' => 'category',
			'where' => $category['category_id'],
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

		$this->canonicalizeRequestUrl(XenForo_Link::buildPublicLink('media/category', $category, array('page' => $start)));
		$this->canonicalizePageNumber($start, $stop, $count, 'media/category', $category);
		
		$listParams['fuser'] = $fuserText;
		$listParams['filter'] = $filterText;
		unset($listParams['type']);
		unset($listParams['where']);

		$breadCrumbs = array_reverse($this->getModelFromCache('EWRmedio_Model_Lists')->getCrumbs($category));
		array_pop($breadCrumbs);
		
		$customParams = array(
			'media_custom1' => $listParams['c1'],
			'media_custom2' => $listParams['c2'],
			'media_custom3' => $listParams['c3'],
			'media_custom4' => $listParams['c4'],
			'media_custom5' => $listParams['c5'],
		);

		$viewParams = array(
			'perms' => $this->perms,
			'category' => $category,
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
			'breadCrumbs' => $breadCrumbs,
			'sidebar' => $this->getModelFromCache('EWRmedio_Model_Parser')->parseSidebar(),
		);

		return $this->responseView('EWRmedio_ViewPublic_CategoryView', 'EWRmedio_CategoryView', $viewParams);
	}

	public function actionCategoryRss()
	{
		$catID = $this->_input->filterSingle('action_id', XenForo_Input::UINT);

		if (!$category = $this->getModelFromCache('EWRmedio_Model_Categories')->getCategoryByID($catID))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media/categories'));
		}

		$this->_routeMatch->setResponseType('rss');

		$viewParams = array(
			'rss' => $this->getModelFromCache('EWRmedio_Model_Sitemaps')->getRSSbyMedia(null, 'category', $category['category_id']),
		);

		return $this->responseView('EWRmedio_ViewPublic_RSS', '', $viewParams);
	}

	public function actionCategoryPodcast()
	{
		$catID = $this->_input->filterSingle('action_id', XenForo_Input::UINT);

		if (!$category = $this->getModelFromCache('EWRmedio_Model_Categories')->getCategoryByID($catID))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media/categories'));
		}

		$this->_routeMatch->setResponseType('rss');

		$viewParams = array(
			'rss' => $this->getModelFromCache('EWRmedio_Model_Sitemaps')->getRSSbyMedia(null, 'category', $category['category_id'], true),
		);

		return $this->responseView('EWRmedio_ViewPublic_RSS', '', $viewParams);
	}

	public function actionCategoryEdit()
	{
		if (!$this->perms['admin']) { return $this->responseNoPermission(); }

		$catID = $this->_input->filterSingle('action_id', XenForo_Input::UINT);

		if (!$category = $this->getModelFromCache('EWRmedio_Model_Categories')->getCategoryByID($catID))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media'));
		}

		if ($this->_request->isPost())
		{
			$input = $this->_input->filter(array(
				'category_name' => XenForo_Input::STRING,
				'category_parent' => XenForo_Input::UINT,
				'category_disabled' => XenForo_Input::UINT,
				'submit' => XenForo_Input::STRING,
			));
			$input['category_id'] = $category['category_id'];
			$input['category_description'] = $this->getHelper('Editor')->getMessageText('category_description', $this->_input);

			$this->getModelFromCache('EWRmedio_Model_Categories')->updateCategory($input);
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media/admin/categories'));
		}

		$children = array($category['category_id'] => $category);
		$children = $this->getModelFromCache('EWRmedio_Model_Lists')->getCategoryList($category['category_id'], $children);
		$catList = $this->getModelFromCache('EWRmedio_Model_Lists')->getCategoryList();

		foreach ($catList AS &$list)
		{
			$list['disabled'] = array_key_exists($list['category_id'], $children) ? true:false;
		}

		$viewParams = array(
			'category' => $category,
			'catList' => $catList,
		);

		return $this->responseView('EWRmedio_ViewPublic_CategoryEdit', 'EWRmedio_CategoryEdit', $viewParams);
	}

	public function actionCategoryDelete()
	{
		if (!$this->perms['admin']) { return $this->responseNoPermission(); }

		$catID = $this->_input->filterSingle('action_id', XenForo_Input::UINT);

		if ($category = $this->getModelFromCache('EWRmedio_Model_Categories')->getCategoryByID($catID))
		{
			if ($this->_request->isPost())
			{
				if (!$category['category_parent'] = $this->_input->filterSingle('category_parent', XenForo_Input::UINT))
				{
					throw new XenForo_Exception(new XenForo_Phrase('please_select_a_new_parent_category_node'), true);
				}

				$this->getModelFromCache('EWRmedio_Model_Categories')->deleteCategory($category);
			}
			else
			{
				$children = array($category['category_id'] => $category);
				$children = $this->getModelFromCache('EWRmedio_Model_Lists')->getCategoryList($category['category_id'], $children);
				$catList = $this->getModelFromCache('EWRmedio_Model_Lists')->getCategoryList();

				foreach ($catList AS &$list)
				{
					$list['disabled'] = array_key_exists($list['category_id'], $children) ? true:false;
				}

				$viewParams = array(
					'category' => $category,
					'catList' => $catList,
				);

				return $this->responseView('EWRmedio_ViewPublic_CategoryDelete', 'EWRmedio_CategoryDelete', $viewParams);
			}
		}

		return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT, XenForo_Link::buildPublicLink('media/admin/categories'));
	}

	public function actionCategoryCreate()
	{
		if (!$this->perms['admin']) { return $this->responseNoPermission(); }

		if ($this->_request->isPost())
		{
			$input = $this->_input->filter(array(
				'category_name' => XenForo_Input::STRING,
				'category_parent' => XenForo_Input::UINT,
				'category_disabled' => XenForo_Input::UINT,
				'submit' => XenForo_Input::STRING,
			));
			$input['category_description'] = $this->getHelper('Editor')->getMessageText('category_description', $this->_input);

			$this->getModelFromCache('EWRmedio_Model_Categories')->updateCategory($input);
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media/admin/categories'));
		}

		$viewParams = array(
			'catList' => $this->getModelFromCache('EWRmedio_Model_Lists')->getCategoryList(),
		);

		return $this->responseView('EWRmedio_ViewPublic_CategoryCreate', 'EWRmedio_CategoryCreate', $viewParams);
	}

	public static function getSessionActivityDetailsForList(array $activities)
	{
		$catIDs = array();
		foreach ($activities AS $activity)
		{
			if (!empty($activity['params']['category_id']))
			{
				$catIDs[$activity['params']['category_id']] = $activity['params']['category_id'];
			}
		}

		$categoryData = array();
		if ($catIDs)
		{
			$catModel = XenForo_Model::create('EWRmedio_Model_Categories');
			$categories = $catModel->getCategoriesByIDs($catIDs);

			foreach ($categories AS $category)
			{
				$categoryData[$category['category_id']] = array(
					'title' => $category['category_name'],
					'url' => XenForo_Link::buildPublicLink('media/category', $category)
				);
			}
		}

        $output = array();
        foreach ($activities as $key => $activity)
		{
			$category = false;
			if (!empty($activity['params']['category_id']))
			{
				$catID = $activity['params']['category_id'];
				if (isset($categoryData[$catID]))
				{
					$category = $categoryData[$catID];
				}
			}

			if ($category)
			{
				$output[$key] = array(new XenForo_Phrase('viewing_media_library'), $category['title'], $category['url'], false);
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

		if (!$this->perms['browse']) { throw $this->getNoPermissionResponseException(); }
	}
}