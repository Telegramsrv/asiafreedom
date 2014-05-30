<?php

class AnyTV_Shows
{
	public static function responseLayout(XenForo_ControllerPublic_Page $controller, XenForo_ControllerResponse_View $response)
	{
		$options = $options = XenForo_Application::get('options');
		$response->templateName = 'anytv_shows_page';
		$response->params['games'] = AnyTV_Games::getGames();
        $response->params['option'] = array('profile' => 'http://www.facebook.com/mcnfreedom');¬
        $response->params['channel'] = $options->ShowsChannel;
		$response->params['playlist'] = $options->ShowsPlaylist;
	}
}
