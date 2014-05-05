<?php

class EWRmedio_Services_Justin extends XenForo_Model
{
	public static function dumpMedia($service, $matches)
	{
		$client = new Zend_Http_Client('http://api.justin.tv/api/clip/show/'.$matches['sval1'].'.json');
		$feed = $client->request()->getBody();
		$json = json_decode($feed, true);
		
		if (!empty($json['error']))
		{
			throw new XenForo_Exception($json['error'], true);
		}
		
		$json = $json[0];
		
		$media = array(
			'media_value1' => $json['id'],
			'media_value2' => '',
			'media_thumb' => $json['image_url_large'],
			'media_title' => $json['title'],
			'media_description' => $json['description'],
			'media_duration' => $json['length'],
			'media_keywords' => str_replace(' ', ',', $json['tags']),
		);
		
		return $media;
	}
}