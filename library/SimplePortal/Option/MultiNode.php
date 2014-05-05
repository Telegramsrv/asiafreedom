<?php

class SimplePortal_Option_MultiNode{

    /**
     *
     * @deprecated ! do we really need this?:D
     *
     * @param XenForo_View $view
     * @param $fieldPrefix
     * @param array $preparedOption
     * @param $canEdit
     * @return XenForo_Template_Abstract
     */
    public static function renderOption(XenForo_View $view, $fieldPrefix, array $preparedOption, $canEdit)
    {
        $editLink = $view->createTemplateObject('option_list_option_editlink', array(
            'preparedOption' => $preparedOption,
            'canEditOptionDefinition' => $canEdit
        ));

        /* @var $nodeModel XenForo_Model_Node */
        $nodeModel = XenForo_Model::create('XenForo_Model_Node');

        $forumOptions = $nodeModel->getNodeOptionsArray($nodeModel->getAllNodes(), $preparedOption['option_value'], '(unspecified)');

        return $view->createTemplateObject('option_list_option_select', array(
            'fieldPrefix' => $fieldPrefix,
            'listedFieldName' => $fieldPrefix . '_listed[]',
            'preparedOption' => $preparedOption,
            'formatParams' => $forumOptions,
            'editLink' => $editLink
        ));
    }


    public static function renderMultiple(XenForo_View $view, $fieldPrefix, array $preparedOption, $canEdit)
    {

        $editLink = $view->createTemplateObject('option_list_option_editlink', array(
            'preparedOption' => $preparedOption,
            'canEditOptionDefinition' => $canEdit
        ));

        /* @var $nodeModel XenForo_Model_Node */
        $nodeModel = XenForo_Model::create('XenForo_Model_Node');

        $forumOptions = $nodeModel->getNodeOptionsArray($nodeModel->getAllNodes(), $preparedOption['option_value'], '(unspecified)');

        return $view->createTemplateObject('el_portal_nodeoptions', array(
            'fieldPrefix' => $fieldPrefix,
            'listedFieldName' => $fieldPrefix . '_listed[]',
            'preparedOption' => $preparedOption,
            'formatParams' => $forumOptions,
            'editLink' => $editLink
        ));
    }
}