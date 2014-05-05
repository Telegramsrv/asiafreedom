<?php

class EWRmedio_Services_Adobe extends XenForo_Model
{
	public static function dumpMedia($service, $matches)
	{
		$client = new Zend_Http_Client('http://tv.adobe.com/watch/'.$matches['sval1']);
		$feed = $client->request()->getBody();
		$meta = array();
		
		preg_match_all('#<meta\s+(name|property)="([^"]+)"\s+(content|value)="([^"]*)"#i', $feed, $props);
		foreach ($props[2] as $key => $value) { $meta[$value] = $props[4][$key]; }
		
		if (empty($meta['og:video']))
		{
			throw new XenForo_Exception(new XenForo_Phrase('media_url_did_not_retrieve_valid_data'), true);
		}
		
		$time = explode(':', $meta['duration']);
		preg_match_all('#/search/\?q=(%20)?([\w%]+)"#i', $feed, $keywords);
		
		$media = array(
			'media_value1' => $matches['sval1'],
			'media_value2' => str_replace('https://tv.adobe.com/embed/', '', $meta['twitter:player']),
			'media_thumb' => $meta['og:image'],
			'media_title' => $meta['og:title'],
			'media_description' => $meta['og:description'],
			'media_duration' => ($time[0] * 60 * 60) + ($time[1] * 60) + $time[2],
			'media_keywords' => urldecode(implode(', ',$keywords[2])),
		);
		
		return $media;
	}
}