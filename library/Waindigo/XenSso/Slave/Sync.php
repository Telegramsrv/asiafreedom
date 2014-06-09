<?php

/**
 * Sync methods
 */
class Waindigo_XenSso_Slave_Sync
{

    protected static $_fbUser = null;

    /**
     * Validate if the given user exists on the master
     *
     * @param int $userId
     * @return void
     */
    public static function copyToMaster($userId)
    {
        // Validate user input
        if ((empty($_POST['login']) && empty($_POST['username'])) || empty($_POST['password'])) {
            return;
        }

        // Get user info
        $userModel = new XenForo_Model_User();
        if (!$user = $userModel->getUserById($userId)) {
            return;
        }

        // Check if user info matches user input
        if (isset($_POST['login']) && strtolower($user['username']) != strtolower($_POST['login']) &&
             strtolower($user['email']) != strtolower($_POST['login'])) {
            XenForo_Error::logException(
                new Exception(__CLASS__ . '::' . __METHOD__ . ' - User input did not match retreived user data (1)'));
            return;
        }

        // Check if user info matches user input
        if (isset($_POST['username']) && strtolower($user['username']) != strtolower($_POST['username'])) {
            XenForo_Error::logException(
                new Exception(__CLASS__ . '::' . __METHOD__ . ' - User input did not match retreived user data (2)'));
            return;
        }

        // Set data to be send to master
        $authModel = new Waindigo_XenSso_Shared_Model_Auth();
        $data = $authModel->getSyncUserById($userId);
        unset($data['user_id']);

        // Encrypt data
        $authData = Waindigo_XenSso_Shared_Secure::encrypt($data,
            XenForo_Application::get('options')->waindigo_xenSso_slave_secretPrivate);

        // Send data to master and capture result
        if ($result = self::httpRequest('sync', array(
            'authData' => $authData
        ))) {

            if (substr($result, 0, 5) == 'ERROR') {
                XenForo_Error::debug('%s', __CLASS__ . '::' . __METHOD__ . ' - Sync Error: ' . $result);
                return;
            }

            if (strlen($result) > 100) {
                XenForo_Error::logException(
                    new Exception(__CLASS__ . '::' . __METHOD__ . ' - Unexpected Result: ' . strip_tags($result)));
                return;
            }

            // Save auth key in session
            $session = XenForo_Application::get('session');
            $session->set('xensso_auth_key', $result);

            Waindigo_XenSso_Slave_Listener_ControllerPreDispatch::setAuthKey($result);
        }
    } /* END copyToMaster */

    /**
     * Validate if the given user exists on the master and if so copy it to
     * slave
     *
     * @param int $userId
     * @return void
     */
    public static function copyFromMaster($userId)
    {
        $inputData = Waindigo_XenSso_Shared_Secure::encrypt(array(
            $userId
        ), XenForo_Application::get('options')->waindigo_xenSso_slave_secretPrivate);

        // Send data to master and capture result
        if ($result = self::httpRequest('retrieve', array(
            'inputData' => $inputData
        )) and $result !== '0') {
            $result = Waindigo_XenSso_Shared_Secure::decrypt($result,
                XenForo_Application::get('options')->waindigo_xenSso_slave_secretPrivate);

            if (!$result or is_string($result)) {
                XenForo_Error::debug('%s', __CLASS__ . '::' . __METHOD__ . ' - Sync Error: ' . $result);
                return false;
            }

            try {
                $r = Waindigo_XenSso_Shared_User::createAccount((array) $result, false);

                if (!$r) {
                    return self::syncExistingUser($result);
                } else {
                    return $r;
                }

                return $r;
            } catch (XenForo_Exception $e) {
                XenForo_Error::debug('%s', __CLASS__ . '::' . __METHOD__ . ' - createAccount Error: ' . $e->getMessage());
                return self::syncExistingUser($result);
            }
        }

        return false;
    } /* END copyFromMaster */

    /**
     * Synchronizes missing data for existing users (if any)
     *
     * @param array $syncUser
     * @return bool array
     */
    public static function syncExistingUser($syncUser)
    {
        if (empty($syncUser['facebook_auth_id'])) {
            return false;
        }

        $userModel = new XenForo_Model_User();
        $user = $userModel->getUserByEmail($syncUser['email']);
        $fbUser = Waindigo_XenSso_Slave_Sync::getFbUser();

        if (!$user or !$fbUser) {
            return false;
        }

        if ($syncUser['facebook_auth_id'] == $fbUser['id'] && $user['email'] == $fbUser['email']) {
            try {
                $externalModel = new XenForo_Model_UserExternal();
                $externalModel->updateExternalAuthAssociation('facebook', $fbUser['id'], $user['user_id']);

                $dw = new XenForo_DataWriter_User();
                $dw->setExistingData($user);
                $dw->set('facebook_auth_id', $fbUser['id']);
                $dw->save();

                return $dw->getMergedData();
            } catch (Exception $e) {}
        }

        return false;
    } /* END syncExistingUser */

