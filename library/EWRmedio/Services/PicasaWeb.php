<?php

class EWRmedio_Services_PicasaWeb extends XenForo_Model
{
	public static function dumpMedia($service, $matches)
	{
		if (!empty($matches['album']))
		{
			$client = new Zend_Http_Client('https://picasaweb.google.com/'.$matches['sval1'].'/'.$matches['album']);
			$feed = $client->request()->getBody();
			
			if (!preg_match('#albumid/(\d+)"#i', $feed, $match))
			{
				throw new XenForo_Exception(new XenForo_Phrase('media_url_did_not_retrieve_valid_data'), true);
			}
			
			$matches['sval2'] = $match[1];
		}
		
		$client = new Zend_Http_Client('https://picasaweb.google.com/data/feed/api/user/'.$matches['sval1'].'/albumid/'.$matches['sval2']);
		$feed = $client->request()->getBody();
		
		$model = XenForo_Model::create('EWRmedio_Model_Xml2Array');
		$mrss = $model->xml2array($feed);
		
		if (empty($mrss['feed']['icon']))
		{
			throw new XenForo_Exception(new XenForo_Phrase('media_url_did_not_retrieve_valid_data'), true);
		}
		
		$mrss = $mrss['feed'];
		
		$media = array(
			'media_value1' => $matches['sval1'],
			'media_value2' => $matches['sval2'],
			'media_thumb' => $mrss['icon'],
			'media_title' => $mrss['title'],
			'media_description' => $mrss['title'],
			'media_duration' => $mrss['openSearch:totalResults'],
			'media_keywords' => '',
		);
		
		return $media;
	}
}