<?php

class Waindigo_UserFieldCats_Listener_LoadClass extends Waindigo_Listener_LoadClass
{
    protected function _getExtendedClasses()
    {
        return array(
            'Waindigo_UserFieldCats' => array(
                'controller' => array(
                    'XenForo_ControllerAdmin_UserField',
                    'XenForo_ControllerPublic_Account',
                ), /* END 'controller' */
                'model' => array(
                    'XenForo_Model_UserField',
                ), /* END 'model' */
                'datawriter' => array(
                    'XenForo_DataWriter_UserField',
                ), /* END 'datawriter' */
            ), /* END 'Waindigo_UserFieldCats' */
        );
    } /* END _getExtendedClasses */

    public static function loadClassController($class, array &$extend)
    {
        $loadClassController = new Waindigo_UserFieldCats_Listener_LoadClass($class, $extend, 'controller');
        $extend = $loadClassController->run();
    } /* END loadClassController */

    public static function loadClassModel($class, array &$extend)
    {
        $loadClassModel = new Waindigo_UserFieldCats_Listener_LoadClass($class, $extend, 'model');
        $extend = $loadClassModel->run();
    } /* END loadClassModel */

    public static function loadClassDataWriter($class, array &$extend)
    {
        $loadClassDataWriter = new Waindigo_UserFieldCats_Listener_LoadClass($class, $extend, 'datawriter');
        $extend = $loadClassDataWriter->run();
    } /* END loadClassDataWriter */
}