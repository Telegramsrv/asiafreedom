<?php

class Waindigo_CustomPostFields_Listener_LoadClass extends Waindigo_Listener_LoadClass
{

    protected function _getExtendedClasses()
    {
        return array(
            'Waindigo_CustomPostFields' => array(
                'controller' => array(
                    'XenForo_ControllerPublic_Post',
                    'Waindigo_Library_ControllerAdmin_Library',
                    'Waindigo_Library_ControllerPublic_Article',
                    'Waindigo_Library_ControllerPublic_ArticlePage',
                    'Waindigo_Library_ControllerPublic_Library',
                    'XenForo_ControllerAdmin_Forum',
                    'XenForo_ControllerPublic_Forum',
                    'XenForo_ControllerPublic_Thread'
                ), /* END 'controller' */
                'datawriter' => array(
                    'XenForo_DataWriter_DiscussionMessage_Post',
                    'Waindigo_Library_DataWriter_ArticlePage',
                    'Waindigo_Library_DataWriter_Library',
                    'XenForo_DataWriter_Forum'
                ), /* END 'datawriter' */
                'installer_waindigo' => array(
                    'Waindigo_Library_Install_Controller'
                ), /* END 'installer_waindigo' */
                'model' => array(
                    'XenForo_Model_AddOn'
                ), /* END 'model' */
                'search_data' => array(
                    'XenForo_Search_DataHandler_Post'
                ), /* END 'search_data' */
            ), /* END 'Waindigo_CustomPostFields' */
        );
    } /* END _getExtendedClasses */

    public static function loadClassController($class, array &$extend)
    {
        $loadClassController = new Waindigo_CustomPostFields_Listener_LoadClass($class, $extend, 'controller');
        $extend = $loadClassController->run();
    } /* END loadClassController */

    public static function loadClassDataWriter($class, array &$extend)
    {
        $loadClassDataWriter = new Waindigo_CustomPostFields_Listener_LoadClass($class, $extend, 'datawriter');
        $extend = $loadClassDataWriter->run();
    } /* END loadClassDataWriter */

    public static function loadClassInstallerWaindigo($class, array &$extend)
    {
        $loadClassInstallerWaindigo = new Waindigo_CustomPostFields_Listener_LoadClass($class, $extend, 'installer_waindigo');
        $extend = $loadClassInstallerWaindigo->run();
    } /* END loadClassInstallerWaindigo */

    public static function loadClassModel($class, array &$extend)
    {
        $loadClassModel = new Waindigo_CustomPostFields_Listener_LoadClass($class, $extend, 'model');
        $extend = $loadClassModel->run();
    } /* END loadClassModel */

    public static function loadClassSearchData($class, array &$extend)
    {
        $loadClassSearchData = new Waindigo_CustomPostFields_Listener_LoadClass($class, $extend, 'search_data');
        $extend = $loadClassSearchData->run();
    } /* END loadClassSearchData */
}