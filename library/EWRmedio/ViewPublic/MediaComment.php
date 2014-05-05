<?php

class EWRmedio_ViewPublic_MediaComment extends XenForo_ViewPublic_Base
{
	public function renderJson()
	{
		$output = $this->_renderer->getDefaultOutputArray(get_class($this), $this->_params, $this->_templateName);
		
		$output['_redirectMessage'] = new XenForo_Phrase('redirect_changes_saved_successfully');
		
		return XenForo_ViewRenderer_Json::jsonEncodeForOutput($output);
	}
}