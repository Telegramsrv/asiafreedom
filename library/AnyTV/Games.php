<?php

class AnyTV_Games
{
	public static function resposeLayout(XenForo_ControllerPublic_Page $controller, XenForo_ControllerResponse_View $response)
	{
		$game = $_GET['game'];
		$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? $_GET['limit'] : 0;
		$offset = isset($_GET['offset']) && is_numeric($_GET['offset']) ? $_GET['offset'] : 0;
		$games = AnyTV_Games::getGames();
		$videos = AnyTV_Games::getVideos($games[$game]['name'], $limit, $offset, false, isset($game[$game]['id']));
		$playlists = AnyTV_Games::getPlaylist($game);
		$options = $options = XenForo_Application::get('options');
        $response->params['option'] = array('profile' => $options->facebookLink);
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
            $video['PLID'] = $params['PLID'] = $video['snippet']['playlistId']
                ? $video['snippet']['playlistId']
                : 'false';
            $video['INDEX'] = $params['INDEX'] = $video['snippet']['playlistId']
                ? $video['snippet']['position']
                : 'false';
			$video['IMG'] = $params['IMG'] = $video['snippet']['thumbnails']['default']['url'];
			$video['LINK'] = $params['LINK'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]".AnyTV_Helpers::createHash($video);
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
		$response->params['globalPlaylists'] = array();
		foreach ($playlists as &$playlist) {
			$channelId = $playlist['snippet']['channelId'];
			$date = $playlist['snippet']['publishedAt'];
			$time = explode('T', $date);
			$date = $time[0];
			$time = $time[1];
			$date = explode('-', $date);
			$time = explode('.', $time);
			$params = array();
			$playlist['ID'] = $params['ID'] = isset($playlist['id']) && isset($playlist['id'])
				? $playlist['id']
				: $playlist['id'];
			$playlist['IMG'] = $params['IMG'] = $playlist['snippet']['thumbnails']['default']['url'];
			$playlist['TITLE'] = $params['TITLE'] = $playlist['snippet']['title'];
			$playlist['MONTH'] = $params['MONTH'] = AnyTV_Helpers::numToMonth((int)$date[1]);
			$playlist['DAY'] = $params['DAY'] = $date[2];
			$playlist['YEAR'] = $params['YEAR'] = $date[0];
			$playlist['TIME'] = $params['TIME'] = $time[0];
			$playlist['DETAILS'] = $params['DETAILS'] = str_replace('\n', '<br />', $playlist['snippet']['description']);
			$playlist['SEARCH'] = $params['SEARCH'] = $search;

			$response->params['globalPlaylists'][$params['ID']] = $params;
			$i++;
		}
		
		$response->params['videos'] = $videos;
		$response->params['playlists'] = $playlists;
		$response->params['totalVideos'] = AnyTV_Games::getVideos($games[$game]['name'], 0, 0, true);
		$response->params['globalVideos'] = json_encode($response->params['globalVideos']);
		$response->params['globalPlaylists'] = json_encode($response->params['globalPlaylists']);

		/*echo "<pre>";
        print_r($response);
        exit;*/
        if(isset($_GET['ajax'])) {
        	die(json_encode($videos));
        }
	}

    public static function responseGamesList(
        XenForo_ControllerPublic_Page $controller,
        XenForo_ControllerResponse_View $response
    ) {
        $limit = isset($_GET['limit']) && is_numeric($_GET['limit'])
            ? $_GET['limit'] : 6;
        $offset = isset($_GET['offset']) && is_numeric($_GET['offset'])
            ? $_GET['offset'] : 0;
        $games = AnyTV_Games::getGames();
        $options = $options = XenForo_Application::get('options');
		$response->params['option'] = array('profile' => $options->facebookLink);
		$response->containerParams = array('bannerNew' => new Xenforo_Phrase('games'));

		$response->templateName = 'anytv_games_list_page';
		$response->params['games'] = $games;
	}

