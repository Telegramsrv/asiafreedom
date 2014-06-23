<?php

class AnyTV_News
{
    public static function responseLayout(
        XenForo_ControllerPublic_Page $controller,
        XenForo_ControllerResponse_View $response
    ) {
		$options = $options = XenForo_Application::get('options');
		echo "<pre>";
		print_r($options);
		exit;
		$response->templateName = 'anytv_news_page';
		$response->params['games'] = AnyTV_Games::getGames();
        $response->params['option'] = array('profile' => $options->facebookLink);
        $response->params['joinUs'] = $options->joinUsLink;
		$response->params['channel'] = $options->NewsChannel;
		$response->params['playlist'] = $options->NewsPlaylist;

		if(isset($_GET['cache'])) {
			ini_set('max_execution_time', 0);
			ini_set('memory_limit', -1);
			$fieldModel = XenForo_Model::create('AnyTV_Models_CustomUserFieldModel');
			$lists = $fieldModel->getYouTube();

			$items = array();
			$json = array();
			$i = 0;

			$host = XenForo_Application::get('db')->getConfig()['host'];
			$m = new MongoClient($host); // connect
			$db = $m->selectDB("asiafreedom_youtubers");
			foreach ($lists as $key => $value) {
				if(isset($value['youtube_id']) && $value['youtube_id'] != ''){
					$json = file_get_contents("https://www.googleapis.com/youtube/v3/playlists?channelId="
	                    .$value['youtube_id']."&part=id%2C+snippet%2C+contentDetails&key=AIzaSyAP14m25_1uScfmZObKqRI4lCwveb9E8Vk&maxResults=50"
	                );
					$json = json_decode($json, true);
					if(isset($json['items']) && !empty($json['items']))
						$items = array_merge($items, $json['items']);
				}
	            if(isset($items) && !empty($items)){
					$db->playlists->batchInsert($items);
				}
				$json = array();
				$items = array();
			}
		}
	}
}
