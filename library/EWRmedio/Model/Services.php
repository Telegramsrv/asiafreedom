<?php

class EWRmedio_Model_Services extends XenForo_Model
{
	public function getServices()
	{
		$services = $this->fetchAllKeyed("
			SELECT *
				FROM EWRmedio_services
			ORDER BY service_name
		", 'service_name');
		
		$odd = 1;
		
		foreach ($services AS &$service)
		{
			$service['primary'] = $odd ? 1 : 0;
			$odd = $odd ? 0 : 1;
		}

        return $services;
	}

	public function getServiceByID($srvID)
	{
		if (!$service = $this->_getDb()->fetchRow("
			SELECT *
				FROM EWRmedio_services
			WHERE service_id = ?
		", $srvID))
		{
			return false;
		}

        return $service;
	}

	public function getServiceByName($name)
	{
		if (!$service = $this->_getDb()->fetchRow("
			SELECT *
				FROM EWRmedio_services
			WHERE service_name = ?
		", $name))
		{
			return false;
		}

        return $service;
	}

	public function updateService($input)
	{
		$dw = XenForo_DataWriter::create('EWRmedio_DataWriter_Services');

		if (!empty($input['service_id']) && $service = $this->getServiceByID($input['service_id']))
		{
			$dw->setExistingData($service);
		}
		
		$dw->bulkSet($input);
		$dw->save();
		$input['service_id'] = $dw->get('service_id');

		return $input;
	}
	
	public function deleteService($input)
	{
		$listParams = array(
			'type' => 'service',
			'where' => $input['service_id']
		);
				
		if ($count = $this->getModelFromCache('EWRmedio_Model_Lists')->getMediaCount($listParams))
		{
			throw new XenForo_Exception(new XenForo_Phrase('unable_to_delete_service', array('count' => $count)), true);
		}
			
		$dw = XenForo_DataWriter::create('EWRmedio_DataWriter_Services');
		$dw->setExistingData($input);
		$dw->delete();
		
		return true;
	}
	
	public function exportService($service)
	{
		$document = new DOMDocument('1.0', 'utf-8');
		$document->formatOutput = true;

		$srv_node = $document->createElement('service');
		$document->appendChild($srv_node);

		$srv_node->appendChild($document->createElement('service_name', $service['service_name']));
		$srv_node->appendChild($document->createElement('service_media', $service['service_media']));

		$sub_node = $document->createElement('service_regex', '');
		$sub_node->appendChild($document->createCDATASection($service['service_regex']));
		$srv_node->appendChild($sub_node);

		$sub_node = $document->createElement('service_playlist', '');
		$sub_node->appendChild($document->createCDATASection($service['service_playlist']));
		$srv_node->appendChild($sub_node);

		$sub_node = $document->createElement('service_url', '');
		$sub_node->appendChild($document->createCDATASection($service['service_url']));
		$srv_node->appendChild($sub_node);
		
		$srv_node->appendChild($document->createElement('service_callback', $service['service_callback']));
		$srv_node->appendChild($document->createElement('service_width', $service['service_width']));
		$srv_node->appendChild($document->createElement('service_height', $service['service_height']));
		
		$sub_node = $document->createElement('service_embed', '');
		$sub_node->appendChild($document->createCDATASection($service['service_embed']));
		$srv_node->appendChild($sub_node);
		
		$srv_node->appendChild($document->createElement('service_local', $service['service_local']));

		return $document;
	}

	public function importService($fileName)
	{
		if (!file_exists($fileName) || !is_readable($fileName))
		{
			throw new XenForo_Exception(new XenForo_Phrase('please_enter_valid_file_name_requested_file_not_read'), true);
		}

		$file = new SimpleXMLElement($fileName, null, true);

		if ($file->getName() != 'service')
		{
			throw new XenForo_Exception(new XenForo_Phrase('provided_file_is_not_a_service_xml_file'), true);
		}

		$dw = XenForo_DataWriter::create('EWRmedio_DataWriter_Services');

		if ($service = $this->getServiceByName($file->service_name))
		{
			$dw->setExistingData($service);
		}
		$dw->bulkSet(array(
			'service_name' => $file->service_name,
			'service_media' => $file->service_media,
			'service_regex' => $file->service_regex,
			'service_playlist' => $file->service_playlist,
			'service_url' => $file->service_url,
			'service_callback' => $file->service_callback,
			'service_width' => $file->service_width,
			'service_height' => $file->service_height,
			'service_embed' => $file->service_embed,
			'service_local' => $file->service_local,
		));
		$dw->save();

		return true;
	}

	public function rebuildServices($rebuild = true)
	{
		$services = $this->getServices();
		$files = scandir($xmlDir = XenForo_Application::getInstance()->getRootDir().'/library/EWRmedio/Services', 1);

		foreach ($files AS $file)
		{
			if (!$rebuild)
			{
				$service = str_ireplace('.xml', '', $file);
				if (empty($services[$service])) { continue; }
			}
		
			if (stristr($file,'.xml') && $file != '_temp.xml')
			{
				XenForo_Model::create('EWRmedio_Model_Services')->importService($xmlDir.'/'.$file);
			}
		}

		return true;
	}
}