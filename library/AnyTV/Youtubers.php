<?php

class AnyTV_Youtubers
{
	public static function resposeLayout(XenForo_ControllerPublic_Page $controller, XenForo_ControllerResponse_View $response)
	{
		// fetch all recent members who registered
		$userModel = $controller->getModelFromCache('XenForo_Model_User');
		$response->params['users'] = $userModel->getLatestUsers(array(), array('limit' => 16));
        $options = $options = XenForo_Application::get('options');
        $response->params['option'] = array('profile' => $options->facebookLink);
		$response->templateName = 'youtubers_list_page';
		$response->containerParams = array('banner' => '/images/youtubers-list.png');
	}
}
