<?php

/**
 * View for the admin image upload page.
 */
class Waindigo_AdminImages_ViewAdmin_AdminImage_Upload extends XenForo_ViewAdmin_Base
{
	public function renderHtml()
	{
		$this->_params['typeChoices'] = array();

		if (!empty($this->_params['typeHandlers']))
		{
			foreach ($this->_params['typeHandlers'] AS $contentType => $handler)
			{
				$selectedContentId = (isset($this->_params['typeId'][$contentType]) ? $this->_params['typeId'][$contentType] : 0);
				$this->_params['typeChoices'][] = $handler->getUploadAdminImageOption($this, $selectedContentId, $contentType);
			}
		}
	} /* END renderHtml */
}