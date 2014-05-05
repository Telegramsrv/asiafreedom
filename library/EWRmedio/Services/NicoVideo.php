<?php

class EWRmedio_Services_NicoVideo extends XenForo_Model
{
	public static function dumpMedia($service, $matches)
	{
		$client = new Zend_Http_Client('http://ext.nicovideo.jp/api/getthumbinfo/'.$matches['sval1']);
		$feed = $client->request()->getBody();
		
		$model = XenForo_Model::create('EWRmedio_Model_Xml2Array');
		$mrss = $model->xml2array($feed);
		
		if (empty($mrss['nicovideo_thumb_response']['thumb']['video_id']))
		{
			throw new XenForo_Exception(new XenForo_Phrase('media_url_did_not_retrieve_valid_data'), true);
		}
		
		$mrss = $mrss['nicovideo_thumb_response']['thumb'];
		$length = explode(':', $mrss['length']);
		$keywords = !empty($mrss['tags'][0]['tag']) ? $mrss['tags'][0]['tag'] : $mrss['tags']['tag'];
		
		foreach ($keywords AS $key => $word)
		{
			if (is_array($word)) { unset($keywords[$key]); }
		}
		
		$media = array(
			'media_value1' => $mrss['video_id'],
			'media_value2' => '',
			'media_thumb' => $mrss['thumbnail_url'],
			'media_title' => $mrss['title'],
			'media_description' => $mrss['description'],
			'media_duration' => ($length[0] * 60) + $length[1],
			'media_keywords' => implode(',', $keywords),
		);
		
		return $media;
	}
}