<?php

/**
 *
 * @see Waindigo_Library_ControllerPublic_ArticlePage
 */
class Waindigo_CustomPostFields_Extend_Waindigo_Library_ControllerPublic_ArticlePage extends XFCP_Waindigo_CustomPostFields_Extend_Waindigo_Library_ControllerPublic_ArticlePage
{

    /**
     *
     * @see Waindigo_Library_ControllerPublic_ArticlePage::actionEdit
     */
    public function actionEdit()
    {
        $response = parent::actionEdit();

        if ($response instanceof XenForo_ControllerResponse_View) {
            $nodeId = $response->params['library']['node_id'];

            $nodeRequiredFields = array();
            if ($response->params['library']['required_post_fields'])
                $nodeRequiredFields = unserialize($response->params['library']['required_post_fields']);

            $fieldValues = array();
            if (isset($response->params['articlePage']['custom_post_fields']) &&
            $response->params['articlePage']['custom_post_fields']) {
                $fieldValues = unserialize($response->params['articlePage']['custom_post_fields']);
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
     * @see Waindigo_Library_ControllerPublic_ArticlePage::actionSave
     */
    public function actionSave()
    {
        $GLOBALS['Waindigo_Library_ControllerPublic_ArticlePage'] = $this;

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