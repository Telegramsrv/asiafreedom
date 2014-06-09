<?php

class Waindigo_XenSso_Slave_Listener_LoadClass extends Waindigo_Listener_LoadClass
{

    protected function _getExtendedClasses()
    {
        return array(
            'Waindigo_XenSso_Slave' => array(
                'controller' => array(
                    'XenForo_ControllerPublic_Login',
                    'XenForo_ControllerPublic_Register',
                    'XenForo_ControllerPublic_AccountConfirmation',
                    'XenForo_ControllerAdmin_User'
                ), /* END 'datawriter' */
                'datawriter' => array(
                    'XenForo_DataWriter_User'
                ), /* END 'datawriter' */
            ), /* END 'Waindigo_XenSso_Master' */
        );
    } /* END _getExtendedClasses */

    public static function loadClassController($class, array &$extend)
    {
        $loadClassController = new Waindigo_XenSso_Slave_Listener_LoadClass($class, $extend, 'controller');
        $extend = $loadClassController->run();
    } /* END loadClassController */

    public static function loadClassDataWriter($class, array &$extend)
    {
        $loadClassDataWriter = new Waindigo_XenSso_Slave_Listener_LoadClass($class, $extend, 'datawriter');
        $extend = $loadClassDataWriter->run();
    } /* END loadClassDataWriter */
}