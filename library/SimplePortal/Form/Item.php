<?php

class SimplePortal_Form_Item extends SimplePortal_Form{

	public $dataWriter = 'SimplePortal_DataWriter_PortalItem';

	function getFields()
	{
		return array(
			'content_type' => XenForo_Input::STRING,
			'content_id' => XenForo_Input::UINT,
			'display_order' => XenForo_Input::UINT,
			'attachment_id' => XenForo_Input::UINT,
			'category_id' => XenForo_Input::UINT,
			'portalItem_id'=> XenForo_Input::UINT
		);
	}


	function getConditions($input){
		if ($input['portalItem_id'] > 0){
			return array('portalItem_id' => $input['portalItem_id']);
		}
		return array(
			'content_type' => $input['content_type'],
			'content_id' => $input['content_id'],
		);
	}

}