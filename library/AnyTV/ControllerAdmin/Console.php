<?php
class AnyTV_ControllerAdmin_Console extends XenForo_ControllerAdmin_User
{
	public function actionIndex()
	{

		return $this->responseView('AnyTV_ViewAdmin_Featured', 'anytv_featured', $viewParams);
	}
}
