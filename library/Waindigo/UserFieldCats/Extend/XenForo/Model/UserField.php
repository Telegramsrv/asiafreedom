<?php

/**
 *
 * @see XenForo_Model_UserField
 */
class Waindigo_UserFieldCats_Extend_XenForo_Model_UserField extends XFCP_Waindigo_UserFieldCats_Extend_XenForo_Model_UserField
{

    /**
     *
     * @see XenForo_Model_UserField::prepareUserFieldConditions()
     */
    public function prepareUserFieldConditions(array $conditions, array &$fetchOptions)
    {
        $db = $this->_getDb();
        $sqlConditions[] = parent::prepareUserFieldConditions($conditions, $fetchOptions);

        if (!empty($conditions['user_field_category_id'])) {
            $sqlConditions[] = 'user_field.user_field_category_id = ' . $db->quote(
                $conditions['user_field_category_id']);
        }

        return $this->getConditionsForClause($sqlConditions);
    } /* END prepareUserFieldConditions */

    /**
     *
     * @see XenForo_Model_UserField::getUserFieldGroups()
     */
    public function getUserFieldGroups()
    {
        $userFieldGroups = parent::getUserFieldGroups();

        $userFieldGroups['custom'] = array(
            'value' => 'custom',
            'label' => new XenForo_Phrase('waindigo_custom_userfieldcats')
        );

        return $userFieldGroups;
    } /* END getUserFieldGroups */
}