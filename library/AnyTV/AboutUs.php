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

        if(!empty($_POST)) {
            $client = new Google_Client();
            $client->setApplicationName('AsiaFreedom');
            $client->setRedirectUri("http://".$_SERVER['SERVER_NAME']);
            $client->setClientId('556525497714-ci74k99ts0ar37fo5pv0b5ea28es2reg.apps.googleusercontent.com');
            $client->setClientSecret('RDqts1yS-MMG2fREcOWIzZgk');
            $client->setRedirectUri('http://'.$_SERVER['SERVER_NAME'].'/zh/index.php?account/user-field-category&user_field_category_id=2');
            $auth = $client->authenticate($_POST['code']);
            $token = $client->getAccessToken();
            if($token) {
                $auth = json_decode($token);
                die(json_encode($auth));
            }
        }
		/*
			code for the cron job actually
		 */sssssss
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
            $videoCount = 0;
            $clientId = '556525497714-ci74k99ts0ar37fo5pv0b5ea28es2reg.apps.googleusercontent.com';
            $clientSecret = 'RDqts1yS-MMG2fREcOWIzZgk';
	        //$path = '/Users/Public/log.txt';
	        $path = __DIR__.'/../../log.txt';
            foreach ($lists as $key => $value) {
                $access=null;
				file_put_contents($path, ":\n\r\n: =====================================START LOOP===============================================", FILE_APPEND);
                file_put_contents($path, ":\n\r\n: GET ACCESS TOKEN: ".(isset($value['refresh_token'])
                    ? $value['refresh_token'] : 'none'), FILE_APPEND);;
                if(isset($value['refresh_token'])) {
                    $ch =curl_init('https://accounts.google.com/o/oauth2/token');
                    $data = 'client_id='.$clientId
                        .'&client_secret='.$clientSecret
                        .'&refresh_token='.$value['refresh_token']
                        .'&grant_type=refresh_token';
                    curl_setopt($ch, CURLOPT_POST, 4);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $access = curl_exec($ch);
                    curl_close($ch);
                    $access = json_decode($access, 1);
                }

                file_put_contents($path, ":\n\r\n: tokenn taken: ".json_encode($access ? $access: ''), FILE_APPEND);
                $hasAccess = ($access && isset($access['access_token']) && $access['access_token'] != '');
			    //if($hasAccess) {
				do {
					if(isset($value['youtubeUploads']) && $value['youtubeUploads'] != ''){
						do {
							$json = @file_get_contents("https://www.googleapis.com/youtube/v3/playlistItems?order=date&playlistId="
		                        .$value['youtubeUploads']."&part=snippet&key=AIzaSyAZ8ezBGVLa1OGIe0g2lPqApwb0-F8zaNU&maxResults=50"
		                        .(isset($json['nextPageToken'])
		                            ? "&pageToken=".$json['nextPageToken'] : ""
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
						.$item['snippet']['resourceId']['videoId']."&fields=items(snippet(channelId%2Ctags)%2C+statistics)"
						.($hasAccess ? "&access_token=".$access['access_token'] : "&key=AIzaSyAZ8ezBGVLa1OGIe0g2lPqApwb0-F8zaNU&maxResults=50");

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
                //}
			}
		}
	}
}
