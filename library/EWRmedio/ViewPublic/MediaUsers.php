<?php

class EWRmedio_ViewPublic_MediaUsers extends XenForo_ViewPublic_Base
{
	public function renderJson()
	{
		$output = $this->_renderer->getDefaultOutputArray(get_class($this), $this->_params, $this->_templateName);
		
		$output['_redirectMessage'] = new XenForo_Phrase('processing_your_user_submission');
		
		return XenForo_ViewRenderer_Json::jsonEncodeForOutput($output);
	}
}