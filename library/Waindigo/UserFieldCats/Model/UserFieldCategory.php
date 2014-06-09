<?php

/**
 * Model for user field categories.
 */
class Waindigo_UserFieldCats_Model_UserFieldCategory extends XenForo_Model
{
    /**
     * Gets user field categories that match the specified criteria.
     *
     * @param array $conditions List of conditions.
     * @param array $fetchOptions
     *
     * @return array [user field category id] => info.
     */
    public function getUserFieldCategories(array $conditions = array(), array $fetchOptions = array())
    {
        $whereClause = $this->prepareUserFieldCategoryConditions($conditions, $fetchOptions);

        $sqlClauses = $this->prepareUserFieldCategoryFetchOptions($fetchOptions);
        $limitOptions = $this->prepareLimitFetchOptions($fetchOptions);

        return $this->fetchAllKeyed($this->limitQueryResults('
                SELECT user_field_category.*
                    ' . $sqlClauses['selectFields'] . '
                FROM xf_user_field_category AS user_field_category
                ' . $sqlClauses['joinTables'] . '
                WHERE ' . $whereClause . '
                ' . $sqlClauses['orderClause'] . '
            ', $limitOptions['limit'], $limitOptions['offset']
        ), 'user_field_category_id');
    } /* END getUserFieldCategories */

    /**
     * Gets the user field category that matches the specified criteria.
     *
     * @param array $conditions List of conditions.
     * @param array $fetchOptions Options that affect what is fetched.
     *
     * @return array|false
     */
    public function getUserFieldCategory(array $conditions = array(), array $fetchOptions = array())
    {
        $userFieldCategories = $this->getUserFieldCategories($conditions, $fetchOptions);

        return reset($userFieldCategories);
    } /* END getUserFieldCategory */

    /**
     * Gets a user field category by ID.
     *
     * @param integer $userFieldCategoryId
     * @param array $fetchOptions Options that affect what is fetched.
     *
     * @return array|false
     */
    public function getUserFieldCategoryById($userFieldCategoryId, array $fetchOptions = array())
    {
        $conditions = array('user_field_category_id' => $userFieldCategoryId);

        return $this->getUserFieldCategory($conditions, $fetchOptions);
    } /* END getUserFieldCategoryById */

    /**
     *
     * @param array $user
     *
     * @return array
     */
    public function getViewableUserFieldCategories(array $user) {
        $userFieldCategories = $this->getUserFieldCategories();

        if ($userFieldCategories) {
            $userModel = $this->getModelFromCache('XenForo_Model_User');

            foreach ($userFieldCategories as $userFieldCategoryId => $userFieldCategory) {
                if ($userFieldCategory['user_group_ids']) {
                    $userGroupIds = explode(',', $userFieldCategory['user_group_ids']);
                    if (!$userModel->isMemberOfUserGroup($user, $userGroupIds)) {
                        unset($userFieldCategories[$userFieldCategoryId]);
                    }
                }
            }
        }

        if (!$userFieldCategories) {
            return false;
        }

        return $userFieldCategories;
    } /* END getViewableUserFieldCategories */

