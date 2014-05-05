<?php

class AnyTV_AboutUs
{
	public static function resposeLayout(XenForo_ControllerPublic_Page $controller, XenForo_ControllerResponse_View $response)
	{
		$response->templateName = 'about_us';
		$response->params['option'] = array('profile' => 'http://www.facebook.com/mcnfreedom');
		$response->containerParams = array('banner' => '/images/about-us.png');
	

		/*
			code for the cron job actually
		 */
		
		ini_set('max_execution_time', 0);	
		$fieldModel = XenForo_Model::create('AnyTV_Models_CustomUserFieldModel');
		$values = $fieldModel->getFieldValuesByFieldId('youtubeUploads');
		$values = $values['youtubeUploads'];
		$lists = array_map(function($value) {
			return $value['value'];
		}, $values);

		$lists = array_filter($lists);

		$items = array();
		$json = array();
		$i = 0; 
		
		$m = new MongoClient(); // connect
		$db = $m->selectDB("asiafreedom_youtubers");

		foreach ($lists as $key => $value) {
			do {
				$json = file_get_contents("https://www.googleapis.com/youtube/v3/playlistItems?order=date&playlistId="
					.$value."&part=snippet&key=AIzaSyAP14m25_1uScfmZObKqRI4lCwveb9E8Vk&maxResults=50".(isset($json['nextPageToken']) ? "&pageToken=".$json['nextPageToken'] : "" ));
				$json = json_decode($json, true);
				$items = array_merge($items, $json['items']);
			} while(isset($json['nextPageToken']));

			$db->videos->batchInsert($items);
			$items = array();
		}
	}
}