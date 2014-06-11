<?php

class AnyTV_AboutUs
{
    public static function resposeLayout(
        XenForo_ControllerPublic_Page $controller,
        XenForo_ControllerResponse_View $response
    ) {
        $options = $options = XenForo_Application::get('options');
		$response->templateName = 'about_us';
		$response->params['option'] = array('profile' => $options->facebookLink);
		$response->params['joinUs'] = $options->joinUsLink;
		$response->containerParams = array('banner' => '/zh/images/about-us.png');
		//$respomse->selectedTabId = 'about-us';

		/*
			code for the cron job actually
		 */
		if(isset($_GET['cache'])) {
			/*$values = $fieldModel->getFieldValuesByFieldId('youtubeUploads');
			$values = $values['youtubeUploads'];
			$lists = array_map(function($value) {
				return $value['value'];
			}, $values);*/
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
					if(isset($value['youtubeUploads']) && $value['youtubeUploads'] != ''){
						$json = file_get_contents("https://www.googleapis.com/youtube/v3/playlistItems?order=date&playlistId="
	                        .$value['youtubeUploads']."&part=snippet&key=AIzaSyAP14m25_1uScfmZObKqRI4lCwveb9E8Vk&maxResults=50"
	                        .(isset($json['nextPageToken'])
	                            ? "&pageToken=".$json['nextPageToken']
	                            : ""
	                        )
	                    );
						$json = json_decode($json, true);
						$items = array_merge($items, $json['items']);
	                }


					if(isset($value['access_token']) && $value['access_token'] != ''){
						$tags = array();
						$i=0;
						foreach($items as $item){
							$tags = file_get_contents("https://www.googleapis.com/youtube/v3/videos?part=snippet%2Cstatistics&id="
								.$item['snippet']['resourceId']['videoId']."&fields=items(snippet(tags)%2C+statistics)"
								."&access_token=".$value['access_token']
							);
							$tags = json_decode($tags, true);
							$items[$i]['snippet']['resourceId'] = array_merge($items[$i]['snippet']['resourceId'], $tags['items'][0]['snippet']);
							$items[$i]['snippet']['resourceId'] = array_merge($items[$i]['snippet']['resourceId'], $tags['items'][0]['statistics']);
							$i++;
						}
					}
				} while(isset($json['nextPageToken']));

				if(isset($items) && !empty($items))
					$db->videos->batchInsert($items);
				$items = array();
			}
		}
	}
}
