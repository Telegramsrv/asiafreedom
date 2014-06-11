<?php

/**
 *
 * @see XenResource_ControllerPublic_Resource
 */
class Waindigo_CustomFields_Extend_XenResource_ControllerPublic_Resource_Base extends XFCP_Waindigo_CustomFields_Extend_XenResource_ControllerPublic_Resource
{
}

$rmVersion = 0;
if (XenForo_Application::$versionId >= 1020000) {
    $addOns = XenForo_Application::get('addOns');
    if (isset($addOns['XenResource'])) {
        $rmVersion = $addOns['XenResource'] >= 1010000;
    }
}

if ($rmVersion < 1010000) {

    class Waindigo_CustomFields_Extend_XenResource_ControllerPublic_Resource extends Waindigo_CustomFields_Extend_XenResource_ControllerPublic_Resource_Base
    {

        /**
         *
         * @see XenResource_ControllerPublic_Resource::getResourceViewWrapper()
         */
        protected function _getResourceViewWrapper($selectedTab, array $resource, array $category,
            XenForo_ControllerResponse_View $subView)
        {
            /* @var $response XenForo_ControllerResponse_View */
            $response = parent::_getResourceViewWrapper($selectedTab, $resource, $category, $subView);

            if ($response instanceof XenForo_ControllerResponse_View) {
                $resource = $response->params['resource'];

                $fieldModel = $this->_getFieldModel();
                $customFields = $fieldModel->prepareResourceFields(
                    $fieldModel->getResourceFields(
                        array(
                            'informationView' => true
                        ),
                        array(
                            'valueResourceId' => $resource['resource_id']
                        )));

                $customFieldsGrouped = $fieldModel->groupResourceFields($customFields);

                $response->params['customFieldsGrouped'] = $customFieldsGrouped;
            }

            return $response;
        } /* END _getResourceViewWrapper */

        /**
         *
         * @see XenResource_ControllerPublic_Resource::_getResourceAddOrEditResponse()
         */
        protected function _getResourceAddOrEditResponse(array $resource, array $category, array $attachments = array())
        {
            $response = parent::_getResourceAddOrEditResponse($resource, $category, $attachments);

            if ($response instanceof XenForo_ControllerResponse_View) {
                $categoryId = $response->params['category']['resource_category_id'];

                $fieldValues = array();
                if (isset($response->params['resource']['custom_resource_fields']) &&
                     $response->params['resource']['custom_resource_fields']) {
                    $fieldValues = unserialize($response->params['resource']['custom_resource_fields']);
                } elseif (isset($response->params['category']['category_resource_fields']) &&
                     $response->params['category']['category_resource_fields']) {
                    $fieldValues = unserialize($response->params['category']['category_resource_fields']);
                }

                $response->params['customFields'] = $this->_getFieldModel()->prepareGroupedResourceFields(
                    $this->_getFieldModel()
                        ->getUsableResourceFieldsInCategories(
                        array(
                            $categoryId
                        )), true, $fieldValues, false,
                    ($response->params['category']['required_fields'] ? unserialize(
                        $response->params['category']['required_fields']) : array()));
            }

            return $response;
        } /* END _getResourceAddOrEditResponse */

        /**
         *
         * @see XenResource_ControllerPublic_Resource::actionSave()
         */
        public function actionSave()
        {
            $GLOBALS['XenResource_ControllerPublic_Resource'] = $this;

            return parent::actionSave();
        } /* END actionSave */

        /**
         *
         * @return Waindigo_CustomFields_Model_ResourceField
         */
        protected function _getFieldModel()
        {
            return $this->getModelFromCache('Waindigo_CustomFields_Model_ResourceField');
        } /* END _getFieldModel */
    }
} else {

    class Waindigo_CustomFields_Extend_XenResource_ControllerPublic_Resource extends Waindigo_CustomFields_Extend_XenResource_ControllerPublic_Resource_Base
    {

        /**
         *
         * @see XenResource_ControllerPublic_Resource::getResourceViewWrapper()
         */
        protected function _getResourceViewWrapper($selectedTab, array $resource, array $category,
            XenForo_ControllerResponse_View $subView)
        {
            $response = parent::_getResourceViewWrapper($selectedTab, $resource, $category, $subView);

            if ($response instanceof XenForo_ControllerResponse_View) {
                $resource = $response->params['resource'];

                $fieldModel = $this->getModelFromCache('Waindigo_CustomFields_Model_ResourceField');
                $customFields = $fieldModel->prepareResourceFields(
                    $fieldModel->getResourceFields(
                        array(
                            'informationView' => true
                        ),
                        array(
                            'valueResourceId' => $resource['resource_id']
                        )));

                $customFieldsGrouped = $fieldModel->groupResourceFields($customFields);

                $response->params['customFieldsGrouped'] = $customFieldsGrouped;
            }

            return $response;
        } /* END _getResourceViewWrapper */
    }
}