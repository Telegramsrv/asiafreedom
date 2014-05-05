<?php


/**
 * @xfcp XenForo_ControllerPublic_Forum
 *
 */
class SimplePortal_Extend_ControllerPublic_Forum extends
	XFCP_SimplePortal_Extend_ControllerPublic_Forum
{
	public function actionAddThread()
	{
		$forumId = $this->_input->filterSingle('node_id', XenForo_Input::UINT);
		$forumName = $this->_input->filterSingle('node_name', XenForo_Input::STRING);

		$ftpHelper = $this->getHelper('ForumThreadPost');

		$promoteToPortal = $this->_input->filterSingle('promote_to_portal', XenForo_Input::UINT);

		if ($promoteToPortal && SimplePortal_Static::getItemModel()->canPromoteItem('thread', $ftpHelper->assertForumValidAndViewable($forumId ? $forumId : $forumName))){
            $form = new SimplePortal_Form_Item($this->_input);
			$input = $form->getValidatedInputFields();

			XenForo_Application::set('extraportal.promoteThread', $input);
		}
		$parentReturn = parent::actionAddThread();
		
		return $parentReturn;
	}

}