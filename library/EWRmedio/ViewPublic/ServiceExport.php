<?php

class EWRmedio_ViewPublic_ServiceExport extends XenForo_ViewPublic_Base
{
	public function renderXml()
	{
		$this->setDownloadFileName($this->_params['service']['service_name'] . '.xml');
		return $this->_params['xml']->saveXml();
	}
}