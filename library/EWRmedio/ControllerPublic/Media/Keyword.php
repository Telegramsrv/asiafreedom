<?php

class EWRmedio_ControllerPublic_Media_Keyword extends XenForo_ControllerPublic_Abstract
{
	public $perms;
	
	public function actionKeywordFind()
	{
		$q = ltrim($this->_input->filterSingle('q', XenForo_Input::STRING, array('noTrim' => true)));

		if ($q !== '' && utf8_strlen($q) >= 2)
		{
			$keywords = $this->getModelFromCache('EWRmedio_Model_Keywords')->findKeywords($q, 10);
		}
		else
		{
			$keywords = array();
		}

		$viewParams = array('keywords' => $keywords);

		return $this->responseView('EWRmedio_ViewPublic_KeywordFind', '', $viewParams);
	}

	public function actionKeyword()
	{
		$keywordSlug = $this->_input->filterSingle('string_id', XenForo_Input::STRING);
		
		return $this->responseRedirect(
			XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT,
			XenForo_Link::buildPublicLink('media/medias', '', array('filter' => $keywordSlug))
		);
	}

	public function actionKeywordCreate()
	{
		if (!$this->perms['admin']) { return $this->responseNoPermission(); }
		
		$this->_assertPostOnly();
		$this->getModelFromCache('EWRmedio_Model_Keywords')->updateKeywords($this->_input->filterSingle('media_keywords', XenForo_Input::STRING), true);

		return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media/admin/keywords'));
	}

	public static function getSessionActivityDetailsForList(array $activities)
	{
		return new XenForo_Phrase('viewing_media_library');
	}

	protected function _preDispatch($action)
	{
		parent::_preDispatch($action);

		$this->perms = $this->getModelFromCache('EWRmedio_Model_Perms')->getPermissions();

		if (!$this->perms['browse']) { throw $this->getNoPermissionResponseException(); }
	}
}