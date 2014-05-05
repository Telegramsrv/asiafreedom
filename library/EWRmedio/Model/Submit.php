<?php

class EWRmedio_Model_Submit extends XenForo_Model
{
	public function fetchBulkInfo($source, $params)
	{
		$options = XenForo_Application::get('options');
		$source = substr($source, 0, 7) == 'http://' ? $source : 'http://'.$source;
		$services = $this->getModelFromCache('EWRmedio_Model_Services')->getServices();

		foreach ($services AS $service)
		{
			if (empty($service['service_playlist'])) { continue; }
			$regexes = explode("\n", $service['service_playlist']);

			foreach ($regexes AS $regex)
			{
				if (preg_match('#'.$regex.'#i', $source, $matches))
				{
					$found = true; break 2;
				}
			}
		}

		if (empty($found))
		{
			throw new XenForo_Exception(new XenForo_Phrase('media_url_did_not_match_services'), true);
		}
		
		list($medias, $params) = $this->getModelFromCache($service['service_callback'])->dumpPlaylist($service, $matches, $params);
		
		foreach ($medias AS &$media)
		{
			if (!$options->EWRmedio_retrievekeywords)
			{
				$media['media_keywords'] = '';
			}
		
			$service['service_value'] = $media['media_value1'];
			$service['service_value2'] = $media['media_value2'];
			$service = $this->getModelFromCache('EWRmedio_Model_Parser')->parseReplace($service);
			
			$media += array(
				'service_id' => $service['service_id'],
				'service_media' => $service['service_media'],
				'service_value' => $service['service_value'],
				'service_value2' => $service['service_value2'],
				'service_url' => $service['service_url'],
				'service_width' => $service['service_width'],
				'service_height' => $service['service_height'],
				'service_embed' => $service['service_embed'],
			);

			$media = $this->getModelFromCache('EWRmedio_Model_Media')->getDuration($media);
		}
		
		return array($medias, $params);
	}

	public function fetchFeedInfo($source)
	{
		$options = XenForo_Application::get('options');
		$source = substr($source, 0, 7) == 'http://' ? $source : 'http://'.$source;
		$services = $this->getModelFromCache('EWRmedio_Model_Services')->getServices();

		foreach ($services AS $service)
		{
			$regexes = explode("\n", $service['service_regex']);

			foreach ($regexes AS $regex)
			{
				if (preg_match('#'.$regex.'#i', $source, $matches))
				{
					$found = true; break 2;
				}
			}
		}

		if (empty($found))
		{
			throw new XenForo_Exception(new XenForo_Phrase('media_url_did_not_match_services'), true);
		}
		
		$media = $this->getModelFromCache($service['service_callback'])->dumpMedia($service, $matches);
		
		if (!$options->EWRmedio_retrievekeywords)
		{
			$media['media_keywords'] = '';
		}
	
		$service['service_value'] = $media['media_value1'];
		$service['service_value2'] = $media['media_value2'];
		$service = $this->getModelFromCache('EWRmedio_Model_Parser')->parseReplace($service);
		
		$media += array(
			'service_id' => $service['service_id'],
			'service_media' => $service['service_media'],
			'service_value' => $service['service_value'],
			'service_value2' => $service['service_value2'],
			'service_url' => $service['service_url'],
			'service_width' => $service['service_width'],
			'service_height' => $service['service_height'],
			'service_embed' => $service['service_embed'],
		);

		$media = $this->getModelFromCache('EWRmedio_Model_Media')->getDuration($media);

		return $media;
	}
}