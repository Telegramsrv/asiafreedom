<?php

class EWRmedio_Services_Soundcloud extends XenForo_Model
{
	public static function dumpMedia($service, $matches)
	{
		$client = new Zend_Http_Client('https://soundcloud.com/'.$matches['sval1']);
		$feed = $client->request()->getBody();
		$meta = array();
		
		preg_match_all('#<meta\s+content="([^"]*)"\s+(name|property)="([^"]+)"#i',$feed,$props);
		foreach ($props[3] as $key => $value) { $meta[$value] = $props[1][$key]; }
		
		if (empty($meta['og:video']))
		{
			throw new XenForo_Exception(new XenForo_Phrase('media_url_did_not_retrieve_valid_data'), true);
		}
		
		preg_match('#tracks%2F([\d]+)#i', $meta['og:video'], $match);
		
		$media = array(
			'media_value1' => $matches['sval1'],
			'media_value2' => $match[1],
			'media_thumb' => $meta['og:image'],
			'media_title' => $meta['og:title'],
			'media_description' => $meta['description'],
			'media_duration' => 0,
			'media_keywords' => $meta['keywords'],
		);
		
		return $media;
	}
}