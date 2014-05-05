<?php

class EWRmedio_Services_Blip extends XenForo_Model
{
	public static function dumpMedia($service, $matches)
	{
		$client = new Zend_Http_Client('http://blip.tv/rss/flash/'.$matches['sval1']);
		$feed = $client->request()->getBody();
		
		$model = XenForo_Model::create('EWRmedio_Model_Xml2Array');
		$mrss = $model->xml2array($feed);
		
		if (empty($mrss['rss']['channel']['item']['media:thumbnail_attr']))
		{
			throw new XenForo_Exception($mrss['rss']['channel']['item']['description'], true);
		}
		
		$mrss = $mrss['rss']['channel']['item'];
		
		$media = array(
			'media_value1' => $matches['sval1'],
			'media_value2' => $mrss['blip:embedLookup'],
			'media_thumb' => $mrss['media:thumbnail_attr']['url'],
			'media_title' => $mrss['media:title'],
			'media_description' => $mrss['blip:puredescription'],
			'media_duration' => $mrss['blip:runtime'],
			'media_keywords' => $mrss['media:keywords'],
		);
		
		return $media;
	}
}