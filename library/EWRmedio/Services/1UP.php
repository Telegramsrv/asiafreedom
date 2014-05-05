<?php

class EWRmedio_Services_1UP extends XenForo_Model
{
	public static function dumpMedia($service, $matches)
	{
		$client = new Zend_Http_Client('http://gamevideos.1up.com/video/id/'.$matches['sval1']);
		$feed = $client->request()->getBody();
		$meta = array();
		
		preg_match('#<title>(.*)</title>#i', $feed, $props);
		$head['title'] = $props[1];
					
		preg_match_all('#<meta\s+(name|property)="([^"]+)"\s+(content|value)="([^"]*)"#i', $feed, $props);
		foreach ($props[2] as $key => $value) { $meta[$value] = $props[4][$key]; }
		
		preg_match_all('#<link\s+rel="([^"]+)"\s+(href|src)="([^"]+)"#i', $feed, $props);
		foreach ($props[1] as $key => $value) { $link[$value] = $props[3][$key]; }
		
		if (empty($link['image_src']))
		{
			throw new XenForo_Exception(new XenForo_Phrase('media_url_did_not_retrieve_valid_data'), true);
		}
		
		preg_match('#object id="vid_(\w+)#i', $feed, $value2);
		
		$media = array(
			'media_value1' => $matches['sval1'],
			'media_value2' => $value2[1],
			'media_thumb' => $link['image_src'],
			'media_title' => $head['title'],
			'media_description' => $meta['description'],
			'media_duration' => 0,
			'media_keywords' => $meta['keywords'],
		);
		
		return $media;
	}
}