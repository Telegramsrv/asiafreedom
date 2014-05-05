<?php

/**
 * route for /
 * Class SimplePortal_RoutePublic_Landingpage
 */
class SimplePortal_RoutePublic_Landingpage implements XenForo_Route_Interface
{
	/**
	 * Match a specific route for an already matched prefix.
	 *
	 * @see XenForo_Route_Interface::match()
	 */
	public function match($routePath, Zend_Controller_Request_Http $request, XenForo_Router $router)
	{
		return $router->getRouteMatch('SimplePortal_ControllerPublic_LandingPage', $routePath, 'portal');
    }
}