	public static function getVideos($game, $limit=0, $offset=0, $count = false, $id=null) {
		$host = XenForo_Application::get('db')->getConfig()['host'];
   		$m = new MongoClient($host); // connect
        $db = $m->selectDB("asiafreedom_youtubers");
        if (isset($id)) {
        	$tags = AnyTV_Games::getTagsById($game);
			$tags = explode(',', $tags);
			foreach ($tags as $key => $value)
				$tags[$key] = new MongoRegex("/^".rtrim(ltrim($value))."/i");

			$videos = $db->videos
	            ->find(
	        		array(
	        			'$or' => array(
							array( 'snippet.title' => array('$regex' => new MongoRegex("/^".$game."/i")) ),
							array( 'snippet.meta.tags' => array('$in' => $tags) )
						)
	            	)
	            )->sort(
	            	array('snippet.publishedAt'=>-1)
	            )->limit($limit)
	           ->skip($offset);
        } else{
        	$videos = $db->videos
	            ->find(
	        		array( 'snippet.title' => array('$regex' => new MongoRegex("/^".$game."/i")) )		
	            )->sort(
	            	array('snippet.publishedAt'=>-1)
	            )->limit($limit)
	           ->skip($offset);
        }

      	if($count) {
          	return $db->videos->count(
              	array(
                  	'snippet.title' => array( '$regex' => $game)
              )
          );
      	}
        return iterator_to_array($videos);
	}

	public static function getPlaylist($game) {
		$host = XenForo_Application::get('db')->getConfig()['host'];
   		$m = new MongoClient($host); // connect
        $db = $m->selectDB("asiafreedom_youtubers");

    	$tags = AnyTV_Games::getTagsById($game);
		$tags = explode(',', $tags);
		foreach ($tags as $key => $value)
			$tags[$key] = new MongoRegex("/^".rtrim(ltrim($value))."/i");

		$playlists = $db->playlists
            ->find(
				array( 'snippet.tags' => array('$in' => $tags) )
            )->sort(
            	array('snippet.publishedAt'=>-1)
            );
        return iterator_to_array($playlists);
	}

	public static function getFeatured() {
		$mydb = XenForo_Application::get('db');
        $featured = $mydb->fetchAll("
			SELECT *
			FROM `anytv_game_featured`
			WHERE `active` = 1");

        return AnyTV_Games::getGames(
        	array_map(function($data) { return $data['game_id']; }, $featured),
        	array_map(function($data) { return $data['id']; }, $featured));
	}

	public static function getTags() {
		$mydb = XenForo_Application::get('db');
        $tags = $mydb->fetchAll("
			SELECT *
			FROM `anytv_game_tags`");

        return $tags;
	}

	public static function getTagsById($game_id) {
		$mydb = XenForo_Application::get('db');
        $tags = $mydb->fetchAll("
			SELECT tags
			FROM `anytv_game_tags`
			WHERE `game_id` ='".$game_id."'");
        
        if($tags) return $tags[0]['tags'];
		else $tags;
	}

	public static function getGames($filter = array(), $featuredIds = array()) {
		$options = XenForo_Application::get('options');
		$games = array();

		//get the games
		foreach ($options->anytv_categories_categories['game_id'] as $key => $value) {
            if(count($filter)
                && !in_array(
                    $options->anytv_categories_categories['game_id'][$key],
                    $filter
                )
            ) {
                continue;
            }
			$games[$options->anytv_categories_categories['game_id'][$key]] = array(
				'id'	=> $options->anytv_categories_categories['game_id'][$key],
				'name' 	=> $options->anytv_categories_categories['game_name'][$key],
				'image' => $options->anytv_categories_categories['game_image'][$key],
				'featured' => count($filter) ? 1 : 0,
                'featured_id' => count($filter)
                ? $featuredIds[array_search(
                    $options->anytv_categories_categories['game_id'][$key],
                    $filter
                )]
                : 0
			);
		}

		return $games;
	}
}
