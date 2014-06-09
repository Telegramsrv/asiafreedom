<?php

/**
 * Extend Login controller to catch failed logins and forward succesful ones to
 * the master XenSSO server
 *
 * @see XenForo_ControllerPublic_Login
 */
class Waindigo_XenSso_Slave_Extend_XenForo_ControllerPublic_Login_Base extends XFCP_Waindigo_XenSso_Slave_Extend_XenForo_ControllerPublic_Login
{

    /**
     * Catch redirects and check if they are a succesful redirect for a logged
     * in user, if so, validate that the user exists on the master
     *
     * @param integer See {@link XenForo_ControllerResponse_Redirect}
     * @param string Target to redirect to
     * @param mixed Message with which to redirect
     * @param array Extra parameters for the redirect
     *
     * @return parent::responseRedirect
     */
    public function responseRedirect($redirectType, $redirectTarget, $redirectMessage = null,
        array $redirectParams = array())
    {

        // get info about visitor
        $visitor = XenForo_Visitor::getInstance();
        $userId = $visitor->user_id;

        // Check if it's a success redirect and if the visitor is logged in
        if ($redirectType == XenForo_ControllerResponse_Redirect::SUCCESS && $userId > 0) {
            // Validate if user exists on master
            Waindigo_XenSso_Slave_Sync::copyToMaster($userId);
        }

        // forward to parent
        return parent::responseRedirect($redirectType, $redirectTarget, $redirectMessage, $redirectParams);
    } /* END responseRedirect */
}

if (XenForo_Application::$versionId < 1020000) {

    class Waindigo_XenSso_Slave_Extend_XenForo_ControllerPublic_Login extends Waindigo_XenSso_Slave_Extend_XenForo_ControllerPublic_Login_Base
    {

        /**
         * Catch error responses, validates if they are a "user not found" or
         * "incorrect password" error and if so turns it into an OpenID login
         */
        protected function _loginErrorResponse($error, $defaultLogin, $needCaptcha, $redirect = false)
        {
            // Receive user input
            $data = $this->_input->filter(
                array(
                    'login' => XenForo_Input::STRING,
                    'password' => XenForo_Input::STRING,
                    'remember' => XenForo_Input::UINT,
                    'register' => XenForo_Input::UINT,
                    'redirect' => XenForo_Input::STRING,
                    'cookie_check' => XenForo_Input::UINT
                ));

            // Check if this is an error we want to act on
            if (!in_array($error->getPhraseName(),
                array(
                    'incorrect_password',
                    'requested_user_x_not_found'
                )) || $data['register'] || empty($data['login']) || empty($data['password'])) {
                // Nope, have the parent handle it
                return parent::_loginErrorResponse($error, $defaultLogin, $needCaptcha, $redirect);
            }

            // Get dynamic redirect if redirect is not set
            if (empty($data['redirect'])) {
                $data['redirect'] = $this->getDynamicRedirect();
            }

            // Encrypt the user input
            $authData = Waindigo_XenSso_Shared_Secure::encrypt($data);

            // Store relevant data in session
            $session = XenForo_Application::get('session');
            $session->set('userName', $data['login']);
            $session->set('redirect', empty($data['redirect']) ? $this->getDynamicRedirect() : $data['redirect']);

            // Set callback params, will be used on callback url's after the
            // provider is done
            $session->set('callbackParams',
                array(
                    'errorType' => $error->getPhraseName()
                ));

            // Set request params, to be send along with the OpenID request
            $session->set('requestParams',
                array(
                    'authData' => $authData,
                    'prompts' => 0
                ));

            // Redirect to OpenID login page
            return parent::responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
                XenForo_Link::buildPublicLink('sso-slave/login'));
        } /* END _loginErrorResponse */
    }
} else {

    class Waindigo_XenSso_Slave_Extend_XenForo_ControllerPublic_Login extends Waindigo_XenSso_Slave_Extend_XenForo_ControllerPublic_Login_Base
    {

        /**
         * Catch error responses, validates if they are a "user not found" or
         * "incorrect password" error and if so turns it into an OpenID login
         */
        protected function _loginErrorResponse($error, $defaultLogin, $needCaptcha, $redirect = false, array $postData = null)
        {
            // Receive user input
            $data = $this->_input->filter(
                array(
                    'login' => XenForo_Input::STRING,
                    'password' => XenForo_Input::STRING,
                    'remember' => XenForo_Input::UINT,
                    'register' => XenForo_Input::UINT,
                    'redirect' => XenForo_Input::STRING,
                    'cookie_check' => XenForo_Input::UINT
                ));

            // Check if this is an error we want to act on
            if (!in_array($error->getPhraseName(),
                array(
                    'incorrect_password',
                    'requested_user_x_not_found'
                )) || $data['register'] || empty($data['login']) || empty($data['password'])) {
                // Nope, have the parent handle it
                return parent::_loginErrorResponse($error, $defaultLogin, $needCaptcha, $redirect,
                    $postData);
            }

            // Get dynamic redirect if redirect is not set
            if (empty($data['redirect'])) {
                $data['redirect'] = $this->getDynamicRedirect();
            }

            // Encrypt the user input
            $authData = Waindigo_XenSso_Shared_Secure::encrypt($data);

            // Store relevant data in session
            $session = XenForo_Application::get('session');
            $session->set('userName', $data['login']);
            $session->set('redirect', empty($data['redirect']) ? $this->getDynamicRedirect() : $data['redirect']);

            // Set callback params, will be used on callback url's after the
            // provider is done
            $session->set('callbackParams',
                array(
                    'errorType' => $error->getPhraseName()
                ));

            // Set request params, to be send along with the OpenID request
            $session->set('requestParams',
                array(
                    'authData' => $authData,
                    'prompts' => 0
                ));

            // Redirect to OpenID login page
            return parent::responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
                XenForo_Link::buildPublicLink('sso-slave/login'));
        } /* END _loginErrorResponse */
    }
}