<?php

class EWRmedio_Template_Helper
{
	public static function getMedioHighUrl($media)
	{
		$highLoc = XenForo_Application::$externalDataPath . "/media/high/$media[media_id].jpg";
		$lowLoc = XenForo_Application::$externalDataPath . "/media/$media[media_id].jpg";
	
		if (file_exists($highLoc))
		{
			return XenForo_Application::$externalDataUrl . "/media/high/$media[media_id].jpg";
		}
		elseif (file_exists($lowLoc))
		{
			return XenForo_Application::$externalDataUrl . "/media/$media[media_id].jpg";
		}
		
		return "styles/8wayrun/media_video.jpg";
	}
	
	public static function getMedioLowUrl($media)
	{
		$lowLoc = XenForo_Application::$externalDataPath . "/media/$media[media_id].jpg";
		
		if (file_exists($lowLoc))
		{
			return XenForo_Application::$externalDataUrl . "/media/$media[media_id].jpg";
		}
		
		return "styles/8wayrun/media_video.jpg";
	}
	
	public static function getKeywordIcon($keyword)
	{
		$icon = XenForo_Application::getInstance()->getRootDir() . "/styles/8wayrun/keywords/$keyword[keyword_text].jpg";
		
		if (file_exists($icon))
		{
			return "styles/8wayrun/keywords/$keyword[keyword_text].jpg";
		}
		
		return "styles/8wayrun/keywords/_.jpg";
	}
}