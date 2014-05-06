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
}
?>