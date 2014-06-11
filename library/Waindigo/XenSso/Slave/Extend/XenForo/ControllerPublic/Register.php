<?php

/**
 * Extend registration controller to catch succesful user signups so we can sync
 * them to the master
 */
class Waindigo_XenSso_Slave_Extend_XenForo_ControllerPublic_Register_Base extends XFCP_Waindigo_XenSso_Slave_Extend_XenForo_ControllerPublic_Register
{

    /**
     * Registers a new account (or associates with an existing one) using
     * Facebook.
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionFacebookRegister()
    {
        $this->_assertPostOnly();

        $userModel = $this->_getUserModel();

        $doAssoc = ($this->_input->filterSingle('associate', XenForo_Input::STRING) or
             $this->_input->filterSingle('force_assoc', XenForo_Input::UINT));

        if ($doAssoc) {
            $associate = $this->_input->filter(
                array(
                    'associate_login' => XenForo_Input::STRING,
                    'associate_password' => XenForo_Input::STRING
                ));

            $userId = $userModel->validateAuthentication($associate['associate_login'],
                $associate['associate_password'], $error);
            if (!$userId) {
                $user = Waindigo_XenSso_Slave_Sync::copyFromMaster($associate['associate_login']);
                $fbUser = Waindigo_XenSso_Slave_Sync::getFbUser();

                if ($fbUser && !empty($user['facebook_auth_id']) && $user['facebook_auth_id'] == $fbUser['id']) {
                    XenForo_Helper_Facebook::setUidCookie($fbUser['id']);

                    $redirect = XenForo_Application::get('session')->get('fbRedirect');
                    XenForo_Application::get('session')->changeUserId($user['user_id']);
                    XenForo_Visitor::setup($user['user_id']);

                    XenForo_Application::get('session')->remove('fbRedirect');
                    $redirect = $this->getDynamicRedirect(false, false);

                    return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, $redirect);
                }
            }
        }

        return parent::actionFacebookRegister();
    } /* END actionFacebookRegister */
}

if (XenForo_Application::$versionId < 1020000) {

    class Waindigo_XenSso_Slave_Extend_XenForo_ControllerPublic_Register extends Waindigo_XenSso_Slave_Extend_XenForo_ControllerPublic_Register_Base
    {

        /**
         * Catch response and check if it's for a succesful signup, if so sync
         * it to the master.
         *
         * @see XenForo_Controller::responseView
         */
        public function responseView($viewName, $templateName = 'DEFAULT', array $params = array(), array $containerParams = array())
        {
            // Check if this is a succesful signup
            if ($viewName == 'XenForo_ViewPublic_Register_Process' && isset($params['user'])) {
                // sync to master
                Waindigo_XenSso_Slave_Sync::copyToMaster($params['user']['user_id']);
            }

            return parent::responseView($viewName, $templateName, $params, $containerParams);
        } /* END responseView */
    }
} else {

    class Waindigo_XenSso_Slave_Extend_XenForo_ControllerPublic_Register extends Waindigo_XenSso_Slave_Extend_XenForo_ControllerPublic_Register_Base
    {

        /**
         * Catch response and check if it's for a succesful signup, if so sync
         * it to the master.
         *
         * @see XenForo_Controller::responseView
         */
        public function responseView($viewName = '', $templateName = '', array $params = array(), array $containerParams = array())
        {
            // Check if this is a succesful signup
            if ($viewName == 'XenForo_ViewPublic_Register_Process' && isset($params['user'])) {
                // sync to master
                Waindigo_XenSso_Slave_Sync::copyToMaster($params['user']['user_id']);
            }

            return parent::responseView($viewName, $templateName, $params, $containerParams);
        } /* END responseView */
    }
}