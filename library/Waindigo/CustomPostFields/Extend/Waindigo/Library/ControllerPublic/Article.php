<?php

/**
 *
 * @see Waindigo_Library_ControllerPublic_Article
 */
class Waindigo_CustomPostFields_Extend_Waindigo_Library_ControllerPublic_Article extends XFCP_Waindigo_CustomPostFields_Extend_Waindigo_Library_ControllerPublic_Article
{

    /**
     *
     * @see Waindigo_Library_ControllerPublic_Article::actionIndex()
     */
    public function actionIndex()
    {
        $response = parent::actionIndex();

        if ($response instanceof XenForo_ControllerResponse_View) {
            $nodeId = $response->params['library']['node_id'];

            $fieldValues = array();
            if (isset($response->params['article']['custom_post_fields']) &&
                 $response->params['article']['custom_post_fields']) {
                $fieldValues = unserialize($response->params['article']['custom_post_fields']);
            }

            $response->params['customPostFields'] = $this->_getPostFieldModel()->prepareGroupedPostFields(
                $this->_getPostFieldModel()
                    ->getUsablePostFieldsInForums(array(
                    $nodeId
                )), true, $fieldValues, false,
                ($response->params['library']['required_post_fields'] ? unserialize(
                    $response->params['library']['required_post_fields']) : array()));
        }

        return $response;
    } /* END actionIndex */

    /**
     *
     * @see Waindigo_Library_ControllerPublic_Article::actionEdit()
     */
    public function actionEdit()
    {
        $response = parent::actionEdit();

        if ($response instanceof XenForo_ControllerResponse_View) {
            $nodeId = $response->params['article']['node_id'];

            $fieldValues = array();
            if (isset($response->params['article']['custom_post_fields']) &&
                 $response->params['article']['custom_post_fields']) {
                $fieldValues = unserialize($response->params['article']['custom_post_fields']);
            }

            $response->params['customPostFields'] = $this->_getPostFieldModel()->prepareGroupedPostFields(
                $this->_getPostFieldModel()
                    ->getUsablePostFieldsInForums(array(
                    $nodeId
                )), true, $fieldValues, false,
                ($response->params['library']['required_post_fields'] ? unserialize(
                    $response->params['library']['required_post_fields']) : array()));
        }

        return $response;
    } /* END actionEdit */

    /**
     *
     * @see Waindigo_Library_ControllerPublic_Article::actionCreatePage()
     */
    public function actionCreatePage()
    {
        $response = parent::actionCreatePage();

        if ($response instanceof XenForo_ControllerResponse_View) {
            $nodeId = $response->params['library']['node_id'];

            $fieldValues = array();
            if (isset($response->params['library']['custom_post_fields']) &&
                 $response->params['library']['custom_post_fields']) {
                $fieldValues = unserialize($response->params['library']['custom_post_fields']);
            }

            $response->params['customPostFields'] = $this->_getPostFieldModel()->prepareGroupedPostFields(
                $this->_getPostFieldModel()
                    ->getUsablePostFieldsInForums(array(
                    $nodeId
                )), true, $fieldValues, false,
                ($response->params['library']['required_post_fields'] ? unserialize(
                    $response->params['library']['required_post_fields']) : array()));
        }

        return $response;
    } /* END actionCreatePage */

    /**
     *
     * @see Waindigo_Library_ControllerPublic_Article::actionAddPage()
     */
    public function actionAddPage()
    {
        $GLOBALS['Waindigo_Library_ControllerPublic_Article'] = $this;

        return parent::actionAddPage();
    } /* END actionAddPage */

    /**
     *
     * @see Waindigo_Library_ControllerPublic_Article::actionValidateField()
     */
    public function actionValidateField()
    {
        $this->_assertPostOnly();

        $field = $this->_getFieldValidationInputParams();

        if (preg_match('/^custom_post_field_([a-zA-Z0-9_]+)$/', $field['name'], $match)) {
            $writer = XenForo_DataWriter::create('Waindigo_Library_DataWriter_ArticlePage');

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