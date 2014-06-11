<?php

/**
 *
 * @see XenForo_ControllerPublic_Post
 */
class Waindigo_CustomPostFields_Extend_XenForo_ControllerPublic_Post extends XFCP_Waindigo_CustomPostFields_Extend_XenForo_ControllerPublic_Post
{

    /**
     *
     * @see XenForo_ControllerPublic_Post::actionEdit()
     */
    public function actionEdit()
    {
        $response = parent::actionEdit();

        if ($response instanceof XenForo_ControllerResponse_View) {
            $nodeId = $response->params['forum']['node_id'];

            $nodeRequiredFields = array();
            if ($response->params['forum']['required_post_fields'])
                $nodeRequiredFields = unserialize($response->params['forum']['required_post_fields']);

            $fieldValues = array();
            if (isset($response->params['post']['custom_post_fields']) && $response->params['post']['custom_post_fields']) {
                $fieldValues = unserialize($response->params['post']['custom_post_fields']);
            }

            $response->params['customPostFields'] = $this->_getPostFieldModel()->prepareGroupedPostFields(
                $this->_getPostFieldModel()
                    ->getUsablePostFieldsInForums(array(
                    $nodeId
                )), true, $fieldValues, true, $nodeRequiredFields, $response->params);
        }

        return $response;
    } /* END actionEdit */

    /**
     *
     * @see XenForo_ControllerPublic_Post::actionSave()
     */
    public function actionSave()
    {
        $GLOBALS['XenForo_ControllerPublic_Post'] = $this;

        return parent::actionSave();
    } /* END actionSave */

    /**
     *
     * @return Waindigo_CustomPostFields_Model_PostField
     */
    protected function _getPostFieldModel()
    {
        return $this->getModelFromCache('Waindigo_CustomPostFields_Model_PostField');
    } /* END _getPostFieldModel */
}