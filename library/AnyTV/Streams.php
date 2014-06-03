<?php

class AnyTV_Streams
{
	public static function responseLayout(XenForo_ControllerPublic_Page $controller, XenForo_ControllerResponse_View $response)
	{
		$options = $options = XenForo_Application::get('options');
		$response->templateName = 'anytv_streams_page';
		$response->params['games'] = AnyTV_Games::getGames();
        $response->params['option'] = array('profile' => $options->facebookLink);
        $response->params['joinUs'] = $options->joinUsLink;

		$fieldsModel = new AnyTV_Models_CustomUserFieldModel();
		$modelValues = $fieldsModel->getFieldValuesByFieldId('twitchStreams');
		$modelValues = $fieldsModel->filterTwitchStreams($modelValues);

		$status = stripslashes(file_get_contents('https://api.twitch.tv/kraken/streams?channel='.implode(',', array_keys($modelValues))));
		$status = json_decode($status, 1);

		$response->params['streams'] = $status['streams'];
		$response->params['map'] = $modelValues;
	}
}
