<?php

class EWRmedio_ControllerPublic_Media_Admin extends XenForo_ControllerPublic_Abstract
{
	public $perms;

	public function actionAdmin()
	{
		return $this->responseReroute(__CLASS__, 'admin/categories');
	}

	public function actionAdminCategories()
	{
		if ($this->_request->isPost())
		{
			$input = $this->_input->filter(array(
				'category_order' => XenForo_Input::ARRAY_SIMPLE,
				'submit' => XenForo_Input::STRING,
			));

			$this->getModelFromCache('EWRmedio_Model_Categories')->updateCategories($input);
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media/admin/categories'));
		}

		$viewParams = array(
			'catList' => $this->getModelFromCache('EWRmedio_Model_Lists')->getCategoryList(),
		);

		return $this->responseView('EWRmedio_ViewPublic_CategoryAdmin', 'EWRmedio_CategoryAdmin', $viewParams);
	}

	public function actionAdminKeywords()
	{
		if ($this->_request->isPost())
		{
			$input = $this->_input->filter(array(
				'keywords' => XenForo_Input::ARRAY_SIMPLE,
				'submit' => XenForo_Input::STRING,
			));

			$this->getModelFromCache('EWRmedio_Model_Keywords')->deleteKeywords($input['keywords']);
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media/admin/keywords'));
		}

		$start = $this->_input->filterSingle('page', XenForo_Input::UINT) < 1 ? 1 : $this->_input->filterSingle('page', XenForo_Input::UINT);
		$stop = 100;

		$viewParams = array(
			'start' => $start,
			'stop' => $stop,
			'count' => $this->getModelFromCache('EWRmedio_Model_Keywords')->getKeywordCount(),
			'keyList' => $this->getModelFromCache('EWRmedio_Model_Keywords')->getKeywords($start, $stop),
		);

		return $this->responseView('EWRmedio_ViewPublic_KeywordAdmin', 'EWRmedio_KeywordAdmin', $viewParams);
	}

	public function actionAdminServices()
	{
		$viewParams = array(
			'srvList' => $this->getModelFromCache('EWRmedio_Model_Services')->getServices(),
		);

		return $this->responseView('EWRmedio_ViewPublic_ServiceAdmin', 'EWRmedio_ServiceAdmin', $viewParams);
	}

	public function actionAdminImport()
	{
		$fileTransfer = new Zend_File_Transfer_Adapter_Http();

		if ($fileTransfer->isUploaded('upload_file'))
		{
			$fileInfo = $fileTransfer->getFileInfo('upload_file');
			$fileName = $fileInfo['upload_file']['tmp_name'];

			$this->getModelFromCache('EWRmedio_Model_Services')->importService($fileName);
		}

		return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media/admin/services'));
	}

	public function actionAdminRebuild()
	{
		XenForo_Model::create('EWRmedio_Model_Services')->rebuildServices();

		return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('media/admin/services'));
	}

	public static function getSessionActivityDetailsForList(array $activities)
	{
		return new XenForo_Phrase('viewing_media_library');
	}

	protected function _preDispatch($action)
	{
		parent::_preDispatch($action);

		$this->perms = $this->getModelFromCache('EWRmedio_Model_Perms')->getPermissions();

		if (!$this->perms['admin']) { throw $this->getNoPermissionResponseException(); }
	}
}