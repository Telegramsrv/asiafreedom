<?php

class EWRmedio_ViewPublic_AccountLikes extends XenForo_ViewPublic_Base
{
	public function renderHtml()
	{
		foreach ($this->_params['likes'] AS &$item)
		{
			$item['listTemplate'] = $this->createTemplateObject($item['listTemplateName'], array(
				'item' => $item,
				'user' => array(
					'user_id' => $item['user_id'],
					'username' => $item['username'],
				),
				'content' => $item['content']
			));
		}
	}
}