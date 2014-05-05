<?php

class EWRmedio_Services_YouTube extends XenForo_Model
{
	public static function dumpMedia($service, $matches)
	{
		$client = new Zend_Http_Client('http://gdata.youtube.com/feeds/api/videos/'.$matches['sval1'].'?v=2&alt=json');
		$feed = $client->request()->getBody();
		$json = json_decode($feed, true);
		
		if (empty($json))
		{
			throw new XenForo_Exception(new XenForo_Phrase('media_url_did_not_retrieve_valid_data'), true);
		}
		
		$e = !empty($json['entry']['app$control']['yt$state']) ? $json['entry']['app$control']['yt$state'] : null;
		$error = !empty($e) && $e['name'] != 'restricted' && $e['name'] != 'processing' ? $e['$t'] : null;
		if ($error) { throw new XenForo_Exception($error, true); }
		
		$json = $json['entry']['media$group'];
		
		$media = array(
			'media_value1' => $matches['sval1'],
			'media_value2' => '',
			'media_thumb' => $json['media$thumbnail']['2']['url'],
			'media_title' => $json['media$title']['$t'],
			'media_description' => $json['media$description']['$t'],
			'media_duration' => $json['yt$duration']['seconds'],
			'media_keywords' => !empty($json['media$keywords']) ? $json['media$keywords']['$t'] : null,
		);
		
		if ($json['media$thumbnail']['3']['yt$name'] == 'sddefault')
		{
			$media['media_thumb'] = $json['media$thumbnail']['3']['url'];
		}
		
		return $media;
	}
	
	public static function dumpPlaylist($service, $matches, $params)
	{
		$params['index'] = (($params['start'] - 1) * $params['count'])+1;
	
		$client = new Zend_Http_Client('http://gdata.youtube.com/feeds/api/playlists/'.$matches['sval1'].'?alt=json&start-index='.$params['index'].'&max-results='.$params['count']);
		$feed = $client->request()->getBody();
		$json = json_decode($feed, true);
		
		if (empty($json) || empty($json['feed']['entry']))
		{
			throw new XenForo_Exception(new XenForo_Phrase('media_url_did_not_retrieve_valid_data'), true);
		}
		
		$e = !empty($json['entry']['app$control']['yt$state']) ? $json['entry']['app$control']['yt$state'] : null;
		$error = !empty($e) && $e['name'] != 'restricted' && $e['name'] != 'processing' ? $e['$t'] : null;
		if ($error) { throw new XenForo_Exception($error, true); }
		
		$mediaModel = XenForo_Model::create('EWRmedio_Model_Media');
		$medias = array();
		
		foreach ($json['feed']['entry'] AS $entry)
		{
			$id = explode('/', $entry['link'][3]['href']);
			$serviceVAL = end($id);
			
			$entry = $entry['media$group'];
			
			$medias[] = array(
				'media_value1' => $serviceVAL,
				'media_value2' => '',
				'media_thumb' => $entry['media$thumbnail']['0']['url'],
				'media_title' => $entry['media$title']['$t'],
				'media_description' => $entry['media$description']['$t'],
				'media_duration' => $entry['yt$duration']['seconds'],
				'media_keywords' => !empty($entry['media$keywords']) ? $entry['media$keywords']['$t'] : null,
				'exists' => $mediaModel->getMediaByServiceInfo($service['service_id'], $serviceVAL, ''),
			);
		}
		
		$params['total'] = $json['feed']['openSearch$totalResults']['$t'];
		$params['cease'] = $params['total'] - $params['index']+1 - $params['count'];
		
		return array($medias, $params);
	}
}