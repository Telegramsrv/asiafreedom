<?php

/**
 * INDEX CONTROLLER
 *
 */
class SimplePortal_ControllerPublic_LandingPage extends SimplePortal_ControllerPublic_Abstract
{

    protected function getExtraContent($page, $category){
        return false;
    }

	public function actionIndex()
	{
		if (isset($this->getPortalConfig()->only_container, $this->getPortalConfig()->container)) {
			return $this->responseView('SimplePortal_ViewPublic_Index', $this->getPortalConfig()->container, array());
		}
		$page = max(1, $this->_input->filterSingle('page', XenForo_Input::UINT));
		$categoryId = $this->_input->filterSingle('category_id', XenForo_Input::UINT);
		$category = $this->getPortalCategoryHelper()->assertCategoryValid($categoryId);

        $extraReturn = $this->getExtraContent($page,$category);
        if ($extraReturn && $extraReturn instanceOf XenForo_ControllerResponse_Abstract){
            return $extraReturn;
        }

		if ($category){
			$categoryId = $category['category_id'];
		}else {
			$categoryId = 0;
		}

		$itemsPerPage = SimplePortal_Static::option('perPage');

		if ($categoryId){
			$this->canonicalizeRequestUrl(
				XenForo_Link::buildPublicLink('portal/categories', $category, array('page' => $page))
			);
		}
		else {
			$this->canonicalizeRequestUrl(
				XenForo_Link::buildPublicLink('portal', $category, array('page' => $page))
			);
		}

		$conditions = array(
			'category_id' => $categoryId
		);

		$fetchOptions = array(
			'perPage' => $itemsPerPage,
			'page' => $page
		);

        $categories = $this->getModelFromCache('SimplePortal_Model_Category')->getAllCategories();

        $fetchOptions = SimplePortal_Static::getItemModel()->getDefaultFetchOptions($fetchOptions);

        $fetchOptions += array('join' => SimplePortal_Model_PortalItem::FETCH_CATEGORY );

		$items = SimplePortal_Static::getItemModel()->getPortalItems($conditions, $fetchOptions);
		$items = SimplePortal_Static::getItemModel()->fetchPortalItemsData($items);
        $itemHandlers = SimplePortal_Static::getItemModel()->getPortalItemHandlerClasses();

		$viewParams = array(
			'items' => $items,
            'itemCount' => (count($items)),
			'totalItems' => SimplePortal_Static::getItemModel()->countItems($conditions),
			'page' => $page,
			'postsPerPage' => $itemsPerPage,
			'categories' => $categories,
            'handlerClasses' => $itemHandlers,
		);




        if (isset($this->getPortalConfig()->showInlineAttachments) OR !XenForo_Application::getOptions()->strip_portal_attachments){
            $viewParams['showInlineAttachments'] = true;
        }

		if ($categoryId) {
			$viewParams += array('current_category' => $categories[$categoryId]);
			$viewParams += array('linkparams' => array('category_id' => $categoryId));
		} else {
			$viewParams += array('current_category' => false);
		}

		return $this->wrapPage($viewParams);
	}



	protected function wrapPage($viewParams)
	{
		return $this->responseView('SimplePortal_ViewPublic_Index', 'el_portal_index', $viewParams);
	}


	public static function getSessionActivityDetailsForList(array $activities)
	{
		$categoryIds = array();
		foreach ($activities AS $activity) {
			if (! empty($activity['params']['category_id'])) {
				$categoryIds[$activity['params']['category_id']] = intval($activity['params']['category_id']);
			}
		}

		$categoryData = array();

		if ($categoryIds) {
			/* @var $categoryModel SimplePortal_Model_Category */
			$categoryModel = XenForo_Model::create('SimplePortal_Model_Category');

			$categories = $categoryModel->getCategories(array('category_id' => $categoryIds));

			foreach ($categories AS $category) {
				$categoryData[$category['category_id']] = array(
					'title' => $category['title'],
					'url' => XenForo_Link::buildPublicLink('portal/categories', $category),
				);
			}
		}

		$output = array();
		foreach ($activities AS $key => $activity) {
			$category = false;
			if (! empty($activity['params']['category_id'])) {
				$categoryId = $activity['params']['category_id'];
				if (isset($categoryData[$categoryId])) {
					$category = $categoryData[$categoryId];
				}
			}

			if ($category) {
				$output[$key] = array(
					new XenForo_Phrase('el_portal_viewing_portal'),
					$category['title'],
					$category['url'],
					false
				);
			} else {
				$output[$key] = new XenForo_Phrase('el_portal_viewing_portal');
			}
		}
		return $output;
	}
}