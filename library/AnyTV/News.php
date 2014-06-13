<?php

class AnyTV_News
{
    public static function responseLayout(
        XenForo_ControllerPublic_Page $controller,
        XenForo_ControllerResponse_View $response
    ) {
		$options = $options = XenForo_Application::get('options');
		$response->templateName = 'anytv_news_page';
		$response->params['games'] = AnyTV_Games::getGames();
        $response->params['option'] = array('profile' => $options->facebookLink);
        $response->params['joinUs'] = $options->joinUsLink;
		$response->params['channel'] = $options->NewsChannel;
		$response->params['playlist'] = $options->NewsPlaylist;

		if(isset($_GET['cache'])) {
			ini_set('max_execution_time', 0);
			$fieldModel = XenForo_Model::create('AnyTV_Models_CustomUserFieldModel');
			$lists = $fieldModel->getYouTube();

			$items = array();
			$json = array();
			$i = 0;

			$host = XenForo_Application::get('db')->getConfig()['host'];
			$m = new MongoClient($host); // connect
			$db = $m->selectDB("asiafreedom_youtubers");

			foreach ($lists as $key => $value) {
				do {
					if(isset($value['youtube_id']) && $value['youtube_id'] != ''){
						$json = file_get_contents("https://www.googleapis.com/youtube/v3/playlists?channelId="
	                        .$value['youtube_id']."&part=snippet&key=AIzaSyAP14m25_1uScfmZObKqRI4lCwveb9E8Vk&maxResults=50"
	                    );
						$json = json_decode($json, true);
						$items = array_merge($items, $json['items']);
	                }
				} while(isset($json['nextPageToken']));

				if(isset($items) && !empty($items))
					$db->videos->batchInsert($items);
				$items = array();
			}
		}
	}
}
