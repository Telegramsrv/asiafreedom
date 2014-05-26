<?php
//Our class helper (we can put any helpers in here we want)
class AnyTV_Helpers
{
    public static function helperArrayGet()
    {
        $numargs = func_num_args();
	    $value = func_get_arg(0);

        if($numargs == 2) {
	   		return $value[func_get_arg(1)];
	   	}

	    for($i=1; $i<$numargs; $i++) {
	    	$value = $value[func_get_arg($i)];
	    }

	    return $value;
    }

    public static function cacheBust() {
        return date_timestamp_get(date_create());
    }

    public static function cacheVideos($youtubeId) {

    }

    public static function getFeaturedUsers() {
        $mydb = XenForo_Application::get('db');
        $featured = $mydb->fetchAll("
            SELECT *
            FROM `anytv_user_featured`
            WHERE `active` = 1");

        $userModel = XenForo_Model::create('XenForo_Model_User');
        return $userModel->getUsersByIds(array_map(function($e) {
            return $e['user_id'];
        }, $featured));
    }

    public static function getFeaturedVideos($skip = null) {
        $m = new MongoClient();
        $db = $m->selectDB("asiafreedom_youtubers");
        $mydb = XenForo_Application::get('db');
        $featured = $mydb->fetchAll("
            SELECT *
            FROM `anytv_video_featured`
            WHERE `active` = 1");

        $featuredVideos = array();
        $featuredVideosMap = array();

        foreach($featured as $data) {
            $featuredVideos[] = new MongoID($data['video_id']);
            $featuredVideosMap[$data['video_id']] = $data;
        }

        $featuredMongo = $db->videos->find(
            array(
                '_id' => array(
                    '$in' => $featuredVideos
                )
            )
        )->limit(12);
        if($skip) {
            $featuredMongo = $featuredMongo->skip($skip);
        }

        $featuredMongo = iterator_to_array($featuredMongo);

        foreach ($featuredMongo as $key => &$value) {
            $value['featured_id'] = $featuredVideosMap[$key]['id'];
            $value['featured'] = 1;
        }

        return $featuredMongo;
    }

    public static function createHash($data){
        $hash = '#!/';
        $hash = '#!/video/'
            + isset($data['id']) && isset($data['id']['videoId'])
                ? $data['id']['videoId']
                : $data['snippet']['resourceId']['videoId'];
        if($data['snippet']['playlistId']) {
            $hash ='#!/playlist/'+$data['snippet']['playlistId']+'/video/'
                +(isset($data['id']) && isset($data['id']['videoId'])
                    ? $data['id']['videoId']
                    : $data['snippet']['resourceId']['videoId'])
                +'/'+($data['snippet']['position']);
        }

        return $hash;
    }

    public static function numToMonth($m) {
        $arr = ['', 'Janary', 'Feburary', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'];
        return $arr[$m];
    }
}
?>
