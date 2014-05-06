<?php

class AnyTV_ViewAdmin_Featured extends XenForo_ViewAdmin_Base
{
	public function renderJson()
	{
		if (!empty($this->_params['filterView']))
		{
			$this->_templateName = 'anytv_user_list_items';
		}

		return null;
	}
}