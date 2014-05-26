<?php

class AnyTV_Route_AnyTV implements XenForo_Route_Interface
{
    public function match(
        $routePath,
        Zend_Controller_Request_Http $request,
        XenForo_Router $router
    ) {
		$components = explode('/', $routePath);
		$subPrefix = strtolower(array_shift($components));

		$strParams = '';
		$slice = true;

		switch ($subPrefix)
		{
			case 'featured':		$controllerName = '_Featured'; break;
			case 'hasmedia': 		$controllerName = '_HasMedia'; break;
			case 'user_settings':	$controllerName = '_UserSettings'; break;
			default:				$controllerName = '_Featured';
		}

		$routePathAction = ($slice ? implode('/', array_slice($components, 0, 2)) : 'index');

		$routePathAction = strlen($routePathAction) ? $routePathAction : 'index';

		$action = $router->resolveActionWithStringParam($routePathAction, $request, $strParams);

		return $router->getRouteMatch('AnyTV_ControllerAdmin'.$controllerName, $action, 'AnyTV', $routePath);
	}
}
