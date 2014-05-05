<?php

class SimplePortal_Model_Category extends XenForo_Model
{

	public function getDefaultCategory()
	{
		return array(
			'display_order' => 10
		);
	}


	public function getCategoryById($categoryId)
	{
		return $this->_getDb()->fetchRow('
            SELECT *
            FROM xf_portalcategory
            WHERE category_id = ?
            ', $categoryId);
	}

	public function getAllCategories()
	{
        if (!XenForo_Application::isRegistered('elportal_allcategories')){
            $categories = $this->getCategories();
            XenForo_Application::set('elportal_allcategories', $categories);
        }

		return XenForo_Application::get('elportal_allcategories');
	}


	public function countCategory(array $conditions = array(), array $fetchOptions = array())
	{
		$whereConditions = $this->prepareCategoryConditions($conditions, $fetchOptions);
		return $this->_getDb()->fetchOne('
			SELECT COUNT(*)
			FROM xf_portalcategory as category
			WHERE ' . $whereConditions
		);
	}


	public function getCategories(array $conditions = array(), array $fetchOptions = array())
	{
		$whereConditions = $this->prepareCategoryConditions($conditions, $fetchOptions);
		$limitOptions = $this->prepareLimitFetchOptions($fetchOptions);

		return $this->fetchAllKeyed($this->limitQueryResults('
        SELECT category.*
        FROM xf_portalcategory as category
        WHERE ' . $whereConditions . '
        ORDER BY display_order
        ', $limitOptions['limit'], $limitOptions['offset']
		), 'category_id');
	}


	public function prepareCategoryConditions(array $conditions, array &$fetchOptions)
	{
		$db = $this->_getDb();
		$sqlConditions = array();

		if (!empty($conditions['category_id']))
		{
			if (is_array($conditions['category_id']))
			{
				$sqlConditions[] = 'category.category_id IN (' . $db->quote($conditions['category_id']) . ')';
			}
			else
			{
				$sqlConditions[] = 'category.category_id = ' . $db->quote($conditions['category_id']);
			}
		}

		return $this->getConditionsForClause($sqlConditions);
	}

}