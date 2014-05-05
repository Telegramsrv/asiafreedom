<?php

class EWRmedio_Services_AudioXML extends XenForo_Model
{
	public static function dumpMedia($service, $matches)
	{
		$file = XenForo_Application::get('options')->boardUrl.'/'.XenForo_Application::$externalDataPath.'/local/'.$matches['sval1'];

		if (!file_exists(XenForo_Application::$externalDataPath.'/local/'.$matches['sval1']))
		{
			throw new XenForo_Exception(new XenForo_Phrase('file_does_not_exist').': '.$matches['sval1'], true);
		}
		
		$client = new Zend_Http_Client($file);
		$feed = $client->request()->getBody();
		
		$model = XenForo_Model::create('EWRmedio_Model_Xml2Array');
		$mrss = $model->xml2array($feed);
		
		$mrss = $mrss = $mrss['rss']['channel'];
		$length = 0;
		
		foreach ($mrss['item'] AS $item)
		{
			$length += !empty($item['media:content_attr']['duration']) ? $item['media:content_attr']['duration'] : 0;
		}
		
		$media = array(
			'media_value1' => $matches['sval1'],
			'media_value2' => '',
			'media_thumb' => !empty($mrss['image']) ? $mrss['image'] : $mrss['item'][0]['media:thumbnail_attr']['url'],
			'media_title' => $mrss['title'],
			'media_description' => $mrss['title'],
			'media_duration' => $length,
			'media_keywords' => '',
		);
		
		return $media;
	}
}