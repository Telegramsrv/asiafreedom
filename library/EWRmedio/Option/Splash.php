<?php

class EWRmedio_Option_Splash
{
	public static function renderOption(XenForo_View $view, $fieldPrefix, array $preparedOption, $canEdit)
	{
		$values = $preparedOption['option_value'];

		$choices = array();
		if (!empty($values))
		{
			foreach ($values AS $value)
			{
				$choices[] = $value;
			}
		}

		$editLink = $view->createTemplateObject('option_list_option_editlink', array(
			'preparedOption' => $preparedOption,
			'canEditOptionDefinition' => $canEdit
		));

		return $view->createTemplateObject('option_template_splash_EWRmedio', array(
			'fieldPrefix' => $fieldPrefix,
			'listedFieldName' => $fieldPrefix . '_listed[]',
			'preparedOption' => $preparedOption,
			'formatParams' => $preparedOption['formatParams'],
			'editLink' => $editLink,
			'choices' => $choices,
			'nextCounter' => count($choices)
		));
	}

	public static function verifyOption(array &$options, XenForo_DataWriter $dw, $fieldName)
	{
		foreach ($options AS $key => &$option)
		{
			if (empty($option['type']) || empty($option['sort']) || empty($option['count']))
			{
				unset($options[$key]);
			}
		}

		return true;
	}
}