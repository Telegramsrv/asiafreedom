<?php

class EWRmedio_Services_DailyMotion extends XenForo_Model
{
	public static function dumpMedia($service, $matches)
	{
		$client = new Zend_Http_Client('https://api.dailymotion.com/video/'.$matches['sval1'].'?fields=description,duration,id,tags,thumbnail_url,title');
		$feed = $client->request()->getBody();
		$json = json_decode($feed, true);
		
		if (!empty($json['error']))
		{
			throw new XenForo_Exception($json['error']['message'], true);
		}
		
		if (empty($json['duration']))
		{
			throw new XenForo_Exception($json['title'], true);
		}
		
		$media = array(
			'media_value1' => $json['id'],
			'media_value2' => '',
			'media_thumb' => $json['thumbnail_url'],
			'media_title' => $json['title'],
			'media_description' => $json['description'],
			'media_duration' => $json['duration'],
			'media_keywords' => implode(',', $json['tags']),
		);
		
		return $media;
	}
	
	public static function dumpPlaylist($service, $matches, $params)
	{
		$client = new Zend_Http_Client('https://api.dailymotion.com/playlist/'.$matches['sval1'].
			'/videos?fields=description,duration,id,tags,thumbnail_url,title&page='.$params['start'].'&limit='.$params['count']);
		$feed = $client->request()->getBody();
		$json = json_decode($feed, true);
		
		if (!empty($json['error']))
		{
			throw new XenForo_Exception($json['error']['message'], true);
		}
		
		$mediaModel = XenForo_Model::create('EWRmedio_Model_Media');
		$medias = array();
		
		foreach ($json['list'] AS $entry)
		{
			$medias[] = array(
				'media_value1' => $entry['id'],
				'media_value2' => '',
				'media_thumb' => $entry['thumbnail_url'],
				'media_title' => $entry['title'],
				'media_description' => $entry['description'],
				'media_duration' => $entry['duration'],
				'media_keywords' => implode(',', $entry['tags']),
				'exists' => $mediaModel->getMediaByServiceInfo($service['service_id'], $entry['id'], ''),
			);
		}
		
		$client = new Zend_Http_Client('https://api.dailymotion.com/playlist/'.$matches['sval1'].'?fields=videos_total');
		$feed = $client->request()->getBody();
		$json = json_decode($feed, true);
		
		$params['total'] = $json['videos_total'];
		$params['cease'] = $params['total'] - ($params['start'] * $params['count']);
		
		return array($medias, $params);
	}
}