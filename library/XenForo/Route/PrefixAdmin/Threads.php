<?php

/**
 * Route prefix handler for threads in the admin control panel.
 */
class XenForo_Route_PrefixAdmin_Threads implements XenForo_Route_Interface
{
	/**
	 * Match a specific route for an already matched prefix.
	 *
	 * @see XenForo_Route_Interface::match()
	 */
	public function match($routePath, Zend_Controller_Request_Http $request, XenForo_Router $router)
	{
		return $router->getRouteMatch('XenForo_ControllerAdmin_Thread', $routePath, 'threadsPosts');
	}
}