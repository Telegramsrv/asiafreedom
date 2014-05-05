<?php

class EWRmedio_Route_Media implements XenForo_Route_Interface
{
	public function match($routePath, Zend_Controller_Request_Http $request, XenForo_Router $router)
	{
		$action = $router->resolveActionWithIntegerParam($routePath, $request, 'action_id');
		$action = $router->resolveActionAsPageNumber($action, $request);
		
		$actions = explode('/', $action);
		
		switch ($actions[0])
		{
			case 'media':			$controller = '_Media';				break;
			case 'account':			$controller = '_Account';			break;
			case 'admin':			$controller = '_Admin';				break;
			case 'category':		$controller = '_Category';			break;
			case 'comment':			$controller = '_Comment';			break;
			case 'keyword':			$controller = '_Keyword';			break;
			case 'playlist':		$controller = '_Playlist';			break;
			case 'user':			$controller = '_User';				break;
			case 'service':			$controller = '_Service';			break;
			default:				$controller = '';
		}
		
		if (!empty($actions[1]) && $actions[1] == 'keyword')
		{
			$controller = '_Keyword';
			$action = $router->resolveActionWithStringParam($routePath, $request, 'string_id');
		}
		
		return $router->getRouteMatch('EWRmedio_ControllerPublic_Media'.$controller, $action, 'media');
	}

	public function buildLink($originalPrefix, $outputPrefix, $action, $extension, $data, array &$extraParams)
	{
		$actions = explode('/', $action);
		
		switch ($actions[0])
		{
			case 'queue':
			case 'media':		$intParams = 'media_id';		$strParams = 'media_title';			break;
			case 'comment':		$intParams = 'comment_id';		$strParams = 'username';			break;
			case 'playlist':	$intParams = 'playlist_id';		$strParams = 'playlist_name';		break;
			case 'category':	$intParams = 'category_id';		$strParams = 'category_name';		break;
			case 'user':		$intParams = 'user_id';			$strParams = 'username';			break;
			case 'service':		$intParams = 'service_id';		$strParams = 'service_name';		break;
			case 'keyword':		$intParams = '';				$strParams = 'keyword_text';		break;
			default:			$intParams = '';				$strParams = '';					break;
		}
		
		$action = XenForo_Link::getPageNumberAsAction($action, $extraParams);

		if ($intParams)
		{
			return XenForo_Link::buildBasicLinkWithIntegerParam($outputPrefix, $action, $extension, $data, $intParams, $strParams);
		}
		else
		{
			return XenForo_Link::buildBasicLinkWithStringParam($outputPrefix, $action, $extension, $data, $strParams);
		}
	}
}