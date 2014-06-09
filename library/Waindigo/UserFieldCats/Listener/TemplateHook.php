<?php

class Waindigo_UserFieldCats_Listener_TemplateHook extends Waindigo_Listener_TemplateHook
{
    protected function _getHooks()
    {
        return array(
            'account_wrapper_sidebar_settings',
        );
    } /* END _getHooks */

    public static function templateHook($hookName, &$contents, array $hookParams, XenForo_Template_Abstract $template)
    {
        $templateHook = new Waindigo_UserFieldCats_Listener_TemplateHook($hookName, $contents, $hookParams, $template);
        $contents = $templateHook->run();
    } /* END templateHook */

    protected function _accountWrapperSidebarSettings()
    {
        $this->_appendTemplate('waindigo_account_wrapper_userfieldcats');
    } /* END _accountWrapperSidebarSettings */
}