    /**
     * Valdiate if an account with the given username exists on the master.
     * If so, sync it to slave.
     *
     * @param array $data
     * @param null|array $ignoreIf
     *
     * @return bool array
     */
    public static function validateMasterExists($data, $ignoreIf = null)
    {

        // Encrypt data
        $inputData = Waindigo_XenSso_Shared_Secure::encrypt(array(
            $data,
            $ignoreIf
        ), XenForo_Application::get('options')->waindigo_xenSso_slave_secretPrivate);

        // Send data to master and capture result
        if ($result = self::httpRequest('validateExists', array(
            'inputData' => $inputData
        )) and $result = json_decode($result)) {
            return (bool) $result;
        }

        return false;
    } /* END validateMasterExists */

    /**
     * Sync account activation to master
     *
     * @param array $user
     * @return void
     */
    public static function activateAccount(array $user)
    {
        if ($user['user_state'] != 'valid') {
            return false;
        }

        $data = array(
            'email' => $user['email']
        );
        $inputData = Waindigo_XenSso_Shared_Secure::encrypt($data,
            XenForo_Application::get('options')->waindigo_xenSso_slave_secretPrivate);

        self::httpRequest('activateAccount', array(
            'inputData' => $inputData
        ));
    } /* END activateAccount */

    /**
     * Return ignore data for the current user
     * Used to provide external account details so that users of the same
     * external account don't get confronted with validation errors for their
     * own user
     *
     * @param string $username
     * @return array bool
     */
    public static function getIgnoreTerms($username)
    {
        $ignoreIf = null;
        $visitor = XenForo_Visitor::getInstance();

        // If the user is already logged in we can just use their active details
        if (!empty($visitor->user_id)) {
            if ($visitor->username == $username) {
                if (XenForo_Application::$versionId < 1030000) {
                    return array(
                        'facebook_auth_id' => $visitor->facebook_auth_id,
                        'email' => $visitor->email
                    );
                } else {
                    return array(
                        'email' => $visitor->email
                    );
                }
            }
        } else {
            $fbUser = self::getFbUser();
            if (!$fbUser) {
                return false;
            }

            // And finally return ignore fields
            return array(
                'facebook_auth_id' => $fbUser['id'],
                'email' => $fbUser['email']
            );
        }
    } /* END getIgnoreTerms */

    /**
     * Get FB user from request
     *
     * This shouldn't really be in the sync class but for now it'll do
     *
     * @return bool array
     */
    public static function getFbUser()
    {
        if (XenForo_Application::$versionId >= 1030000) {
            return false;
        }

        if (self::$_fbUser !== null) {
            return self::$_fbUser;
        }

        // Gather request info
        $request = new Zend_Controller_Request_Http();
        $requestUri = $request->get('_xfRequestUri');

        // If xfRequestUri is set get the request data from that uri instead
        if (!empty($requestUri)) {
            $data = parse_url($requestUri);
            if (is_array($data) and isset($data['query'])) {
                parse_str($data['query'], $query);
                $input = new XenForo_Input($query);
            }
        }

        // Revert to getting data from current request
        if (!isset($input)) {
            $input = new XenForo_Input(new Zend_Controller_Request_Http());
        }

        // Try to retreive the fbToken
        $fbToken = $input->filterSingle('t', XenForo_Input::STRING);

        // Nope, maybe fb_token ?
        if (!$fbToken) {
            $fbToken = $input->filterSingle('fb_token', XenForo_Input::STRING);
        }

        // Nope, we're doing it the hard way ..
        if (!$fbToken) {
            // Build original redirect uri for validation purposes
            $assocUserId = $input->filterSingle('assoc', XenForo_Input::UINT);
            $fbRedirectUri = XenForo_Link::buildPublicLink('canonical:register/facebook', false,
                array(
                    'assoc' => ($assocUserId ? $assocUserId : false)
                ));

            // Retreive auth code
            $code = $input->filterSingle('code', XenForo_Input::STRING);
            if (!$code) {
                return false;
            }

            // And finally attempt to get the token
            $token = XenForo_Helper_Facebook::getAccessTokenFromCode($code, $fbRedirectUri);
            $fbError = XenForo_Helper_Facebook::getFacebookRequestErrorInfo($token, 'access_token');
            if ($fbError) {
                self::$_fbUser = false;
                return false; // or not
            }

            // Success
            $fbToken = $token['access_token'];
        }

        // Now try to get user info from the provided token
        $fbUser = XenForo_Helper_Facebook::getUserInfo($fbToken);
        $fbError = XenForo_Helper_Facebook::getFacebookRequestErrorInfo($fbUser, 'id');
        if ($fbError) {
            self::$_fbUser = false;
            return false;
        }

        self::$_fbUser = $fbUser;
        return $fbUser;
    } /* END getFbUser */

    /**
     * Make http request to master
     *
     * @param array $data
     * @return string
     */
    public static function httpRequest($action, array $data)
    {
        $url = XenForo_Application::get('options')->waindigo_xenSso_slave_masterUrl . 'index.php?sync/' . $action;
        $postData = XenForo_Link::buildQueryString($data);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);

        $result = curl_exec($ch);
        if ($result === false) {
            XenForo_Error::logException(
                new Exception(
                    'httpRequest Failed, url: ' . $url . ', curl error (' . curl_errno($ch) . '): ' . curl_error($ch)));
        }

        curl_close($ch);

        return $result;
    } /* END httpRequest */
}