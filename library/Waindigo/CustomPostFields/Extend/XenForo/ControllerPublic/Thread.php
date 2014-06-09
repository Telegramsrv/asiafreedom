<?php

/**
 *
 * @see XenForo_ControllerPublic_Thread
 */
class Waindigo_CustomPostFields_Extend_XenForo_ControllerPublic_Thread extends XFCP_Waindigo_CustomPostFields_Extend_XenForo_ControllerPublic_Thread
{

    /**
     *
     * @see XenForo_ControllerPublic_Thread::actionIndex()
     */
    public function actionIndex()
    {
        $response = parent::actionIndex();

        if ($response instanceof XenForo_ControllerResponse_View) {
            $nodeId = $response->params['forum']['node_id'];

            $fieldValues = array();
            if (isset($response->params['forum']['custom_post_fields']) &&
                 $response->params['forum']['custom_post_fields']) {
                $fieldValues = unserialize($response->params['forum']['custom_post_fields']);
            }

            $response->params['customPostFields'] = $this->_getPostFieldModel()->prepareGroupedPostFields(
                $this->_getPostFieldModel()
                    ->getUsablePostFieldsInForums(array(
                    $nodeId
                )), true, $fieldValues, false,
                ($response->params['forum']['required_post_fields'] ? unserialize(
                    $response->params['forum']['required_post_fields']) : array()));
        }

        return $response;
    } /* END actionIndex */

    /**
     *
     * @see XenForo_ControllerPublic_Thread::actionEdit()
     */
    public function actionEdit()
    {
        $response = parent::actionEdit();

        if ($response instanceof XenForo_ControllerResponse_View) {
            $nodeId = $response->params['thread']['node_id'];

            $fieldValues = array();
            if (isset($response->params['thread']['custom_post_fields']) &&
                 $response->params['thread']['custom_post_fields']) {
                $fieldValues = unserialize($response->params['thread']['custom_post_fields']);
            }

            $response->params['customPostFields'] = $this->_getPostFieldModel()->prepareGroupedPostFields(
                $this->_getPostFieldModel()
                    ->getUsablePostFieldsInForums(array(
                    $nodeId
                )), true, $fieldValues, false,
                ($response->params['forum']['required_post_fields'] ? unserialize(
                    $response->params['forum']['required_post_fields']) : array()));
        }

        return $response;
    } /* END actionEdit */

    /**
     *
     * @see XenForo_ControllerPublic_Thread::actionReply()
     */
    public function actionReply()
    {
        $response = parent::actionReply();

        if ($response instanceof XenForo_ControllerResponse_View) {
            $nodeId = $response->params['forum']['node_id'];

            $fieldValues = array();
            if (isset($response->params['forum']['custom_post_fields']) &&
                 $response->params['forum']['custom_post_fields']) {
                $fieldValues = unserialize($response->params['forum']['custom_post_fields']);
            }

            $response->params['customPostFields'] = $this->_getPostFieldModel()->prepareGroupedPostFields(
                $this->_getPostFieldModel()
                    ->getUsablePostFieldsInForums(array(
                    $nodeId
                )), true, $fieldValues, false,
                ($response->params['forum']['required_post_fields'] ? unserialize(
                    $response->params['forum']['required_post_fields']) : array()));
        }

        return $response;
    } /* END actionReply */

    /**
     *
     * @see XenForo_ControllerPublic_Thread::actionAddReply()
     */
    public function actionAddReply()
    {
        $GLOBALS['XenForo_ControllerPublic_Thread'] = $this;

        return parent::actionAddReply();
    } /* END actionAddReply */

    /**
     *
     * @see XenForo_ControllerPublic_Thread::actionValidateField()
     */
    public function actionValidateField()
    {
        $this->_assertPostOnly();

        $field = $this->_getFieldValidationInputParams();

        if (preg_match('/^custom_post_field_([a-zA-Z0-9_]+)$/', $field['name'], $match)) {
            $writer = XenForo_DataWriter::create('Waindigo_CustomFields_DataWriter_Post');

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