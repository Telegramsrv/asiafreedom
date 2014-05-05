<?php

class EWRmedio_Services_MetaCafe extends XenForo_Model
{
	public static function dumpMedia($service, $matches)
	{
		$client = new Zend_Http_Client('http://www.metacafe.com/api/item/'.$matches['sval1']);
		$feed = $client->request()->getBody();
		
		$model = XenForo_Model::create('EWRmedio_Model_Xml2Array');
		$mrss = $model->xml2array($feed);
		
		if (empty($mrss['rss']['channel']['item']['id']))
		{
			throw new XenForo_Exception(new XenForo_Phrase('media_url_did_not_retrieve_valid_data'), true);
		}
		
		$mrss = $mrss['rss']['channel']['item'];
		
		$media = array(
			'media_value1' => $mrss['id'],
			'media_value2' => '',
			'media_thumb' => $mrss['media:thumbnail_attr']['url'],
			'media_title' => $mrss['media:title'],
			'media_description' => $mrss['media:description'],
			'media_duration' => $mrss['media:content_attr']['duration'],
			'media_keywords' => $mrss['media:keywords'],
		);
		
		return $media;
	}
}