<?php

class EWRmedio_ViewPublic_KeywordFind extends XenForo_ViewPublic_Base
{
	public function renderJson()
	{
		$results = array();
		foreach ($this->_params['keywords'] AS $keyword)
		{
			$results[$keyword['keyword_text']] = array(
				'avatar' => EWRmedio_Template_Helper::getKeywordIcon($keyword),
				'username' => htmlspecialchars($keyword['keyword_text'])
			);
		}

		return array(
			'results' => $results
		);
	}
}