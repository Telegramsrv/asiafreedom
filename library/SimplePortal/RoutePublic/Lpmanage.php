<?php

/**
 *
 * route for /portal
 *
 * Class SimplePortal_RoutePublic_Lpmanage
 */
class SimplePortal_RoutePublic_Lpmanage implements XenForo_Route_Interface
{
    protected $_subComponents = array(
        'categories' => array(
            'intId' => 'category_id',
            'title' => 'title',
            'controller' => 'SimplePortal_ControllerPublic_LandingPage'
        ),
        'manage-categories' => array(
            'intId' => 'category_id',
            'title' => 'title',
            'controller' => 'SimplePortal_ControllerPublic_Category'
        ),
        'manage-items' => array(
            'intId' => 'portalItem_id',
            'controller' => 'SimplePortal_ControllerPublic_Item'
        ),
        'create-new' => array(
            'stringId' => 'contentType',
            'controller' => 'SimplePortal_ControllerPublic_CreateNew'
        ),
        'inline-mod' => array(
            'controller' => 'SimplePortal_ControllerPublic_InlineModeration'
        ),
    );

    protected function _getSubcomponents(){
        return $this->_subComponents;
    }

    public function match($routePath, Zend_Controller_Request_Http $request, XenForo_Router $router)
    {
        $subcomponents = $this->_getSubcomponents();
        $defaultController = $subcomponents['categories']['controller'];

        $action = $router->getSubComponentAction($subcomponents, $routePath, $request, $defaultController);
        if ($action === false) {
            $action = $router->resolveActionWithIntegerParam($routePath, $request, 'portalItem_id');
        }
        return $router->getRouteMatch($defaultController, $action, 'portal');
    }

    public function buildLink($originalPrefix, $outputPrefix, $action, $extension, $data, array &$extraParams)
    {
        if ($action == 'categories' && isset($data['categorytitle'])){
            $data['title'] = $data['categorytitle'];
        }
        return XenForo_Link::buildSubComponentLink($this->_getSubcomponents(), $outputPrefix, $action, $extension, $data);
    }
}