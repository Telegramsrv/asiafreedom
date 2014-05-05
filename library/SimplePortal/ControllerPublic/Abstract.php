<?php

class SimplePortal_ControllerPublic_Abstract extends XenForo_ControllerPublic_Abstract
{

    /**
     * @return mixed
     */
    public function getPortalConfig()
    {
        return XenForo_Application::getConfig()->extra_portal;
    }

    protected function getAllCategories()
    {
        return $this->getModelFromCache('SimplePortal_Model_Category')->getAllCategories();
    }

    /**
     * @param $controllerResponse
     * @param $controllerName
     * @param $action
     */
    protected function _postDispatch($controllerResponse, $controllerName, $action)
    {
        parent::_postDispatch($controllerResponse, $controllerName, $action);

        if ($controllerResponse instanceof XenForo_ControllerResponse_View) {

            if (!XenForo_Autoloader::getInstance()->autoload('SimplePortal_BrandingFree')) {
                $controllerResponse->params['show_portal_branding'] = true;
            }

            $portalContainer = $this->getPortalConfig();
            if (isset($portalContainer->container)) {
                $controllerResponse->containerParams = array('containerTemplate' => $portalContainer->container);
            }


            if (SimplePortal_Static::option('defaultSidebar')) {
                $viewParams = $this->_getDefaultSidebarParams();
                $controllerResponse->params += $viewParams;
            }
        }
    }

    protected function _getDefaultSidebarParams()
    {
        $viewParams = array(
            'showDefaultSidebar' => true,
            'onlineUsers' => $this->_getSessionActivityList(),
            'boardTotals' => $this->_getBoardTotals()
        );
        return $viewParams;
    }


    protected function _getSessionActivityList()
    {
        $visitor = XenForo_Visitor::getInstance();

        /** @var $sessionModel XenForo_Model_Session */
        $sessionModel = $this->getModelFromCache('XenForo_Model_Session');

        return $sessionModel->getSessionActivityQuickList(
            $visitor->toArray(),
            array('cutOff' => array('>', $sessionModel->getOnlineStatusTimeout())),
            ($visitor['user_id'] ? $visitor->toArray() : null)
        );

    }

    protected function _getBoardTotals()
    {
        $boardTotals = $this->getModelFromCache('XenForo_Model_DataRegistry')->get('boardTotals');
        if (!$boardTotals) {
            $boardTotals = $this->getModelFromCache('XenForo_Model_Counters')->rebuildBoardTotalsCounter();
        }
        return $boardTotals;
    }


    /**
     * @return SimplePortal_ControllerHelper_Category
     */
    protected function getPortalCategoryHelper()
    {
        return $this->getHelper('SimplePortal_ControllerHelper_Category');
    }

    /**
     * @return SimplePortal_Model_Category
     */
    public function getCategoryModel()
    {
        return $this->getModelFromCache('SimplePortal_Model_Category');
    }

    protected function getCategoryOrError($categoryId = null)
    {
        if (!$categoryId) {
            $categoryId = $this->_input->filterSingle('category_id', XenForo_Input::UINT);
        }

        return $this->getRecordOrError($categoryId, $this->getCategoryModel(), 'getCategoryById', 'requested_portalcategory_not_found');
    }
}