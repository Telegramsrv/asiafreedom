<?php

class EWRmedio_Services_Vimeo extends XenForo_Model
{
	public static function dumpMedia($service, $matches)
	{
		$client = new Zend_Http_Client('http://vimeo.com/api/v2/video/'.$matches['sval1'].'.json');
		$feed = $client->request()->getBody();
		$json = json_decode($feed, true);
		
		if (empty($json[0]['id']))
		{
			throw new XenForo_Exception(new XenForo_Phrase('media_url_did_not_retrieve_valid_data'), true);
		}
		
		$json = $json[0];
		
		$media = array(
			'media_value1' => $json['id'],
			'media_value2' => '',
			'media_thumb' => $json['thumbnail_large'],
			'media_title' => $json['title'],
			'media_description' => str_replace('<br />', '', $json['description']),
			'media_duration' => $json['duration'],
			'media_keywords' => $json['tags'],
		);
		
		return $media;
	}
	
	public static function dumpPlaylist($service, $matches, $params)
	{
		$params['count'] = 20;
		$params['start'] = $params['start'] > 3 ? 3 : $params['start'];
	
		$client = new Zend_Http_Client('http://vimeo.com/api/v2/album/'.$matches['sval1'].'/videos.json?page='.$params['start']);
		$feed = $client->request()->getBody();
		$json = json_decode($feed, true);
		
		if (empty($json[0]['id']))
		{
			throw new XenForo_Exception(new XenForo_Phrase('media_url_did_not_retrieve_valid_data'), true);
		}
		
		$mediaModel = XenForo_Model::create('EWRmedio_Model_Media');
		$medias = array();
		
		foreach ($json AS $entry)
		{
			$medias[] = array(
				'media_value1' => $entry['id'],
				'media_value2' => '',
				'media_thumb' => $entry['thumbnail_large'],
				'media_title' => $entry['title'],
				'media_description' => str_replace('<br />', '', $entry['description']),
				'media_duration' => $entry['duration'],
				'media_keywords' => $entry['tags'],
			);
		}
		
		$client = new Zend_Http_Client('http://vimeo.com/api/v2/album/'.$matches['sval1'].'/info.json');
		$feed = $client->request()->getBody();
		$json = json_decode($feed, true);
		
		$params['total'] = $json['total_videos'];
		$params['cease'] = $params['total'] - ($params['start'] * $params['count']);
		
		return array($medias, $params);
	}
}