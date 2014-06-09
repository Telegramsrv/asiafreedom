<?php

/**
 * Route handler for prefix 'sso-slave'
 */
class Waindigo_XenSso_Slave_Route_Prefix_Consumer implements XenForo_Route_Interface
{

    /**
     * Match to action
     *
     * @param string $routePath
     * @param Zend_Controller_Request_Http $request
     * @param XenForo_Router $router
     * @return $router->getRouteMatch
     */
    public function match($routePath, Zend_Controller_Request_Http $request, XenForo_Router $router)
    {
        $action = explode('/', $routePath);
        return $router->getRouteMatch('Waindigo_XenSso_Slave_ControllerPublic_Consumer', $action[0]);
    } /* END match */
}