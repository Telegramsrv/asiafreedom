<?php

class Waindigo_CustomPostFields_Listener_TemplatePostRender extends Waindigo_Listener_TemplatePostRender
{

    protected function _getTemplates()
    {
        return array(
            'thread_create',
            'waindigo_article_create_library',
            'post_edit',
            'thread_view',
            'waindigo_article_page_create_new_library',
            'waindigo_article_page_edit_library',
            'post_field_edit'
        );
    } /* END _getTemplates */

    public static function templatePostRender($templateName, &$content, array &$containerData,
        XenForo_Template_Abstract $template)
    {
        $templatePostRender = new Waindigo_CustomPostFields_Listener_TemplatePostRender($templateName, $content,
            $containerData, $template);
        list($content, $containerData) = $templatePostRender->run();
    } /* END templatePostRender */

    protected function _threadCreate()
    {
        $viewParams = $this->_fetchViewParams();
        $customPostFields = $viewParams['customPostFields'];
        if ($customPostFields) {
            foreach ($customPostFields as $postFieldGroup) {
                $replace = '';
                foreach ($postFieldGroup['fields'] as $field) {
                    $pattern = '#<dl class="ctrlUnit fullWidth surplusLabel">\s*<dt><label for="ctrl_title_thread_create">.*</dl>#Us';
                    if (!$field['below_title_on_thread_create']) {
                        continue;
                    }
                    if ($field['field_type'] == 'callback') {
                        $field['validator_name'] = 'custom_post_field_' . $field['field_id'];
                        $field['name'] = 'custom_post_fields[' . $field['field_id'] . ']';
                        $field['custom_field_type'] = 'post';
                        $replace .= call_user_func_array(
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
                        $replace .= $this->_render('custom_post_field_edit', $viewParams);
                    }
                }
                if ($replace) {
                    $replace = '${0}' . $this->_escapeDollars(
                        '<h3 class="textHeading">' . (isset($postFieldGroup['title']) ? $postFieldGroup['title'] : '') .
                             '</h3>' . $replace);
                    $this->_patternReplace($pattern, $replace);
                }
            }
        }
    } /* END _threadCreate */

    protected function _waindigoArticleCreateLibrary()
    {
        $this->_threadCreate();
    } /* END _waindigoArticleCreateLibrary */

    protected function _postEdit()
    {
        $viewParams = $this->_fetchViewParams();
        $prepend = '';
        if (isset($viewParams['customPostFields']) && $viewParams['customPostFields']) {
            $customPostFields = $viewParams['customPostFields'];
            foreach ($customPostFields as $postFieldGroup) {
                $prepend .= '<h3 class="textHeading">' .
                     (isset($postFieldGroup['title']) ? $postFieldGroup['title'] : '') . '</h3>';
                foreach ($postFieldGroup['fields'] as $field) {
                    if ($field['field_type'] == 'callback') {
                        $field['validator_name'] = 'custom_post_field_' . $field['field_id'];
                        $field['name'] = 'custom_post_fields[' . $field['field_id'] . ']';
                        $prepend .= call_user_func_array(
                            array(
                                $field['field_callback_class'],
                                $field['field_callback_method']
                            ),
                            array(
                                $this->_template,
                                $field,
                                'custom_post_field'
                            ))->render() . '<input type="hidden" name="custom_post_fields_shown[]" value="' .
                             $field['field_id'] . '" />';
                    } else {
                        $viewParams['field'] = $field;
                        $prepend .= $this->_render('custom_post_field_edit', $viewParams);
                    }
                }
            }
        }
        if ($this->_templateName == 'thread_view') {
            $pattern = '#<div class="submitUnit">#';
            $replacement = '<div class="xenForm">' . $this->_escapeDollars($prepend) . '</div>${0}';
            ;
        } else {
            $pattern = '#<dl class="ctrlUnit submitUnit">#';
            $replacement = $this->_escapeDollars($prepend) . '${0}';
        }
        $this->_patternReplace($pattern, $replacement);
    } /* END _postEdit */

    protected function _threadView()
    {
        $this->_postEdit();
    } /* END _threadView */

    protected function _waindigoArticlePageCreateNewLibrary()
    {
        $this->_postEdit();
    } /* END _waindigoArticlePageCreateNewLibrary */

    protected function _waindigoArticlePageEditLibrary()
    {
        $this->_postEdit();
    } /* END _waindigoArticlePageEditLibrary */

    protected function _postFieldEdit()
    {
        $pattern = '#<li>\s*<label for="ctrl_field_type_callback">\s*<input type="radio" name="field_type" value="callback" id="ctrl_field_type_callback"[^>]*>[^<]*</label>\s*</li>#Us';
        $replacement = $this->_escapeDollars($this->_render('waindigo_field_edit_php_callback_customfields'));
        $this->_patternReplace($pattern, $replacement);

        $viewParams = $this->_fetchViewParams();
        $pattern = '#<ul class="FieldChoices">.*</ul>\s*<input[^>]*>\s*<p class="explain">[^<]*</p>#Us';
        preg_match($pattern, $this->_contents, $matches);
        if (isset($matches[0])) {
            $viewParams['contents'] = $matches[0];
            $replacement = $this->_escapeDollars($this->_render('waindigo_field_edit_choice_customfields', $viewParams));
            $this->_patternReplace($pattern, $replacement);
        }

        $pattern = '#</li>\s*</ul>\s*<dl class="ctrlUnit submitUnit">#';
        $replacement = $this->_escapeDollars($this->_render('waindigo_field_edit_panes_customfields')) . '${0}';
        $this->_patternReplace($pattern, $replacement);

        $pattern = '#<dl class="ctrlUnit">\s*<dt>\s*<label for="ctrl_display_template">.*</dl>#Us';
        $replacement = $this->_escapeDollars($this->_render('waindigo_field_edit_value_display_customfields'));
        $this->_patternReplace($pattern, $replacement);
    } /* END _postFieldEdit */ /* END _userFieldEdit */
}