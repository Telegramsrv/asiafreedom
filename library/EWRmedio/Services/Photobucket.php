<?php

class EWRmedio_Services_Photobucket extends XenForo_Model
{
	public static function dumpMedia($service, $matches)
	{
		$client = new Zend_Http_Client('http://feed.photobucket.com/albums/f83/'.$matches['sval1'].'/'.$matches['sval2'].'/feed.rss');
		$feed = $client->request()->getBody();
		
		$model = XenForo_Model::create('EWRmedio_Model_Xml2Array');
		$mrss = $model->xml2array($feed);
		
		if (empty($mrss['rss']['channel']['item'][0]['media:content']['media:thumbnail_attr']['url']))
		{
			throw new XenForo_Exception(new XenForo_Phrase('media_url_did_not_retrieve_valid_data'), true);
		}
		
		$mrss = $mrss['rss']['channel'];
		
		$media = array(
			'media_value1' => $matches['sval1'],
			'media_value2' => $matches['sval2'],
			'media_thumb' => $mrss['item'][0]['media:content']['media:thumbnail_attr']['url'],
			'media_title' => $mrss['title'],
			'media_description' => $mrss['description'],
			'media_duration' => count($mrss['item']),
			'media_keywords' => '',
		);
		
		return $media;
	}
}