<?php

class EWRmedio_Services_Ustream extends XenForo_Model
{
	public static function dumpMedia($service, $matches)
	{
		$client = new Zend_Http_Client('http://api.ustream.tv/json/video/'.$matches['sval1'].'/getinfo');
		$feed = $client->request()->getBody();
		$json = json_decode($feed, true);
		
		if (!empty($json['error']))
		{
			throw new XenForo_Exception($json['msg'], true);
		}
		
		$json = $json['results'];
		
		$media = array(
			'media_value1' => $json['id'],
			'media_value2' => '',
			'media_thumb' => $json['imageUrl']['medium'],
			'media_title' => $json['title'],
			'media_description' => $json['description'],
			'media_duration' => $json['lengthInSecond'],
			'media_keywords' => implode(',', $json['tags']),
		);
		
		return $media;
	}
}