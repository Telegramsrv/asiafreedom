<?php

class EWRmedio_Services_Hulu extends XenForo_Model
{
	public static function dumpMedia($service, $matches)
	{
		$client = new Zend_Http_Client('http://www.hulu.com/api/oembed.json');
		$client->setParameterGet(array(
			'url' => 'http://hulu.com/watch/'.$matches['sval1']
		));
		$feed = $client->request()->getBody();
		$json = json_decode($feed, true);
		
		if (empty($json['embed_url']))
		{
			throw new XenForo_Exception(new XenForo_Phrase('media_url_did_not_retrieve_valid_data'), true);
		}
		
		$media = array(
			'media_value1' => $matches['sval1'],
			'media_value2' => str_replace('http://www.hulu.com/embed.html?eid=', '', $json['embed_url']),
			'media_thumb' => $json['large_thumbnail_url'],
			'media_title' => $json['title'],
			'media_description' => $json['title'],
			'media_duration' => $json['duration'],
			'media_keywords' => '',
		);
		
		return $media;
	}
}