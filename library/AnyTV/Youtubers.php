<?php

class AnyTV_Youtubers
{
	public static function resposeLayout(XenForo_ControllerPublic_Page $controller, XenForo_ControllerResponse_View $response)
	{
		// fetch all recent members who registered

		$userModel = $controller->getModelFromCache('XenForo_Model_User');
		$fieldsModel =  XenForo_Model::create('AnyTV_Models_CustomUserFieldModel');
		$response->params['users'] = $fieldsModel->getFieldValuesByFieldId('youtube_id');
		$response->params['users'] = $response->params['users']['youtube_id'];
        $options = $options = XenForo_Application::get('options');
        $response->params['option'] = array('profile' => $options->facebookLink);
		$response->templateName = 'youtubers_list_page';
		$response->containerParams = array('banner' => '/zh/images/youtubers-list.png');
	}
}
