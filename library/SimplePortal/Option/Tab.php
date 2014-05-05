<?php

class SimplePortal_Option_Tab
{

    static $navbarPositions = array(
        'home',
        'middle',
        'end'
    );


    public static function renderOption(XenForo_View $view, $fieldPrefix, array $preparedOption, $canEdit)
    {
        $editLink = $view->createTemplateObject('option_list_option_editlink', array(
            'preparedOption' => $preparedOption,
            'canEditOptionDefinition' => $canEdit
        ));

        $options = array();
        foreach (self::$navbarPositions AS $pos){
            $options[$pos] = $pos;
        }
        return $view->createTemplateObject('option_list_option_select', array(
            'fieldPrefix' => $fieldPrefix,
            'listedFieldName' => $fieldPrefix . '_listed[]',
            'preparedOption' => $preparedOption,
            'formatParams' => $options,
            'editLink' => $editLink
        ));
    }


    public static function validateOption(&$choices, XenForo_DataWriter $dw, $fieldName){
        if ($dw->isInsert())
        {
            return true;
        }


       if (!in_array($choices,self::$navbarPositions)){
           $dw->error(new XenForo_Phrase('invalid_argument'), $fieldName);
           return false;
       }
        return true;


    }

}