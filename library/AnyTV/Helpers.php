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

    public static function cacheVideos($youtubeId) {
    	
    }
}
?>