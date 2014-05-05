<?php

class XenForo_ViewPublic_Member_View extends XenForo_ViewPublic_Base
{
	public function renderHtml()
	{
		$bbCodeParser = XenForo_BbCode_Parser::create(XenForo_BbCode_Formatter_Base::create('Base', array('view' => $this)));

		$this->_params['user']['aboutHtml'] = new XenForo_BbCode_TextWrapper($this->_params['user']['about'], $bbCodeParser);
		$this->_params['user']['signatureHtml'] = new XenForo_BbCode_TextWrapper($this->_params['user']['signature'], $bbCodeParser, array('lightBox' => false));

		foreach ($this->_params['customFieldsGrouped'] AS &$fields)
		{
			$fields = XenForo_ViewPublic_Helper_User::addUserFieldsValueHtml($this, $fields);
		}

		$optionModel =  XenForo_Model::create('XenForo_Model_Option');
		$fetchOptions = array('join' => XenForo_Model_Option::FETCH_ADDON);
		$options = $optionModel->getOptionsInGroup("anytv_categories", $fetchOptions);

		$games = unserialize($options['anytv_categories_categories']['option_value']);
		$this->_params['games'] = array();

		for($i=0; $i<count($games['game_id']); $i++) {
			$this->_params['games'][$games['game_id'][$i]]['image'] = $games['game_image'][$i];
			$this->_params['games'][$games['game_id'][$i]]['name'] = $games['game_name'][$i];
		}
	}
}