<?php

/**
 *
 * @see XenForo_ControllerAdmin_User
 */
class Waindigo_XenSso_Slave_Extend_XenForo_ControllerAdmin_User extends XFCP_Waindigo_XenSso_Slave_Extend_XenForo_ControllerAdmin_User
{

    /**
     *
     * @see XenForo_ControllerAdmin_User::_preDispatch()
     */
    protected function _preDispatch($action)
    {
        XenForo_DataWriter::create('XenForo_DataWriter_User');
        Waindigo_XenSso_Slave_Extend_XenForo_DataWriter_User::$_validateWithMaster = false;
        return parent::_preDispatch($action);
    } /* END _preDispatch */
}