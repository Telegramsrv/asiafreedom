<?php

/**
 *
 * @see Waindigo_Library_ControllerAdmin_Library
 */
class Waindigo_CustomPostFields_Extend_Waindigo_Library_ControllerAdmin_Library extends XFCP_Waindigo_CustomPostFields_Extend_Waindigo_Library_ControllerAdmin_Library
{

    /**
     *
     * @see Waindigo_Library_ControllerAdmin_Library::actionEdit()
     */
    public function actionEdit()
    {
        /* @var $response XenForo_ControllerResponse_View */
        $response = parent::actionEdit();

        if ($response instanceof XenForo_ControllerResponse_View) {
            if (isset($response->params['library'])) {
                $node = $response->params['library'];
            }

            $postFieldModel = $this->_getPostFieldModel();

            $nodeRequiredPostFields = array();
            if (isset($node['node_id'])) {
                $nodeId = $node['node_id'];

                $nodePostFields = array_keys($postFieldModel->getPostFieldsInForum($nodeId));
                if ($node['required_post_fields'])
                    $nodeRequiredPostFields = unserialize($node['required_post_fields']);

                $headers = array(
                    'post_header' => '_header_post_node.' . $nodeId,
                    'post_footer' => '_footer_post_node.' . $nodeId
                );

                $templates = $this->_getTemplateModel()->getTemplatesInStyleByTitles($headers);
            } else {
                $nodePostFields = array();
                $templates = array();
            }

            $response->params['postFieldGroups'] = $postFieldModel->getPostFieldsByGroups();
            $response->params['postFieldOptions'] = $postFieldModel->getPostFieldOptions();
            $response->params['nodeRequiredPostFields'] = ($nodeRequiredPostFields ? $nodeRequiredPostFields : array(
                0
            ));
            $response->params['nodePostFields'] = ($nodePostFields ? $nodePostFields : array(
                0
            ));
            $response->params['customPostFields'] = $postFieldModel->preparePostFields($postFieldModel->getPostFields(),
                true,
                (isset($node['custom_post_fields']) && $node['custom_post_fields'] ? unserialize(
                    $node['custom_post_fields']) : array()), true);

            foreach ($templates as $headerName => $template) {
                $key = array_search($headerName, $headers);
                if ($key) {
                    $response->params['template'][$key] = $template['template'];
                }
            }
        }

        return $response;
    } /* END actionEdit */

    /**
     *
     * @see Waindigo_Library_ControllerAdmin_Library::actionValidateField()
     */
    public function actionValidateField()
    {
        $this->_assertPostOnly();

        $field = $this->_getFieldValidationInputParams();

        if (preg_match('/^custom_post_field_([a-zA-Z0-9_]+)$/', $field['name'], $match)) {
            $writer = XenForo_DataWriter::create('Waindigo_Library_DataWriter_Library');

            $writer->setOption(XenForo_DataWriter_User::OPTION_ADMIN_EDIT, true);

            $writer->setCustomPostFields(array(
                $match[1] => $field['value']
            ));

            $errors = $writer->getErrors();
            if ($errors) {
                return $this->responseError($errors);
            }

            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, '',
                new XenForo_Phrase('redirect_field_validated',
                    array(
                        'name' => $field['name'],
                        'value' => $field['value']
                    )));
        } else {
            // handle normal fields
            return parent::actionValidateField();
        }
    } /* END actionValidateField */

    /**
     *
     * @return Waindigo_CustomPostFields_Model_PostField
     */
    protected function _getPostFieldModel()
    {
        return $this->getModelFromCache('Waindigo_CustomPostFields_Model_PostField');
    } /* END _getPostFieldModel */
}