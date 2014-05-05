<?php

/**
 * @deprecated
 *
 * Class SimplePortal_Form_Category
 */
class SimplePortal_Form_Category extends SimplePortal_Form
{

	public $dataWriter = 'SimplePortal_DataWriter_Category';

	function getFields()
	{
		return array(
			'category_id' => XenForo_Input::UINT,
			'title' => XenForo_Input::STRING,
			'display_order' => XenForo_Input::UINT
		);
	}

	function getConditions($input)
	{
		return array('category_id' => $input['category_id']);
	}


}