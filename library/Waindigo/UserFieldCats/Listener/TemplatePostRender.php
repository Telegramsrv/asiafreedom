<?php

class Waindigo_UserFieldCats_Listener_TemplatePostRender extends Waindigo_Listener_TemplatePostRender
{
    protected function _getTemplates()
    {
        return array(
            'user_field_edit',
        );
    } /* END _getTemplates */

    public static function templatePostRender($templateName, &$content, array &$containerData, XenForo_Template_Abstract $template)
    {
        $templatePostRender = new Waindigo_UserFieldCats_Listener_TemplatePostRender($templateName, $content, $containerData, $template);
        list($content, $containerData) = $templatePostRender->run();
    } /* END templatePostRender */

    protected function _userFieldEdit()
    {
        $pattern = '#<label for="ctrl_display_group_custom">.*</label>#Us';
        $this->_replaceWithTemplateAtPattern($pattern, 'waindigo_user_field_edit_userfieldcats');
    } /* END _userFieldEdit */
}