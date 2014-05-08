<?php

class AnyTV_Games
{
	public static function resposeLayout(XenForo_ControllerPublic_Page $controller, XenForo_ControllerResponse_View $response)
	{
		$game = $_GET['game'];
		$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? $_GET['limit'] : 6;
		$offset = isset($_GET['offset']) && is_numeric($_GET['offset']) ? $_GET['offset'] : 0;
		$games = AnyTV_Games::getGames();
		$videos = AnyTV_Games::getVideos($games[$game]['name'], $limit, $offset);
		
		$response->templateName = 'anytv_games_page';
		$response->params['game'] = $games[$game]['name'];
		$response->params['games'] = $games;
		$response->params['globalVideos'] = array();
		$i = 0;
		$search = false;
		foreach ($videos as &$video) {
			$channelId = $video['snippet']['channelId'];
			$date = $video['snippet']['publishedAt'];
			$time = explode('T', $date);
			$date = $time[0];
			$time = $time[1];
			$date = explode('-', $date);
			$time = explode('.', $time);
			$params = array();
			$video['ID'] = $params['ID'] = isset($video['id']) && isset($video['id']['videoId']) 
				? $video['id']['videoId']
				: $video['snippet']['resourceId']['videoId'];
			$video['YTLINK'] = $params['YTLINK'] = 'https://www.youtube.com/watch?v='+$params['ID'];
			$video['PLID'] = $params['PLID'] = $video['snippet']['playlistId'] ? $video['snippet']['playlistId'] : 'false';
			$video['INDEX'] = $params['INDEX'] = $video['snippet']['playlistId'] ? $video['snippet']['position'] : 'false';
			$video['IMG'] = $params['IMG'] = $video['snippet']['thumbnails']['default']['url'];
			$video['LINK'] = $params['LINK'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";+AnyTV_Helpers::createHash($video);
			$video['TITLE'] = $params['TITLE'] = $video['snippet']['title'];
			$video['MONTH'] = $params['MONTH'] = AnyTV_Helpers::numToMonth((int)$date[1]);
			$video['DAY'] = $params['DAY'] = $date[2];
			$video['YEAR'] = $params['YEAR'] = $date[0];
			$video['TIME'] = $params['TIME'] = $time[0];
			$video['DETAILS'] = $params['DETAILS'] = str_replace('\n', '<br />', $video['snippet']['description']);
			$video['SEARCH'] = $params['SEARCH'] = $search;

			$response->params['globalVideos'][$params['ID']] = $params;
			$i++;
		}

		$response->params['videos'] = $videos;
		$response->params['globalVideos'] = json_encode($response->params['globalVideos']);
        
        if(isset($_GET['ajax'])) {
        	die(json_encode($videos));
        }
	}

	public static function getVideos($game, $limit, $offset) {
		$m = new MongoClient();
        $db = $m->selectDB("asiafreedom_youtubers");
        $videos = $db->videos
            ->find(
            	array('snippet.title' => array('$regex' => $game))
            )->sort(
            	array('snippet.publishedAt'=>-1)
            )->limit($limit)
           ->skip($offset);
        return iterator_to_array($videos);
	}

	public static function getGames() {
		$options = XenForo_Application::get('options');
		$games = array();
		//get the games
		foreach ($options->anytv_categories_categories['game_id'] as $key => $value) {
			$games[$options->anytv_categories_categories['game_id'][$key]] = array(
				'id'	=> $options->anytv_categories_categories['game_id'][$key],
				'name' 	=> $options->anytv_categories_categories['game_name'][$key],
				'image' => $options->anytv_categories_categories['game_image'][$key]
			);
		}

		return $games;
	}
}