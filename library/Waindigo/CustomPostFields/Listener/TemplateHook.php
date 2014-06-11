<?php

class Waindigo_CustomPostFields_Listener_TemplateHook extends Waindigo_Listener_TemplateHook
{

    protected function _getHooks()
    {
        return array(
            'admin_forum_edit_tabs',
            'admin_forum_edit_panes',
            'waindigo_admin_library_edit_panes_library',
            'thread_create_fields_extra',
            'waindigo_article_create_fields_extra_library',
            'thread_reply',
            'message_content',
            'waindigo_article_message_content_library'
        );
    } /* END _getHooks */

    public static function templateHook($hookName, &$contents, array $hookParams, XenForo_Template_Abstract $template)
    {
        $templateHook = new Waindigo_CustomPostFields_Listener_TemplateHook($hookName, $contents, $hookParams, $template);
        $contents = $templateHook->run();
    } /* END templateHook */

    protected function _adminForumEditTabs()
    {
        $this->_appendTemplate('waindigo_forum_edit_tabs_custompostfields');
    } /* END _adminForumEditTabs */

    protected function _waindigoAdminLibraryEditTabsLibrary()
    {
        $this->_adminForumEditTabs();
    } /* END _waindigoAdminLibraryEditTabsLibrary */

    protected function _adminForumEditPanes()
    {
        $this->_appendTemplate('waindigo_forum_edit_panes_custompostfields');

        $viewParams = $this->_fetchViewParams();
        $customPostFields = $viewParams['customPostFields'];
        foreach ($customPostFields as $field) {
            if ($field['field_type'] == 'callback') {
                $field['node_id'] = $viewParams['forum']['node_id'];
                $field['validator_name'] = 'custom_post_field_' . $field['field_id'];
                $field['name'] = 'custom_post_fields[' . $field['field_id'] . ']';
                $this->_appendAtCodeSnippet(
                    '<input type="hidden" name="custom_post_fields_shown[]" value="' . $field['field_id'] . '" />',
                    call_user_func_array(
                        array(
                            $field['field_callback_class'],
                            $field['field_callback_method']
                        ),
                        array(
                            $this->_template,
                            $field
                        ))->render());
            }
        }
    } /* END _adminForumEditPanes */

    protected function _waindigoAdminLibraryEditPanesLibrary()
    {
        $this->_adminForumEditPanes();
    } /* END _waindigoAdminLibraryEditPanesLibrary */

    protected function _threadCreateFieldsExtra()
    {
        $viewParams = $this->_fetchViewParams();
        $customPostFields = $viewParams['customPostFields'];
        if ($customPostFields) {
            foreach ($customPostFields as $postFieldGroup) {
                $append = '';
                foreach ($postFieldGroup['fields'] as $field) {
                    if ($field['below_title_on_thread_create']) {
                        continue;
                    }
                    if ($field['field_type'] == 'callback') {
                        $field['validator_name'] = 'custom_post_field_' . $field['field_id'];
                        $field['name'] = 'custom_post_fields[' . $field['field_id'] . ']';
                        $field['custom_field_type'] = 'post';
                        $append .= call_user_func_array(
                            array(
                                $field['field_callback_class'],
                                $field['field_callback_method']
                            ),
                            array(
                                $this->_template,
                                $field
                            ))->render() . '<input type="hidden" name="custom_post_fields_shown[]" value="' .
                             $field['field_id'] . '" />';
                    } else {
                        $viewParams['field'] = $field;
                        $append .= $this->_render('custom_post_field_edit', $viewParams);
                    }
                }
                if ($append) {
                    $append = '<h3 class="textHeading">' .
                         (isset($postFieldGroup['title']) ? $postFieldGroup['title'] : '') . '</h3>' . $append;
                    $this->_append($append);
                }
            }
        }
    } /* END _threadCreateFieldsExtra */

    protected function _waindigoArticleCreateFieldsExtraLibrary()
    {
        $this->_threadCreateFieldsExtra();
    } /* END _waindigoArticleCreateFieldsExtraLibrary */

    protected function _threadReply()
    {
        $viewParams = $this->_fetchViewParams();
        $customPostFields = $viewParams['customPostFields'];
        if ($customPostFields) {
            foreach ($customPostFields as $postFieldGroup) {
                if (isset($postFieldGroup['title'])) {
                    $this->_append('<h3 class="textHeading">' . $postFieldGroup['title'] . '</h3>');
                }
                foreach ($postFieldGroup['fields'] as $field) {
                    if ($field['field_type'] == 'callback') {
                        $field['validator_name'] = 'custom_post_field_' . $field['field_id'];
                        $field['name'] = 'custom_post_fields[' . $field['field_id'] . ']';
                        $field['custom_field_type'] = 'post';
                        $this->_append(
                            call_user_func_array(
                                array(
                                    $field['field_callback_class'],
                                    $field['field_callback_method']
                                ),
                                array(
                                    $this->_template,
                                    $field
                                ))->render() . '<input type="hidden" name="custom_post_fields_shown[]" value="' .
                                 $field['field_id'] . '" />');
                    } else {
                        $viewParams['field'] = $field;
                        $this->_appendTemplate('custom_post_field_edit', $viewParams);
                    }
                }
            }
        }
    } /* END _threadReply */

    protected function _anyMessageContent($viewParams = null)
    {
        if (is_null($viewParams)) {
            $viewParams = $this->_fetchViewParams();
        }
        if (isset($viewParams['message']['custom_post_fields']) && $viewParams['message']['custom_post_fields']) {
            $viewParams['customPostFields'] = unserialize($viewParams['message']['custom_post_fields']);
        }
        if (isset($viewParams['forum'])) {
            $forum = $viewParams['forum'];
            $pattern = '#<article>#';
            $replacement = $this->_escapeDollars($this->_render('_header_post_node.' . $forum['node_id'], $viewParams)) .
                 '${0}';
            $this->_patternReplace($pattern, $replacement);
            $pattern = '#</article>#';
            $replacement = '${0}' .
                 $this->_escapeDollars($this->_render('_footer_post_node.' . $forum['node_id'], $viewParams));
            $this->_patternReplace($pattern, $replacement);
        }
    } /* END _anyMessageContent */

    protected function _messageContent()
    {
        $this->_anyMessageContent();
    } /* END _messageContent */

    protected function _waindigoArticleMessageContentLibrary()
    {
        $viewParams = $this->_fetchViewParams();
        $viewParams['forum'] = $viewParams['library'];
        $viewParams['thread'] = $viewParams['article'];
        $viewParams['thread']['first_post_id'] = $viewParams['thread']['first_article_page_id'];
        $viewParams['message']['post_id'] = $viewParams['message']['article_page_id'];
        $this->_anyMessageContent($viewParams);
    } /* END _waindigoArticleMessageContentLibrary */
}