    /**
     * Gets the total number of a user field category that match the specified criteria.
     *
     * @param array $conditions List of conditions.
     *
     * @return integer
     */
    public function countUserFieldCategories(array $conditions = array())
    {
        $fetchOptions = array();

        $whereClause = $this->prepareUserFieldCategoryConditions($conditions, $fetchOptions);
        $joinOptions = $this->prepareUserFieldCategoryFetchOptions($fetchOptions);

        $limitOptions = $this->prepareLimitFetchOptions($fetchOptions);

        return $this->_getDb()->fetchOne('
            SELECT COUNT(*)
            FROM xf_user_field_category AS user_field_category
            ' . $joinOptions['joinTables'] . '
            WHERE ' . $whereClause . '
        ');
    } /* END countUserFieldCategories */

    /**
     * Gets all user field categories titles.
     *
     * @return array [user field category id] => title.
     */
    public static function getUserFieldCategoryTitles()
    {
        $userFieldCategories = XenForo_Model::create(__CLASS__)->getUserFieldCategories();
        $titles = array();
        foreach ($userFieldCategories as $userFieldCategoryId => $userFieldCategory) {
            $titles[$userFieldCategoryId] = $userFieldCategory['title'];
        }
        return $titles;
    } /* END getUserFieldCategoryTitles */

    /**
     * Gets the default user field category record.
     *
     * @return array
     */
    public function getDefaultUserFieldCategory()
    {
        return array(
            'user_field_category_id' => '', /* END 'user_field_category_id' */
        );
    } /* END getDefaultUserFieldCategory */

    /**
     * Prepares a set of conditions to select user field categories against.
     *
     * @param array $conditions List of conditions.
     * @param array $fetchOptions The fetch options that have been provided. May be edited if criteria requires.
     *
     * @return string Criteria as SQL for where clause
     */
    public function prepareUserFieldCategoryConditions(array $conditions, array &$fetchOptions)
    {
        $db = $this->_getDb();
        $sqlConditions = array();

        if (isset($conditions['user_field_category_ids']) && !empty($conditions['user_field_category_ids'])) {
            $sqlConditions[] = 'user_field_category.user_field_category_id IN (' . $db->quote($conditions['user_field_category_ids']) . ')';
        } else if (isset($conditions['user_field_category_id'])) {
            $sqlConditions[] = 'user_field_category.user_field_category_id = ' . $db->quote($conditions['user_field_category_id']);
        }

        $this->_prepareUserFieldCategoryConditions($conditions, $fetchOptions, $sqlConditions);

        return $this->getConditionsForClause($sqlConditions);
    } /* END prepareUserFieldCategoryConditions */

    /**
     * Method designed to be overridden by child classes to add to set of conditions.
     *
     * @param array $conditions List of conditions.
     * @param array $fetchOptions The fetch options that have been provided. May be edited if criteria requires.
     * @param array $sqlConditions List of conditions as SQL snippets. May be edited if criteria requires.
     */
    protected function _prepareUserFieldCategoryConditions(array $conditions, array &$fetchOptions, array &$sqlConditions)
    {
    } /* END _prepareUserFieldCategoryConditions */

    /**
     * Checks the 'join' key of the incoming array for the presence of the FETCH_x bitfields in this class
     * and returns SQL snippets to join the specified tables if required.
     *
     * @param array $fetchOptions containing a 'join' integer key built from this class's FETCH_x bitfields.
     *
     * @return string containing selectFields, joinTables, orderClause keys.
     *          Example: selectFields = ', user.*, foo.title'; joinTables = ' INNER JOIN foo ON (foo.id = other.id) '; orderClause = 'ORDER BY x.y'
     */
    public function prepareUserFieldCategoryFetchOptions(array &$fetchOptions)
    {
        $selectFields = '';
        $joinTables = '';
        $orderBy = '';

        $this->_prepareUserFieldCategoryFetchOptions($fetchOptions, $selectFields, $joinTables, $orderBy);

        return array(
            'selectFields' => $selectFields,
            'joinTables'   => $joinTables,
            'orderClause'  => ($orderBy ? "ORDER BY $orderBy" : '')
        );
    } /* END prepareUserFieldCategoryFetchOptions */

    /**
     * Method designed to be overridden by child classes to add to SQL snippets.
     *
     * @param array $fetchOptions containing a 'join' integer key built from this class's FETCH_x bitfields.
     * @param string $selectFields = ', user.*, foo.title'
     * @param string $joinTables = ' INNER JOIN foo ON (foo.id = other.id) '
     * @param string $orderBy = 'x.y ASC, x.z DESC'
     */
    protected function _prepareUserFieldCategoryFetchOptions(array &$fetchOptions, &$selectFields, &$joinTables, &$orderBy)
    {
    } /* END _prepareUserFieldCategoryFetchOptions */
}