<?php

class EWRmedio_Services_Veevr extends XenForo_Model
{
	public static function dumpMedia($service, $matches)
	{
		$client = new Zend_Http_Client('http://veevr.com/videos/'.$matches['sval1']);
		$feed = $client->request()->getBody();
		$meta = array();
		
		preg_match_all('#<meta\s+(name|property)="([^"]+)"\s+content="([^"]*)"#i',$feed,$props);
		foreach ($props[2] as $key => $value) { $meta[$value] = $props[3][$key]; }
		
		if (empty($meta['og:video']))
		{
			throw new XenForo_Exception(new XenForo_Phrase('media_url_did_not_retrieve_valid_data'), true);
		}
		
		preg_match('#\s+duration:\s(\d+),#i',$feed,$duration);
		
		$media = array(
			'media_value1' => $matches['sval1'],
			'media_value2' => '',
			'media_thumb' => $meta['og:image'],
			'media_title' => $meta['og:title'],
			'media_description' => $meta['og:description'],
			'media_duration' => $duration[1],
			'media_keywords' => $meta['keywords'],
		);
		
		return $media;
	}
}