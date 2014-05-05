<?php

class EWRmedio_Services_Veoh extends XenForo_Model
{
	public static function dumpMedia($service, $matches)
	{
		$client = new Zend_Http_Client('http://www.veoh.com/rest/videos/'.$matches['sval1'].'/details');
		$feed = $client->request()->getBody();
		
		$model = XenForo_Model::create('EWRmedio_Model_Xml2Array');
		$mrss = $model->xml2array($feed);
		
		if (empty($mrss['videos']['video_attr']['permalinkId']))
		{
			throw new XenForo_Exception(new XenForo_Phrase('media_url_did_not_retrieve_valid_data'), true);
		}
		
		$mrss = $mrss['videos']['video_attr'];
		
		preg_match('#(\d+)([^\d]+)(\d*?)([^\d]+)(\d*)#i', $mrss['length'], $t);

		if (strpos($t[2],'h'))
		{
			$a = $t[1]*60*60;
		}
		else if (strpos($t[2],'m'))
		{
			$a=$t[1]*60;
		}
		else
		{
			$a=$t[1];
		}

		if (strpos($t[4],'m'))
		{
			$b=$t[3]*60+$t[5];
		}
		else
		{
			$b=$t[3];
		}
		
		$media = array(
			'media_value1' => $mrss['permalinkId'],
			'media_value2' => '',
			'media_thumb' => $mrss['fullHighResImagePath'],
			'media_title' => $mrss['title'],
			'media_description' => $mrss['description'],
			'media_duration' => $a+$b,
			'media_keywords' => $mrss['tagsCommaSeparated'],
		);
		
		return $media;
	}
}