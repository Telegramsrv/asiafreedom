<?php

/**
 *
 * @see XenForo_DataWriter_UserField
 */
class Waindigo_UserFieldCats_Extend_XenForo_DataWriter_UserField extends XFCP_Waindigo_UserFieldCats_Extend_XenForo_DataWriter_UserField
{

    /**
     *
     * @see XenForo_DataWriter_UserField::_getFields()
     */
    protected function _getFields()
    {
        $fields = parent::_getFields();

        $fields['xf_user_field']['display_group']['allowedValues'][] = 'custom';

        $fields['xf_user_field']['user_field_category_id'] = array(
            'type' => self::TYPE_UINT,
            'default' => 0
        );

        return $fields;
    } /* END _getFields */

    /**
     * Pre-save handling.
     */
    protected function _preSave()
    {
        parent::_preSave();

        if (isset($GLOBALS['XenForo_ControllerAdmin_UserField'])) {
            /* @var $controller XenForo_ControllerAdmin_UserField */
            $controller = $GLOBALS['XenForo_ControllerAdmin_UserField'];

            if ($this->get('display_group') == 'custom') {
                $userFieldCategoryId = $controller->getInput()->filterSingle('user_field_category_id',
                    XenForo_Input::UINT);

                if (!$userFieldCategoryId) {
                    $this->error(new XenForo_Phrase('waindigo_please_select_valid_user_field_category_userfieldcats'),
                        'user_field_category_id');
                } else {
                    $this->set('user_field_category_id', $userFieldCategoryId);
                }
            } else {
                $this->set('user_field_category_id', 0);
            }
        }
    } /* END _preSave */
}