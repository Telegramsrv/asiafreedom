<?php

class EWRmedio_Services_FlickR extends XenForo_Model
{
	public static function dumpMedia($service, $matches)
	{
		$client = new Zend_Http_Client('http://api.flickr.com/services/rest/');
		$client->setParameterGet(array(
			'method' => 'flickr.photosets.getInfo',
			'api_key' => 'a3531b01a8acada4a370860107b15a5f',
			'photoset_id' => $matches['sval2'],
			'format' => 'json',
			'nojsoncallback' => '1',
		));
		$feed = $client->request()->getBody();
		$json = json_decode($feed, true);
		
		$error = !empty($json['stat']) && $json['stat'] == 'fail' ? $json['message'] : null;
		if ($error) { throw new XenForo_Exception($error, true); }
		
		$json = $json['photoset'];
		
		$media = array(
			'media_value1' => $json['username'],
			'media_value2' => $json['id'],
			'media_thumb' => 'http://farm'.$json['farm'].'.staticflickr.com/'.$json['server'].'/'.$json['primary'].'_'.$json['secret'].'_z.jpg',
			'media_title' => $json['title']['_content'],
			'media_description' => $json['description']['_content'],
			'media_duration' => $json['photos'],
			'media_keywords' => '',
		);
		
		return $media;
	}
}