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
		$response->params['videos'] = $videos;
		$response->params['game'] = $games[$game]['name'];
		$response->params['games'] = $games;
        
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