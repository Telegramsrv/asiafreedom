<?php

class EWRmedio_Model_Lists extends XenForo_Model
{
	public function getMediaQueue()
	{
		$queues =  $this->_getDb()->fetchAll("
			SELECT EWRmedio_media.*, xf_user.*
				FROM EWRmedio_media
				LEFT JOIN xf_user ON (xf_user.user_id = EWRmedio_media.user_id)
			WHERE EWRmedio_media.media_state = 'moderated'
			ORDER BY EWRmedio_media.media_date ASC
		");
		
		$odd = 1;
		
		foreach ($queues AS &$queue)
		{
			$queue['primary'] = $odd ? 1 : 0;
			$odd = $odd ? 0 : 1;
		}

        return $queues;
	}
	
	public function getQueueCount()
	{
        $count = $this->_getDb()->fetchRow("SELECT COUNT(*) AS total FROM EWRmedio_media WHERE media_state = 'moderated'");

		return $count['total'];
	}

	public function getMediaCount($params = array())
	{
		$params['type']  = empty($params['type'])  ? null   : $params['type'];
		$params['where'] = empty($params['where']) ? null   : $params['where'];
		
		switch ($params['type'])
		{
			case "category":	$onlyWhere = "WHERE EWRmedio_media.category_id = ".$params['where'];	break;
			case "account":
			case "user":		$onlyWhere = "WHERE EWRmedio_media.user_id = ".$params['where'];		break;
			case "service":		$onlyWhere = "WHERE EWRmedio_media.service_id = ".$params['where'];		break;
			default:			$onlyWhere = "WHERE EWRmedio_media.media_state = 'visible'";			break;
		}
		
		$visible = $params['type'] == 'account' ? '' : "AND EWRmedio_media.media_state = 'visible'";
		
		if (!empty($params['fuser']))
		{
			foreach ($params['fuser'] AS $user)
			{
				$onlyWhere .= " AND EWRmedio_media.media_id IN (SELECT media_id FROM EWRmedio_userlinks WHERE username_id = ".$this->_getDb()->quote($user['user_id']).")";
			}
		}
		
		if (!empty($params['filter']))
		{
			foreach ($params['filter'] AS $filter)
			{
				$onlyWhere .= " AND EWRmedio_media.media_id IN (SELECT media_id FROM EWRmedio_keylinks WHERE keyword_id = ".$this->_getDb()->quote($filter['keyword_id']).")";
			}
		}
		
		if (!empty($params['c1'])) { $onlyWhere .= " AND EWRmedio_media.media_custom1 = ".$this->_getDb()->quote($params['c1']); }
		if (!empty($params['c2'])) { $onlyWhere .= " AND EWRmedio_media.media_custom1 = ".$this->_getDb()->quote($params['c2']); }
		if (!empty($params['c3'])) { $onlyWhere .= " AND EWRmedio_media.media_custom1 = ".$this->_getDb()->quote($params['c3']); }
		if (!empty($params['c4'])) { $onlyWhere .= " AND EWRmedio_media.media_custom1 = ".$this->_getDb()->quote($params['c4']); }
		if (!empty($params['c5'])) { $onlyWhere .= " AND EWRmedio_media.media_custom1 = ".$this->_getDb()->quote($params['c5']); }

        $count = $this->_getDb()->fetchRow("
			SELECT COUNT(*) AS total
				FROM EWRmedio_media
			$onlyWhere
				$visible
		");

		return $count['total'];
	}

	public function getMediaList($start, $stop, $params = array())
	{
		if (!$stop) { return array(); }
		
		$params['sort']  = empty($params['sort'])  ? 'date' : $params['sort'];
		$params['order'] = empty($params['order']) ? 'DESC' : $params['order'];
		$params['type']  = empty($params['type'])  ? null   : $params['type'];
		$params['where'] = empty($params['where']) ? null   : $params['where'];
		$params['local'] = empty($params['local']) ? false  : $params['local'];
		
		$params['order'] = $params['order'] == 'ASC' ? 'ASC' : 'DESC';
	
		switch ($params['type'])
		{
			case "category":	$onlyWhere = "WHERE EWRmedio_media.category_id = ".$params['where'];	break;
			case "account":
			case "user":		$onlyWhere = "WHERE EWRmedio_media.user_id = ".$params['where'];		break;
			case "service":		$onlyWhere = "WHERE EWRmedio_media.service_id = ".$params['where'];		break;
			default:			$onlyWhere = "WHERE EWRmedio_media.media_state = 'visible'";			break;
		}

		$local = $params['local'] ? "AND EWRmedio_services.service_local = '1'" : '';
		$visible = $params['type'] == 'account' ? '' : "AND EWRmedio_media.media_state = 'visible'";
		
		if (!empty($params['fuser']))
		{
			foreach ($params['fuser'] AS $user)
			{
				$onlyWhere .= " AND EWRmedio_media.media_id IN (SELECT media_id FROM EWRmedio_userlinks WHERE username_id = ".$this->_getDb()->quote($user['user_id']).")";
			}
		}
		
		if (!empty($params['filter']))
		{
			foreach ($params['filter'] AS $filter)
			{
				$onlyWhere .= " AND EWRmedio_media.media_id IN (SELECT media_id FROM EWRmedio_keylinks WHERE keyword_id = ".$this->_getDb()->quote($filter['keyword_id']).")";
			}
		}
		
		if (!empty($params['c1'])) { $onlyWhere .= " AND EWRmedio_media.media_custom1 = ".$this->_getDb()->quote($params['c1']); }
		if (!empty($params['c2'])) { $onlyWhere .= " AND EWRmedio_media.media_custom1 = ".$this->_getDb()->quote($params['c2']); }
		if (!empty($params['c3'])) { $onlyWhere .= " AND EWRmedio_media.media_custom1 = ".$this->_getDb()->quote($params['c3']); }
		if (!empty($params['c4'])) { $onlyWhere .= " AND EWRmedio_media.media_custom1 = ".$this->_getDb()->quote($params['c4']); }
		if (!empty($params['c5'])) { $onlyWhere .= " AND EWRmedio_media.media_custom1 = ".$this->_getDb()->quote($params['c5']); }
		
		switch ($params['sort'])
		{
			case "date": case "comments":
			case "likes": case "views":
			case "title":
				$orderBy = 'EWRmedio_media.media_'.$params['sort'].' '.$params['order'];
				break;
			case "popular":
				$orderBy = 'popular_score DESC';
				break;
			case "trending":
				$orderBy = 'trending_score DESC';
				break;
			default:
				$orderBy = 'EWRmedio_media.media_date '.$params['order'];
		}

		$start = ($start - 1) * $stop;
		
		$options = XenForo_Application::get('options');

		$medias = $this->fetchAllKeyed("
			SELECT EWRmedio_media.*, EWRmedio_categories.*, EWRmedio_services.*, xf_user.*, EWRmedio_media.service_value2 AS service_value2,
				ROUND((EWRmedio_media.media_comments * ?) + (EWRmedio_media.media_likes * ?) + (EWRmedio_media.media_views * ?)) AS popular_score,
				ROUND(((EWRmedio_media.media_comments * ?) + (EWRmedio_media.media_likes * ?) + (EWRmedio_media.media_views * ?)) / CEIL((? - EWRmedio_media.media_date) / ?), 2) AS trending_score,
				IF(NOT ISNULL(xf_user.user_id), xf_user.username, EWRmedio_media.username) AS username
			FROM EWRmedio_media
				LEFT JOIN EWRmedio_categories ON (EWRmedio_categories.category_id = EWRmedio_media.category_id)
				LEFT JOIN EWRmedio_services ON (EWRmedio_services.service_id = EWRmedio_media.service_id)
				LEFT JOIN xf_user ON (xf_user.user_id = EWRmedio_media.user_id)
			$onlyWhere
				$local
				$visible
			ORDER BY $orderBy, EWRmedio_media.media_date DESC, EWRmedio_media.media_id DESC
			LIMIT ?, ?
		", 'media_id', array(
			$options->EWRmedio_trendcomment, $options->EWRmedio_trendlike, $options->EWRmedio_trendview,
			$options->EWRmedio_trendcomment, $options->EWRmedio_trendlike, $options->EWRmedio_trendview,
			XenForo_Application::$time, $options->EWRmedio_trenddecay, $start, $stop
		));

		foreach ($medias AS &$media)
		{
			$media = $this->getModelFromCache('EWRmedio_Model_Parser')->parseReplace($media);
			$media = $this->getModelFromCache('EWRmedio_Model_Media')->getDuration($media);
		}

        return $medias;
	}

	public function getCategories()
	{
        $pages = $this->_getDb()->fetchAll("
			SELECT EWRmedio_categories.*, COUNT(EWRmedio_media.media_id) AS count
				FROM EWRmedio_categories
				LEFT JOIN EWRmedio_media ON (EWRmedio_media.category_id = EWRmedio_categories.category_id)
			GROUP BY EWRmedio_categories.category_id
			ORDER BY category_order, category_name ASC
		");

		return $pages;
	}

	public function getCategoryList($parent = 0, &$fullCategoryList = array(), $depth = 0, $categories = false)
	{
		if (!$categories) { $categories = $this->getCategories(); }

		foreach ($categories AS $category)
		{
			if ($category['category_parent'] == $parent)
			{
				$category['category_depth'] = $depth;
				$category['category_indent'] = "";
				for ($counter = 1; $counter <= $depth; $counter++)
				{
					$category['category_indent'] .= "&nbsp; &nbsp; ";
				}
				$fullCategoryList[$category['category_id']] = $category;

				$this->getCategoryList($category['category_id'], $fullCategoryList, $depth+1, $categories);
			}
		}

		return $fullCategoryList;
	}

	public function getUserList()
	{
        $userList = $this->_getDb()->fetchAll("
			SELECT COUNT(EWRmedio_media.media_id) AS count, xf_user.*
				FROM EWRmedio_media
				LEFT JOIN xf_user ON (xf_user.user_id = EWRmedio_media.user_id)
			WHERE NOT ISNULL(xf_user.user_id)
			GROUP BY EWRmedio_media.user_id
			ORDER BY count DESC
			LIMIT ?
		", 10);

		return $userList;
	}

	public function getCrumbs($category, &$breadCrumbs = array())
	{
		$breadCrumbs['cat'.$category['category_id']] = array(
			 'value' => $category['category_name'],
			 'href' => XenForo_Link::buildPublicLink('full:media/category', $category), 
		);

		if ($category['category_parent'])
		{
			$topCategory = $this->getModelFromCache('EWRmedio_Model_Categories')->getCategoryByID($category['category_parent']);
			$breadCrumbs = $this->getCrumbs($topCategory, $breadCrumbs);
		}

		return $breadCrumbs;
	}
}