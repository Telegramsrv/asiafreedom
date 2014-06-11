<?php

class Waindigo_XenSso_Slave_Listener_ControllerPreDispatch extends Waindigo_Listener_ControllerPreDispatch
{

    public static $_authKey = false;

    public function run()
    {
        $session = XenForo_Application::get('session');

        if ($session->get('xensso_auth_key')) {
            self::$_authKey = $session->get('xensso_auth_key');
            $session->remove('xensso_auth_key');
        }

        register_shutdown_function(array(
            __CLASS__,
            'preShutdown'
        ));

        parent::run();
    } /* END run */

    public static function preShutdown()
    {
        $session = XenForo_Application::get('session');
        $session->save();
    } /* END preShutdown */

    public static function getAuthKey()
    {
        return self::$_authKey;
    } /* END getAuthKey */

    public static function setAuthKey($authKey)
    {
        self::$_authKey = $authKey;
    } /* END setAuthKey */

    public static function controllerPreDispatch(XenForo_Controller $controller, $action)
    {
        $controllerPreDispatch = new Waindigo_XenSso_Slave_Listener_ControllerPreDispatch($controller, $action);
        $controllerPreDispatch->run();
    } /* END controllerPreDispatch */
}