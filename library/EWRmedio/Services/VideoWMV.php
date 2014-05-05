<?php

class EWRmedio_Services_VideoWMV extends XenForo_Model
{
	public static function dumpMedia($service, $matches)
	{
		$file = XenForo_Application::get('options')->boardUrl.'/'.XenForo_Application::$externalDataPath.'/local/'.$matches['sval1'];

		if (!file_exists(XenForo_Application::$externalDataPath.'/local/'.$matches['sval1']))
		{
			throw new XenForo_Exception(new XenForo_Phrase('file_does_not_exist').': '.$matches['sval1'], true);
		}
		
		$media = array(
			'media_value1' => $matches['sval1'],
			'media_value2' => '',
			'media_thumb' => 'styles/8wayrun/media_video.jpg',
			'media_title' => $matches['sval1'],
			'media_description' => $matches['sval1'],
			'media_duration' => 0,
			'media_keywords' => '',
		);
		
		return $media;
	}
}