<?php
if (XenForo_Application::$versionId < 1020000) {

    /**
     * Extend AccountConfirmation controller to catch succesful confirmations
     * and sync them to master.
     *
     * @see XenForo_ControllerPublic_AccountConfirmation
     */
    class Waindigo_XenSso_Slave_Extend_XenForo_ControllerPublic_AccountConfirmation extends XFCP_Waindigo_XenSso_Slave_Extend_XenForo_ControllerPublic_AccountConfirmation
    {

        /**
         *
         * @see XenForo_ControllerPublic_AccountConfirmation::responseView()
         */
        public function responseView($viewName, $templateName = 'DEFAULT', array $params = array(), array $containerParams = array())
        {
            if ($viewName == 'XenForo_ViewPublic_Register_Confirm' && $templateName == 'register_confirm' &&
                 isset($params['user'])) {
                Waindigo_XenSso_Slave_Sync::activateAccount($params['user']);
            }

            return parent::responseView($viewName, $templateName, $params, $containerParams);
        } /* END responseView */
    }
} else {

    /**
     * Extend AccountConfirmation controller to catch succesful confirmations
     * and sync them to master.
     *
     * @see XenForo_ControllerPublic_AccountConfirmation
     */
    class Waindigo_XenSso_Slave_Extend_XenForo_ControllerPublic_AccountConfirmation extends XFCP_Waindigo_XenSso_Slave_Extend_XenForo_ControllerPublic_AccountConfirmation
    {

        /**
         *
         * @see XenForo_ControllerPublic_AccountConfirmation::responseView()
         */
        public function responseView($viewName = '', $templateName = '', array $params = array(), array $containerParams = array())
        {
            if ($viewName == 'XenForo_ViewPublic_Register_Confirm' && $templateName == 'register_confirm' &&
                 isset($params['user'])) {
                Waindigo_XenSso_Slave_Sync::activateAccount($params['user']);
            }

            return parent::responseView($viewName, $templateName, $params, $containerParams);
        } /* END responseView */
    }
}