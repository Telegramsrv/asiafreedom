<?php

class Waindigo_AdminImages_Listener_LoadClass extends Waindigo_Listener_LoadClass
{
    protected function _getExtendedClasses()
    {
        return array(
            'Waindigo_AdminImages' => array(
                'model' => array(
                    'XenForo_Model_Attachment',
                ), /* END 'model' */
            ), /* END 'Waindigo_AdminImages' */
        );
    } /* END _getExtendedClasses */

    public static function loadClassModel($class, array &$extend)
    {
        $loadClassModel = new Waindigo_AdminImages_Listener_LoadClass($class, $extend, 'model');
        $extend = $loadClassModel->run();
    } /* END loadClassModel */
}