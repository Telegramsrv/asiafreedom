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


		if(isset($_GET['count'])) {
			$toParse = file_get_contents('https://www.youtube.com/watch?v='.$_GET['videoId']);
			preg_match('/<span class="watch-view-count">([0-9]+).*<\/span>/', $toParse, $matches);
			die($matches[1]);
		}

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
			$videoCount = 0;
	        $path = '/Library/WebServer/Documents/asiafreedom/zh/log.txt';
			foreach ($lists as $key => $value) {
				file_put_contents($path, ":\n\r\n: =====================================START LOOP===============================================", FILE_APPEND);
				
				$hasAccess = (isset($value['access_token']) && $value['access_token'] != '');
				
				do {
					if(isset($value['youtubeUploads']) && $value['youtubeUploads'] != ''){
						do {
							$json = @file_get_contents("https://www.googleapis.com/youtube/v3/playlistItems?order=date&playlistId="
		                        .$value['youtubeUploads']."&part=snippet&key=AIzaSyAP14m25_1uScfmZObKqRI4lCwveb9E8Vk&maxResults=50"
		                        .(isset($json['nextPageToken'])
		                            ? "&pageToken=".$json['nextPageToken']
		                            : ""
		                        )
		                    );
						} while(!$json);

						$json = json_decode($json, true);
						$items = array_merge($items, $json['items']);

						file_put_contents($path, ":\n\r\n: Uploads:".$value['youtubeUploads'], FILE_APPEND);
	                }
				} while(isset($json['nextPageToken']));

				$tags = array();
				$i=0;
				foreach($items as $item){
					$requestUrl = "https://www.googleapis.com/youtube/v3/videos?part=snippet%2Cstatistics&id="
						.$item['snippet']['resourceId']['videoId']."&fields=items(snippet".($hasAccess ? '(channelId%2Ctags)' : '(channelId)')
						."%2C+statistics)"
						.($hasAccess ? "&access_token=".$value['access_token'] : "&key=AIzaSyAP14m25_1uScfmZObKqRI4lCwveb9E8Vk&maxResults=50");

					file_put_contents($path, ":\n\r\n: REQUEST ".++$videoCount.": ".$requestUrl, FILE_APPEND);

					do {
						$tags = @file_get_contents($requestUrl);
					}while(!$tags);

	                file_put_contents($path, ":\n\r\n:RESULT: ".$tags, FILE_APPEND);

					$tags = json_decode($tags, true);
					$items[$i]['snippet']['meta'] = array(
						'tags' => isset($tags['items'][0]['snippet']['tags']) ?
							$tags['items'][0]['snippet']['tags'] : array(), 
						'statistics' => $tags['items'][0]['statistics']
					);
					$i++;
				}
    
                file_put_contents($path, ":\n\r\n: =======================================END LOOP=============================================", FILE_APPEND);

				if(isset($items) && !empty($items))
					$db->videos->batchInsert($items);
				$items = array();
			}
		}
	}
}
