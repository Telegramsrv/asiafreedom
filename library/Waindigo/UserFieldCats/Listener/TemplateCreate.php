<?php

class Waindigo_UserFieldCats_Listener_TemplateCreate extends Waindigo_Listener_TemplateCreate
{
    protected function _getTemplates()
    {
        return array(
            'account_wrapper',
        );
    } /* END _getTemplates */

    public static function templateCreate(&$templateName, array &$params, XenForo_Template_Abstract $template)
    {
        $templateCreate = new Waindigo_UserFieldCats_Listener_TemplateCreate($templateName, $params, $template);
        list($templateName, $params) = $templateCreate->run();
    } /* END templateCreate */

    protected function _accountWrapper()
    {
        $this->_preloadTemplate('waindigo_account_wrapper_userfieldcats');
    } /* END _accountWrapper */
}