<?php

class AnyTV_ViewAdmin_Featured extends XenForo_ViewAdmin_Base
{
	public function renderJson()
	{
		if (!empty($this->_params['filterView']))
		{
			switch($this->_params['action']) {
				case 'users':
					$this->_templateName = 'anytv_user_list_items';
					break;
				case 'videos':
					$this->_templateName = 'anytv_video_list_items';
					break;
				case 'games':
					$this->_templateName = 'anytv_game_list_items';
					break;
			}
		}

		return null;
	}
}