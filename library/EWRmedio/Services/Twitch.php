<?php

class EWRmedio_Services_Twitch extends XenForo_Model
{
	public static function dumpMedia($service, $matches)
	{
		$client = new Zend_Http_Client('https://api.twitch.tv/kraken/videos/a'.$matches['sval1']);
		$feed = $client->request()->getBody();
		$json = json_decode($feed, true);
		
		if (!empty($json['status']))
		{
			throw new XenForo_Exception($json['message'], true);
		}
		
		$media = array(
			'media_value1' => $matches['sval1'],
			'media_value2' => '',
			'media_thumb' => $json['preview'],
			'media_title' => $json['title'],
			'media_description' => $json['description'],
			'media_duration' => $json['length'],
			'media_keywords' => '',
		);
		
		return $media;
	}
}