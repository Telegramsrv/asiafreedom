<?php

/**
 * Model for custom post fields.
 */
class Waindigo_CustomPostFields_Model_PostField extends XenForo_Model
{

    const FETCH_FORUM_FIELD = 0x01;

    const FETCH_FIELD_GROUP = 0x02;

    const FETCH_ADDON = 0x04;

    /**
     * Gets a custom post field by ID.
     *
     * @param string $fieldId
     *
     * @return array false
     */
    public function getPostFieldById($fieldId)
    {
        if (!$fieldId) {
            return array();
        }

        return $this->_getDb()->fetchRow(
            '
            SELECT *
            FROM xf_post_field
            WHERE field_id = ?
        ', $fieldId);
    } /* END getPostFieldById */

    /**
     * Gets custom post fields that match the specified criteria.
     *
     * @param array $conditions
     * @param array $fetchOptions
     *
     * @return array [field id] => info
     */
    public function getPostFields(array $conditions = array(), array $fetchOptions = array())
    {
        $whereConditions = $this->preparePostFieldConditions($conditions, $fetchOptions);

        $orderClause = $this->preparePostFieldOrderOptions($fetchOptions, 'field.materialized_order');
        $joinOptions = $this->preparePostFieldFetchOptions($fetchOptions);
        $limitOptions = $this->prepareLimitFetchOptions($fetchOptions);

        $fetchAll = (!empty($fetchOptions['join']) && ($fetchOptions['join'] & self::FETCH_FORUM_FIELD));

        $query = $this->limitQueryResults(
            '
            SELECT field.*
            ' . $joinOptions['selectFields'] . '
            FROM xf_post_field AS field
            ' . $joinOptions['joinTables'] . '
            WHERE ' . $whereConditions . '
            ' . $orderClause . '
            ', $limitOptions['limit'], $limitOptions['offset']);

        return ($fetchAll ? $this->_getDb()->fetchAll($query) : $this->fetchAllKeyed($query, 'field_id'));
    } /* END getPostFields */

    /**
     * Prepares a set of conditions to select fields against.
     *
     * @param array $conditions List of conditions.
     * @param array $fetchOptions The fetch options that have been provided. May
     * be edited if criteria requires.
     *
     * @return string Criteria as SQL for where clause
     */
    public function preparePostFieldConditions(array $conditions, array &$fetchOptions)
    {
        $db = $this->_getDb();
        $sqlConditions = array();

        if (isset($conditions['field_ids'])) {
            $sqlConditions[] = 'field.field_id IN(' . $db->quote($conditions['field_ids']) . ')';
        }

        if (!empty($conditions['field_group_id'])) {
            $sqlConditions[] = 'field.field_group_id = ' . $db->quote($conditions['field_group_id']);
        }

        if (!empty($conditions['field_choices_class_id'])) {
            $sqlConditions[] = 'field.field_choices_class_id = ' . $db->quote($conditions['field_choices_class_id']);
        }

        if (!empty($conditions['addon_id'])) {
            $sqlConditions[] = 'field.addon_id = ' . $db->quote($conditions['addon_id']);
        }

        if (!empty($conditions['active'])) {
            $sqlConditions[] = 'addon.active = 1 OR field.addon_id = \'\'';
            $this->addFetchOptionJoin($fetchOptions, self::FETCH_ADDON);
        }

        if (!empty($conditions['adminQuickSearch'])) {
            $searchStringSql = 'field.field_id LIKE ' .
                 XenForo_Db::quoteLike($conditions['adminQuickSearch']['searchText'], 'lr');

            if (!empty($conditions['adminQuickSearch']['phraseMatches'])) {
                $sqlConditions[] = '(' . $searchStringSql . ' OR field.field_id IN (' .
                     $db->quote($conditions['adminQuickSearch']['phraseMatches']) . '))';
            } else {
                $sqlConditions[] = $searchStringSql;
            }
        }

        if (isset($conditions['node_id'])) {
            if (is_array($conditions['node_id'])) {
                $sqlConditions[] = 'ff.node_id IN(' . $db->quote($conditions['node_id']) . ')';
            } else {
                $sqlConditions[] = 'ff.node_id = ' . $db->quote($conditions['node_id']);
            }
            $this->addFetchOptionJoin($fetchOptions, self::FETCH_FORUM_FIELD);
        }

        if (isset($conditions['node_ids'])) {
            $sqlConditions[] = 'ff.node_id IN(' . $db->quote($conditions['node_ids']) . ')';
            $this->addFetchOptionJoin($fetchOptions, self::FETCH_FORUM_FIELD);
        }

        return $this->getConditionsForClause($sqlConditions);
    } /* END preparePostFieldConditions */

    /**
     * Prepares join-related fetch options.
     *
     * @param array $fetchOptions
     *
     * @return array Containing 'selectFields' and 'joinTables' keys.
     */
    public function preparePostFieldFetchOptions(array $fetchOptions)
    {
        $selectFields = '';
        $joinTables = '';

        $db = $this->_getDb();

        if (!empty($fetchOptions['valuePostId'])) {
            $selectFields .= ',
                field_value.field_value';
            $joinTables .= '
                LEFT JOIN xf_post_field_value AS field_value ON
                (field_value.field_id = field.field_id AND field_value.post_id = ' .
                 $db->quote($fetchOptions['valuePostId']) . ')';
        }

        if (!empty($fetchOptions['valueArticlePageId'])) {
            $selectFields .= ',
                field_value.field_value';
            $joinTables .= '
                LEFT JOIN xf_article_page_field_value AS field_value ON
                (field_value.field_id = field.field_id AND field_value.article_page_id = ' .
                 $db->quote($fetchOptions['valueArticlePageId']) . ')';
        }

        if (!empty($fetchOptions['join'])) {
            if ($fetchOptions['join'] & self::FETCH_FORUM_FIELD) {
                $selectFields .= ',
                    ff.field_id, ff.node_id';
                $joinTables .= '
                    INNER JOIN xf_forum_post_field AS ff ON
                    (ff.field_id = field.field_id)';
            }

            if ($fetchOptions['join'] & self::FETCH_FIELD_GROUP) {
                $selectFields .= ',
                    field_group.display_order AS group_display_order';
                $joinTables .= '
                    LEFT JOIN xf_post_field_group AS field_group ON
                    (field_group.field_group_id = field.field_group_id)';
            }

            if ($fetchOptions['join'] & self::FETCH_ADDON) {
                $selectFields .= ',
                    addon.title AS addon_title, addon.active';
                $joinTables .= '
                    LEFT JOIN xf_addon AS addon ON
                    (field.addon_id = addon.addon_id)';
            }
        }

        return array(
            'selectFields' => $selectFields,
            'joinTables' => $joinTables
        );
    } /* END preparePostFieldFetchOptions */

    /**
     * Construct 'ORDER BY' clause
     *
     * @param array $fetchOptions (uses 'order' key)
     * @param string $defaultOrderSql Default order SQL
     *
     * @return string
     */
    public function preparePostFieldOrderOptions(array &$fetchOptions, $defaultOrderSql = '')
    {
        $choices = array(
            'materialized_order' => 'field.materialized_order',
            'canonical_order' => 'field_group.display_order, field.display_order'
        );

        if (!empty($fetchOptions['order']) && $fetchOptions['order'] == 'canonical_order') {
            $this->addFetchOptionJoin($fetchOptions, self::FETCH_FIELD_GROUP);
        }

        return $this->getOrderByClause($choices, $fetchOptions, $defaultOrderSql);
    } /* END preparePostFieldOrderOptions */

    /**
     * Fetches custom post fields in display groups
     *
     * @param array $conditions
     * @param array $fetchOptions
     * @param integer $fieldCount Reference: counts the total number of fields
     *
     * @return [group ID => [title, fields => field]]
     */
    public function getPostFieldsByGroups(array $conditions = array(), array $fetchOptions = array(), &$fieldCount = 0)
    {
        $fields = $this->getPostFields($conditions, $fetchOptions);

        $fieldGroups = array();
        foreach ($fields as $field) {
            $fieldGroups[$field['field_group_id']][$field['field_id']] = $this->preparePostField($field);
        }

        $fieldCount = count($fields);

        return $fieldGroups;
    } /* END getPostFieldsByGroups */

    /**
     * Fetches all custom post fields available in the specified forums
     *
     * @param integer|array $nodeIds
     *
     * @return array
     */
    public function getPostFieldsInForums($nodeId)
    {
        return $this->getPostFields(
            is_array($nodeId) ? array(
                'node_ids' => $nodeId
            ) : array(
                'node_id' => $nodeId
            ));
    } /* END getPostFieldsInForums */

    /**
     * Fetches all custom post fields available in the specified forums
     *
     * @param integer $nodeId
     *
     * @return array
     */
    public function getPostFieldsInForum($nodeId)
    {
        $output = array();
        foreach ($this->getPostFields(array(
            'node_id' => $nodeId
        )) as $field) {
            $output[$field['field_id']] = $field;
        }

        return $output;
    } /* END getPostFieldsInForum */

    /**
     * Fetches all post fields usable by the visiting user in the specified
     * forum(s)
     *
     * @param integer|array $nodeIds
     * @param array|null $viewingUser
     *
     * @return array
     */
    public function getUsablePostFieldsInForums($nodeIds, array $viewingUser = null, $verifyUsability = true)
    {
        $this->standardizeViewingUserReference($viewingUser);

        $fields = $this->getPostFieldsInForums($nodeIds);

        $fieldGroups = array();
        foreach ($fields as $field) {
            if (!$verifyUsability || $this->_verifyPostFieldIsUsableInternal($field, $viewingUser)) {
                $fieldId = $field['field_id'];
                $fieldGroupId = $field['field_group_id'];

                if (!isset($fieldGroups[$fieldGroupId])) {
                    $fieldGroups[$fieldGroupId] = array();

                    if ($fieldGroupId) {
                        $fieldGroups[$fieldGroupId]['title'] = new XenForo_Phrase(
                            $this->getPostFieldGroupTitlePhraseName($fieldGroupId));
                    }
                }

                $fieldGroups[$fieldGroupId]['fields'][$fieldId] = $field;
            }
        }

        return $fieldGroups;
    } /* END getUsablePostFieldsInForums */

    /**
     * Fetches all post fields usable by the visiting user
     *
     * @param array|null $viewingUser
     *
     * @return array
     */
    public function getUsablePostFields(array $viewingUser = null, $verifyUsability = true)
    {
        $this->standardizeViewingUserReference($viewingUser);

        $fields = $this->getPostFields();

        $fieldGroups = array();
        foreach ($fields as $field) {
            if (!$verifyUsability || $this->_verifyPostFieldIsUsableInternal($field, $viewingUser)) {
                $fieldId = $field['field_id'];
                $fieldGroupId = $field['field_group_id'];

                if (!isset($fieldGroups[$fieldGroupId])) {
                    $fieldGroups[$fieldGroupId] = array();

                    if ($fieldGroupId) {
                        $fieldGroups[$fieldGroupId]['title'] = new XenForo_Phrase(
                            $this->getPostFieldGroupTitlePhraseName($fieldGroupId));
                    }
                }

                $fieldGroups[$fieldGroupId]['fields'][$fieldId] = $field;
            }
        }

        return $fieldGroups;
    } /* END getUsablePostFields */

    public function getPostFieldIfInForum($fieldId, $nodeId)
    {
        return $this->_getDb()->fetchRow(
            '
                SELECT field.*
                FROM xf_post_field AS field
                INNER JOIN xf_forum_post_field AS ff ON (ff.field_id = field.field_id AND ff.node_id = ?)
                WHERE field.field_id = ?
            ', array(
                $nodeId,
                $fieldId
            ));
    } /* END getPostFieldIfInForum */

    public function getForumAssociationsByPostField($fieldId, $fetchAll = false)
    {
        $query = '
            SELECT ff.node_id
            ' . ($fetchAll ? ', node.*' : '') . '
            FROM xf_forum_post_field AS ff
            ' . ($fetchAll ? 'LEFT JOIN xf_node AS node ON (ff.node_id = node.node_id)' : '') . '
            WHERE ff.field_id = ' . $this->_getDb()->quote($fieldId) . '
        ';

        return ($fetchAll ? $this->fetchAllKeyed($query, 'node_id') : $this->_getDb()->fetchCol($query));
    } /* END getForumAssociationsByPostField */

    /**
     * Groups post fields by their field group.
     *
     * @param array $fields
     *
     * @return array [field group id][key] => info
     */
    public function groupPostFields(array $fields)
    {
        $return = array();

        foreach ($fields as $fieldId => $field) {
            $return[$field['field_group_id']][$fieldId] = $field;
        }

        return $return;
    } /* END groupPostFields */

    /**
     * Prepares a post field for display.
     *
     * @param array $field
     * @param boolean $getFieldChoices If true, gets the choice options for this
     * field (as phrases)
     * @param mixed $fieldValue If not null, the value for the field; if null,
     * pulled from field_value
     * @param boolean $valueSaved If true, considers the value passed to be
     * saved; should be false on registration
     *
     * @return array Prepared field
     */
    public function preparePostField(array $field, $getFieldChoices = false, $fieldValue = null, $valueSaved = true,
        $required = false, array $extraData = array())
    {
        $field['isMultiChoice'] = ($field['field_type'] == 'checkbox' || $field['field_type'] == 'multiselect');

        if ($fieldValue === null && isset($field['field_value'])) {
            $fieldValue = $field['field_value'];
        }
        if ($field['isMultiChoice']) {
            if (is_string($fieldValue)) {
                $fieldValue = @unserialize($fieldValue);
            } else
                if (!is_array($fieldValue)) {
                    $fieldValue = array();
                }
        }
        $field['field_value'] = $fieldValue;

        $field['title'] = new XenForo_Phrase($this->getPostFieldTitlePhraseName($field['field_id']));
        $field['description'] = new XenForo_Phrase($this->getPostFieldDescriptionPhraseName($field['field_id']));

        $field['hasValue'] = $valueSaved &&
             ((is_string($fieldValue) && $fieldValue !== '') || (!is_string($fieldValue) && $fieldValue));

        if ($getFieldChoices) {
            if (!empty($field['field_choices_callback_class']) && !empty($field['field_choices_callback_method'])) {
                try {
                    $field['fieldChoices'] = call_user_func(
                        array(
                            $field['field_choices_callback_class'],
                            $field['field_choices_callback_method']
                        ), $field, $extraData);
                } catch (Exception $e) {
                    // do nothing
                }
            } else {
                $field['fieldChoices'] = $this->getPostFieldChoices($field['field_id'], $field['field_choices']);
            }
        }

        $field['isEditable'] = true;

        $field['required'] = $required;

        return $field;
    } /* END preparePostField */

    /**
     * Prepares a list of post fields for display.
     *
     * @param array $fields
     * @param boolean $getFieldChoices If true, gets the choice options for
     * these fields (as phrases)
     * @param array $fieldValues List of values for the specified fields; if
     * skipped, pulled from field_value in array
     * @param boolean $valueSaved If true, considers the value passed to be
     * saved; should be false on registration
     *
     * @return array
     */
    public function preparePostFields(array $fields, $getFieldChoices = false, array $fieldValues = array(), $valueSaved = true,
        array $nodeRequiredFields = array(), array $extraData = array())
    {
        foreach ($fields as &$field) {
            $value = isset($fieldValues[$field['field_id']]) ? $fieldValues[$field['field_id']] : null;
            $required = in_array($field['field_id'], $nodeRequiredFields);
            $field = $this->preparePostField($field, $getFieldChoices, $value, $valueSaved, $required, $extraData);
        }

        return $fields;
    } /* END preparePostFields */

    /**
     * Prepares a list of grouped post fields for display.
     *
     * @param array $fieldGroups
     * @param boolean $getFieldChoices If true, gets the choice options for
     * these fields (as phrases)
     * @param array $fieldValues List of values for the specified fields; if
     * skipped, pulled from field_value in array
     * @param boolean $valueSaved If true, considers the value passed to be
     * saved; should be false on registration
     *
     * @return array
     */
    public function prepareGroupedPostFields(array $fieldGroups, $getFieldChoices = false, array $fieldValues = array(),
        $valueSaved = true, array $nodeRequiredFields = array(), array $extraData = array())
    {
        foreach ($fieldGroups as &$fieldGroup) {
            $fieldGroup['fields'] = $this->preparePostFields($fieldGroup['fields'], $getFieldChoices, $fieldValues,
                $valueSaved, $nodeRequiredFields, $extraData);
        }

        return $fieldGroups;
    } /* END prepareGroupedPostFields */

    public function getPostFieldTitlePhraseName($fieldId)
    {
        return 'post_field_' . $fieldId;
    } /* END getPostFieldTitlePhraseName */

    /**
     * Gets the field choices for the given field.
     *
     * @param string $fieldId
     * @param string|array $choices Serialized string or array of choices; key
     * is choide ID
     * @param boolean $master If true, gets the master phrase values; otherwise,
     * phrases
     *
     * @return array Choices
     */
    public function getPostFieldChoices($fieldId, $choices, $master = false)
    {
        if (!is_array($choices)) {
            $choices = ($choices ? @unserialize($choices) : array());
        }

        if (!$master) {
            foreach ($choices as $value => &$text) {
                $text = new XenForo_Phrase($this->getPostFieldChoicePhraseName($fieldId, $value));
            }
        }

        return $choices;
    } /* END getPostFieldChoices */

    /**
     * Verifies that the value for the specified field is valid.
     *
     * @param array $field
     * @param mixed $value
     * @param mixed $error Returned error message
     *
     * @return boolean
     */
    public function verifyPostFieldValue(array $field, &$value, &$error = '')
    {
        if (($field['field_type'] == 'radio' || $field['field_type'] == 'select' || $field['field_type'] == 'checkbox' ||
             $field['field_type'] == 'multiselect') &&
             (isset($field['field_choices_callback_class']) && $field['field_choices_callback_class']) &&
             (isset($field['field_choices_callback_method']) && $field['field_choices_callback_method'])) {
            $field['field_choices'] = serialize(
                call_user_func(
                    array(
                        $field['field_choices_callback_class'],
                        $field['field_choices_callback_method']
                    )));
        }
        $error = false;

        switch ($field['field_type']) {
            case 'textbox':
                $value = preg_replace('/\r?\n/', ' ', strval($value));
            // break missing intentionally


            case 'textarea':
                $value = trim(strval($value));

                if ($field['max_length'] && utf8_strlen($value) > $field['max_length']) {
                    $error = new XenForo_Phrase('please_enter_value_using_x_characters_or_fewer',
                        array(
                            'count' => $field['max_length']
                        ));
                    return false;
                }

                $matched = true;

                if ($value !== '') {
                    switch ($field['match_type']) {
                        case 'number':
                            $matched = preg_match('/^[0-9]+(\.[0-9]+)?$/', $value);
                            break;

                        case 'alphanumeric':
                            $matched = preg_match('/^[a-z0-9_]+$/i', $value);
                            break;

                        case 'email':
                            $matched = Zend_Validate::is($value, 'EmailAddress');
                            break;

                        case 'url':
                            if ($value === 'http://') {
                                $value = '';
                                break;
                            }
                            if (substr(strtolower($value), 0, 4) == 'www.') {
                                $value = 'http://' . $value;
                            }
                            $matched = Zend_Uri::check($value);
                            break;

                        case 'regex':
                            $matched = preg_match('#' . str_replace('#', '\#', $field['match_regex']) . '#sU', $value);
                            break;

                        case 'callback':
                            $field['custom_field_type'] = 'post';
                            $matched = call_user_func_array(
                                array(
                                    $field['match_callback_class'],
                                    $field['match_callback_method']
                                ),
                                array(
                                    $field,
                                    &$value,
                                    &$error
                                ));

                        default:
                        // no matching
                    }
                }

                if (!$matched) {
                    if (!$error) {
                        $error = new XenForo_Phrase('please_enter_value_that_matches_required_format');
                    }
                    return false;
                }
                break;

            case 'radio':
            case 'select':
                $choices = unserialize($field['field_choices']);
                $value = strval($value);

                if (!isset($choices[$value])) {
                    $value = '';
                }
                break;

            case 'checkbox':
            case 'multiselect':
                $choices = unserialize($field['field_choices']);
                if (!is_array($value)) {
                    $value = array();
                }

                $newValue = array();

                foreach ($value as $key => $choice) {
                    $choice = strval($choice);
                    if (isset($choices[$choice])) {
                        $newValue[$choice] = $choice;
                    }
                }

                $value = $newValue;
                break;
        }

        return true;
    } /* END verifyPostFieldValue */

    public function updatePostFieldForumAssociationByPostField($fieldId, array $nodeIds)
    {
        $emptyNodeKey = array_search(0, $nodeIds);
        if ($emptyNodeKey !== false) {
            unset($nodeIds[$emptyNodeKey]);
        }

        $nodeIds = array_unique($nodeIds);

        $existingNodeIds = $this->getForumAssociationsByPostField($fieldId);
        if (!$nodeIds && !$existingNodeIds) {
            return; // nothing to do
        }

        $db = $this->_getDb();
        XenForo_Db::beginTransaction($db);

        $db->delete('xf_forum_post_field', 'field_id = ' . $db->quote($fieldId));

        foreach ($nodeIds as $nodeId) {
            $db->insert('xf_forum_post_field',
                array(
                    'node_id' => $nodeId,
                    'field_id' => $fieldId,
                    'field_value' => ''
                ));
        }

        $rebuildNodeIds = array_unique(array_merge($nodeIds, $existingNodeIds));
        $this->rebuildPostFieldForumAssociationCache($rebuildNodeIds);

        XenForo_Db::commit($db);
    } /* END updatePostFieldForumAssociationByPostField */

    public function updatePostFieldForumAssociationByForum($nodeId, array $fieldIds)
    {
        $emptyFieldKey = array_search(0, $fieldIds, true);
        if ($emptyFieldKey !== false) {
            unset($fieldIds[$emptyFieldKey]);
        }

        $fieldIds = array_unique($fieldIds);

        $db = $this->_getDb();

        XenForo_Db::beginTransaction($db);

        $db->delete('xf_forum_post_field', 'node_id = ' . $db->quote($nodeId));

        foreach ($fieldIds as $fieldId) {
            $db->insert('xf_forum_post_field',
                array(
                    'node_id' => $nodeId,
                    'field_id' => $fieldId,
                    'field_value' => ''
                ));
        }

        $this->rebuildPostFieldForumAssociationCache($nodeId);

        XenForo_Db::commit($db);
    } /* END updatePostFieldForumAssociationByForum */

    public function rebuildPostFieldForumAssociationCache($nodeIds)
    {
        if (!is_array($nodeIds)) {
            $nodeIds = array(
                $nodeIds
            );
        }
        if (!$nodeIds) {
            return;
        }

        $nodes = $this->_getNodeModel()->getAllNodes();

        $db = $this->_getDb();

        $newCache = array();

        foreach ($this->getPostFieldsInForums($nodeIds) as $field) {
            $fieldGroupId = $field['field_group_id'];
            $newCache[$field['node_id']][$fieldGroupId][$field['field_id']] = $field['field_id'];
        }

        XenForo_Db::beginTransaction($db);

        foreach ($nodeIds as $nodeId) {
            $update = (isset($newCache[$nodeId]) ? serialize($newCache[$nodeId]) : '');
            if (isset($nodes[$nodeId])) {
                if ($nodes[$nodeId]['node_type_id'] == 'Library') {
                    $db->update('xf_library',
                        array(
                            'field_cache' => $update
                        ), 'node_id = ' . $db->quote($nodeId));
                } else {
                    $db->update('xf_forum',
                        array(
                            'field_cache' => $update
                        ), 'node_id = ' . $db->quote($nodeId));
                }
            }
        }

        XenForo_Db::commit($db);
    } /* END rebuildPostFieldForumAssociationCache */

    /**
     * Fetches an array of custom post fields including display group info, for
     * use in <xen:options source />
     *
     * @param array $conditions
     * @param array $fetchOptions
     *
     * @return array
     */
    public function getPostFieldOptions(array $conditions = array(), array $fetchOptions = array())
    {
        $fieldGroups = $this->getPostFieldsByGroups($conditions, $fetchOptions);

        $options = array();

        foreach ($fieldGroups as $fieldGroupId => $fields) {
            if ($fields) {
                if ($fieldGroupId) {
                    $groupTitle = new XenForo_Phrase($this->getPostFieldGroupTitlePhraseName($fieldGroupId));
                    $groupTitle = (string) $groupTitle;
                } else {
                    $groupTitle = new XenForo_Phrase('ungrouped');
                    $groupTitle = '(' . $groupTitle . ')';
                }

                foreach ($fields as $fieldId => $field) {
                    $options[$groupTitle][$fieldId] = array(
                        'value' => $fieldId,
                        'label' => (string) $field['title'],
                        '_data' => array()
                    );
                }
            }
        }

        return $options;
    } /* END getPostFieldOptions */

    /**
     * Gets the possible post field types.
     *
     * @return array [type] => keys: value, label, hint (optional)
     */
    public function getPostFieldTypes()
    {
        return array(
            'textbox' => array(
                'value' => 'textbox',
                'label' => new XenForo_Phrase('single_line_text_box')
            ),
            'textarea' => array(
                'value' => 'textarea',
                'label' => new XenForo_Phrase('multi_line_text_box')
            ),
            'select' => array(
                'value' => 'select',
                'label' => new XenForo_Phrase('drop_down_selection')
            ),
            'radio' => array(
                'value' => 'radio',
                'label' => new XenForo_Phrase('radio_buttons')
            ),
            'checkbox' => array(
                'value' => 'checkbox',
                'label' => new XenForo_Phrase('check_boxes')
            ),
            'multiselect' => array(
                'value' => 'multiselect',
                'label' => new XenForo_Phrase('multiple_choice_drop_down_selection')
            ),
            'callback' => array(
                'value' => 'callback',
                'label' => new XenForo_Phrase('php_callback')
            )
        );
    } /* END getPostFieldTypes */

    /**
     * Maps post fields to their high level type "group".
     * Field types can be changed only
     * within the group.
     *
     * @return array [field type] => type group
     */
    public function getPostFieldTypeMap()
    {
        return array(
            'textbox' => 'text',
            'textarea' => 'text',
            'radio' => 'single',
            'select' => 'single',
            'checkbox' => 'multiple',
            'multiselect' => 'multiple',
            'callback' => 'text'
        );
    } /* END getPostFieldTypeMap */

    /**
     * Gets the field's description phrase name.
     *
     * @param string $fieldId
     *
     * @return string
     */
    public function getPostFieldDescriptionPhraseName($fieldId)
    {
        return 'post_field_' . $fieldId . '_desc';
    } /* END getPostFieldDescriptionPhraseName */

    /**
     * Gets a field choices's phrase name.
     *
     * @param string $fieldId
     * @param string $choice
     *
     * @return string
     */
    public function getPostFieldChoicePhraseName($fieldId, $choice)
    {
        return 'post_field_' . $fieldId . '_choice_' . $choice;
    } /* END getPostFieldChoicePhraseName */

    /**
     * Gets a field's master title phrase text.
     *
     * @param string $id
     *
     * @return string
     */
    public function getPostFieldMasterTitlePhraseValue($id)
    {
        $phraseName = $this->getPostFieldTitlePhraseName($id);
        return $this->_getPhraseModel()->getMasterPhraseValue($phraseName);
    } /* END getPostFieldMasterTitlePhraseValue */

    /**
     * Gets a field's master description phrase text.
     *
     * @param string $id
     *
     * @return string
     */
    public function getPostFieldMasterDescriptionPhraseValue($id)
    {
        $phraseName = $this->getPostFieldDescriptionPhraseName($id);
        return $this->_getPhraseModel()->getMasterPhraseValue($phraseName);
    } /* END getPostFieldMasterDescriptionPhraseValue */

    protected function _prepareFieldValues(array $fields = array())
    {
        $values = array();
        foreach ($fields as $field) {
            if ($field['field_type'] == 'checkbox' || $field['field_type'] == 'multiselect') {
                $values[$field['field_id']] = @unserialize($field['field_value']);
            } else {
                $values[$field['field_id']] = $field['field_value'];
            }
        }

        return $values;
    } /* END _prepareFieldValues */

    /**
     * Gets the post field values for the given post.
     *
     * @param integer $postId
     *
     * @return array [field id] => value (may be string or array)
     */
    public function getPostFieldValues($postId)
    {
        $fields = $this->_getDb()->fetchAll(
            '
            SELECT value.*, field.field_type
            FROM xf_post_field_value AS value
            INNER JOIN xf_post_field AS field ON (field.field_id = value.field_id)
            WHERE value.post_id = ?
        ', $postId);

        return $this->_prepareFieldValues($fields);
    } /* END getPostFieldValues */

    /**
     * Gets the article page field values for the given post.
     *
     * @param integer $articlePageId
     *
     * @return array [field id] => value (may be string or array)
     */
    public function getArticlePageFieldValues($articlePageId)
    {
        $fields = $this->_getDb()->fetchAll(
            '
            SELECT value.*, field.field_type
            FROM xf_article_page_field_value AS value
            INNER JOIN xf_post_field AS field ON (field.field_id = value.field_id)
            WHERE value.article_page_id = ?
        ', $articlePageId);

        return $this->_prepareFieldValues($fields);
    } /* END getArticlePageFieldValues */

    /**
     * Gets the post field values for the given post.
     *
     * @param integer $articleId
     *
     * @return array [field id] => value (may be string or array)
     */
    public function getArticleFieldValues($articleId)
    {
        $fields = $this->_getDb()->fetchAll(
            '
                SELECT value.*, field.field_type
                FROM xf_article_field_value AS value
                INNER JOIN xf_post_field AS field ON (field.field_id = value.field_id)
                WHERE value.article_id = ?
            ', $articleId);

        return $this->_prepareFieldValues($fields);
    } /* END getArticleFieldValues */

    /**
     * Gets the default post field values for the given forum.
     *
     * @param integer $nodeId
     *
     * @return array [field id] => value (may be string or array)
     */
    public function getDefaultPostFieldValues($nodeId = null)
    {
        if ($nodeId) {
            $fields = $this->_getDb()->fetchAll(
                '
                SELECT ff.*, field.field_type
                FROM xf_forum_post_field AS ff
                INNER JOIN xf_post_field AS field ON (field.field_id = ff.field_id)
                WHERE ff.node_id = ?
                ', $nodeId);

            return $this->_prepareFieldValues($fields);
        } else {
            return array(
                'field_id' => null,
                'field_group_id' => '0',
                'display_order' => 1,
                'field_type' => 'textbox',
                'match_type' => 'none',
                'max_length' => 0,
                'field_choices' => ''
            );
        }
    } /* END getDefaultPostFieldValues */

    /**
     * Rebuilds the cache of post field info for front-end display
     *
     * @return array
     */
    public function rebuildPostFieldCache()
    {
        $cache = array();
        foreach ($this->getPostFields() as $fieldId => $field) {
            $cache[$fieldId] = XenForo_Application::arrayFilterKeys($field,
                array(
                    'field_id',
                    'field_type',
                    'field_group_id'
                ));
        }

        $this->_getDataRegistryModel()->set('postFieldsInfo', $cache);
        return $cache;
    } /* END rebuildPostFieldCache */

    /**
     * Rebuilds the 'materialized_order' field in the field table,
     * based on the canonical display_order data in the field and field_group
     * tables.
     */
    public function rebuildPostFieldMaterializedOrder()
    {
        $fields = $this->getPostFields(array(), array(
            'order' => 'canonical_order'
        ));

        $db = $this->_getDb();
        $ungroupedFields = array();
        $updates = array();
        $i = 0;

        foreach ($fields as $fieldId => $field) {
            if ($field['field_group_id']) {
                if (++ $i != $field['materialized_order']) {
                    $updates[$fieldId] = 'WHEN ' . $db->quote($fieldId) . ' THEN ' . $db->quote($i);
                }
            } else {
                $ungroupedFields[$fieldId] = $field;
            }
        }

        foreach ($ungroupedFields as $fieldId => $field) {
            if (++ $i != $field['materialized_order']) {
                $updates[$fieldId] = 'WHEN ' . $db->quote($fieldId) . ' THEN ' . $db->quote($i);
            }
        }

        if (!empty($updates)) {
            $db->query(
                '
                    UPDATE xf_post_field SET materialized_order = CASE field_id
                    ' . implode(' ', $updates) . '
                    END
                    WHERE field_id IN(' . $db->quote(array_keys($updates)) . ')
                ');
        }
    } /* END rebuildPostFieldMaterializedOrder */

    public function verifyPostFieldIsUsable($fieldId, $nodeId, array $viewingUser = null)
    {
        $this->standardizeViewingUserReference($viewingUser);

        if (!$fieldId) {
            return true; // not picking one, always ok
        }

        $field = $this->getPostFieldIfInForum($fieldId, $nodeId);
        if (!$field) {
            return false; // bad field or bad node
        }

        return $this->_verifyPostFieldIsUsableInternal($field, $viewingUser);
    } /* END verifyPostFieldIsUsable */

    protected function _verifyPostFieldIsUsableInternal(array $field, array $viewingUser)
    {
        $userGroups = explode(',', $field['allowed_user_group_ids']);
        if (in_array(-1, $userGroups) || in_array($viewingUser['user_group_id'], $userGroups)) {
            return true; // available to all groups or the primary group
        }

        if ($viewingUser['secondary_group_ids']) {
            foreach (explode(',', $viewingUser['secondary_group_ids']) as $userGroupId) {
                if (in_array($userGroupId, $userGroups)) {
                    return true; // available to one secondary group
                }
            }
        }

        return false; // not available to any groups
    } /* END _verifyPostFieldIsUsableInternal */

    // field groups ---------------------------------------------------------


    /**
     * Fetches a single field group, as defined by its unique field group ID
     *
     * @param integer $fieldGroupId
     *
     * @return array
     */
    public function getPostFieldGroupById($fieldGroupId)
    {
        if (!$fieldGroupId) {
            return array();
        }

        return $this->_getDb()->fetchRow(
            '
                SELECT *
                FROM xf_post_field_group
                WHERE field_group_id = ?
            ', $fieldGroupId);
    } /* END getPostFieldGroupById */

    public function getAllPostFieldGroups()
    {
        return $this->fetchAllKeyed(
            '
                SELECT *
                FROM xf_post_field_group
                ORDER BY display_order
            ', 'field_group_id');
    } /* END getAllPostFieldGroups */

    public function getPostFieldGroupOptions($selectedGroupId = '')
    {
        $fieldGroups = $this->getAllPostFieldGroups();
        $fieldGroups = $this->preparePostFieldGroups($fieldGroups);

        $options = array();

        foreach ($fieldGroups as $fieldGroupId => $fieldGroup) {
            $options[$fieldGroupId] = $fieldGroup['title'];
        }

        return $options;
    } /* END getPostFieldGroupOptions */

    public function mergePostFieldsIntoGroups(array $fields, array $fieldGroups)
    {
        $merge = array();

        foreach ($fieldGroups as $fieldGroupId => $fieldGroup) {
            if (isset($fields[$fieldGroupId])) {
                $merge[$fieldGroupId] = $fields[$fieldGroupId];
                unset($fields[$fieldGroupId]);
            } else {
                $merge[$fieldGroupId] = array();
            }
        }

        if (!empty($fields)) {
            foreach ($fields as $fieldGroupId => $_fields) {
                $merge[$fieldGroupId] = $_fields;
            }
        }

        return $merge;
    } /* END mergePostFieldsIntoGroups */

    public function getPostFieldGroupTitlePhraseName($fieldGroupId)
    {
        return 'post_field_group_' . $fieldGroupId;
    } /* END getPostFieldGroupTitlePhraseName */

    public function preparePostFieldGroups(array $fieldGroups)
    {
        return array_map(array(
            $this,
            'preparePostFieldGroup'
        ), $fieldGroups);
    } /* END preparePostFieldGroups */

    public function preparePostFieldGroup(array $fieldGroup)
    {
        $fieldGroup['title'] = new XenForo_Phrase($this->getPostFieldGroupTitlePhraseName($fieldGroup['field_group_id']));

        return $fieldGroup;
    } /* END preparePostFieldGroup */

    /**
     * Gets the XML representation of a field, including customized templates.
     *
     * @param array $field
     *
     * @return DOMDocument
     */
    public function getFieldXml(array $field)
    {
        $document = new DOMDocument('1.0', 'utf-8');
        $document->formatOutput = true;

        $rootNode = $document->createElement('field');
        $this->_appendFieldXml($rootNode, $field);
        $document->appendChild($rootNode);

        $templatesNode = $document->createElement('templates');
        $rootNode->appendChild($templatesNode);
        $this->getModelFromCache('Waindigo_CustomFields_Model_Template')->appendTemplatesFieldXml($templatesNode,
            $field);

        $adminTemplatesNode = $document->createElement('admin_templates');
        $rootNode->appendChild($adminTemplatesNode);
        $this->getModelFromCache('Waindigo_CustomFields_Model_AdminTemplate')->appendAdminTemplatesFieldXml(
            $adminTemplatesNode, $field);

        $phrasesNode = $document->createElement('phrases');
        $rootNode->appendChild($phrasesNode);
        $this->getModelFromCache('XenForo_Model_Phrase')->appendPhrasesFieldXml($phrasesNode, $field);

        return $document;
    } /* END getFieldXml */

    /**
     * Appends the add-on field XML to a given DOM element.
     *
     * @param DOMElement $rootNode Node to append all elements to
     * @param string $addOnId Add-on ID to be exported
     */
    public function appendFieldsAddOnXml(DOMElement $rootNode, $addOnId)
    {
        $document = $rootNode->ownerDocument;

        $fields = $this->getPostFields(array(
            'addon_id' => $addOnId
        ));
        foreach ($fields as $field) {
            $fieldNode = $document->createElement('field');
            $this->_appendFieldXml($fieldNode, $field);
            $rootNode->appendChild($fieldNode);
        }
    } /* END appendFieldsAddOnXml */

    /**
     *
     * @param DOMElement $rootNode
     * @param array $field
     */
    protected function _appendFieldXml(DOMElement $rootNode, $field)
    {
        $document = $rootNode->ownerDocument;

        $rootNode->setAttribute('export_callback_method', $field['export_callback_method']);
        $rootNode->setAttribute('export_callback_class', $field['export_callback_class']);
        $rootNode->setAttribute('field_callback_method', $field['field_callback_method']);
        $rootNode->setAttribute('field_callback_class', $field['field_callback_class']);
        $rootNode->setAttribute('field_choices_callback_class', $field['field_choices_callback_class']);
        $rootNode->setAttribute('field_choices_callback_method', $field['field_choices_callback_method']);
        $rootNode->setAttribute('display_callback_method', $field['display_callback_method']);
        $rootNode->setAttribute('display_callback_class', $field['display_callback_class']);
        $rootNode->setAttribute('max_length', $field['max_length']);
        $rootNode->setAttribute('match_callback_method', $field['match_callback_method']);
        $rootNode->setAttribute('match_callback_class', $field['match_callback_class']);
        $rootNode->setAttribute('match_regex', $field['match_regex']);
        $rootNode->setAttribute('match_type', $field['match_type']);
        $rootNode->setAttribute('field_type', $field['field_type']);
        $rootNode->setAttribute('display_order', $field['display_order']);
        $rootNode->setAttribute('field_id', $field['field_id']);
        $rootNode->setAttribute('addon_id', $field['addon_id']);

        $titleNode = $document->createElement('title');
        $rootNode->appendChild($titleNode);
        $titleNode->appendChild(
            XenForo_Helper_DevelopmentXml::createDomCdataSection($document,
                new XenForo_Phrase('post_field_' . $field['field_id'])));

        $descriptionNode = $document->createElement('description');
        $rootNode->appendChild($descriptionNode);
        $descriptionNode->appendChild(
            XenForo_Helper_DevelopmentXml::createDomCdataSection($document,
                new XenForo_Phrase('post_field_' . $field['field_id'] . '_desc')));

        $displayTemplateNode = $document->createElement('display_template');
        $rootNode->appendChild($displayTemplateNode);
        $displayTemplateNode->appendChild(
            XenForo_Helper_DevelopmentXml::createDomCdataSection($document, $field['display_template']));

        $fieldChoicesNode = $document->createElement('field_choices');
        $rootNode->appendChild($fieldChoicesNode);
        if ($field['field_choices']) {
            $fieldChoices = unserialize($field['field_choices']);
            foreach ($fieldChoices as $fieldChoiceValue => $fieldChoiceText) {
                $fieldChoiceNode = $document->createElement('field_choice');
                $fieldChoiceNode->setAttribute('value', $fieldChoiceValue);
                $fieldChoiceNode->appendChild(
                    XenForo_Helper_DevelopmentXml::createDomCdataSection($document, $fieldChoiceText));
                $fieldChoicesNode->appendChild($fieldChoiceNode);
            }
        }
    } /* END _appendFieldXml */

    /**
     * Imports a field XML file.
     *
     * @param SimpleXMLElement $document
     * @param string $fieldGroupId
     * @param integer $overwriteFieldId
     *
     * @return array List of cache rebuilders to run
     */
    public function importFieldXml(SimpleXMLElement $document, $fieldGroupId = 0, $overwriteFieldId = 0)
    {
        if ($document->getName() != 'field') {
            throw new XenForo_Exception(new XenForo_Phrase('provided_file_is_not_valid_field_xml'), true);
        }

        $fieldId = (string) $document['field_id'];
        if ($fieldId === '') {
            throw new XenForo_Exception(new XenForo_Phrase('provided_file_is_not_valid_field_xml'), true);
        }

        $phraseModel = $this->_getPhraseModel();

        $overwriteField = array();
        if ($overwriteFieldId) {
            $overwriteField = $this->getPostFieldById($overwriteFieldId);
        }

        $db = $this->_getDb();
        XenForo_Db::beginTransaction($db);

        $dw = XenForo_DataWriter::create('Waindigo_CustomPostFields_DataWriter_PostField');
        if (isset($overwriteField['field_id'])) {
            $dw->setExistingData($overwriteFieldId);
        } else {
            if ($overwriteFieldId) {
                $dw->set('field_id', $overwriteFieldId);
            } else {
                $dw->set('field_id', $fieldId);
            }
            if ($fieldGroupId) {
                $dw->set('field_group_id', $fieldGroupId);
            }
            $dw->set('allowed_user_group_ids', -1);
        }

        $dw->bulkSet(
            array(
                'display_order' => $document['display_order'],
                'field_type' => $document['field_type'],
                'match_type' => $document['match_type'],
                'match_regex' => $document['match_regex'],
                'match_callback_class' => $document['match_callback_class'],
                'match_callback_method' => $document['match_callback_method'],
                'max_length' => $document['max_length'],
                'display_callback_class' => $document['display_callback_class'],
                'display_callback_method' => $document['display_callback_method'],
                'field_choices_callback_class' => $document['field_choices_callback_class'],
                'field_choices_callback_method' => $document['field_choices_callback_method'],
                'field_callback_class' => $document['field_callback_class'],
                'field_callback_method' => $document['field_callback_method'],
                'export_callback_class' => $document['export_callback_class'],
                'export_callback_method' => $document['export_callback_method'],
                'display_template' => XenForo_Helper_DevelopmentXml::processSimpleXmlCdata($document->display_template)
            ));

        /* @var $addOnModel XenForo_Model_AddOn */
        $addOnModel = XenForo_Model::create('XenForo_Model_AddOn');
        $addOn = $addOnModel->getAddOnById($document['addon_id']);
        if (!empty($addOn)) {
            $dw->set('addon_id', $addOn['addon_id']);
        }

        $dw->setExtraData(Waindigo_CustomPostFields_DataWriter_PostField::DATA_TITLE,
            XenForo_Helper_DevelopmentXml::processSimpleXmlCdata($document->title));
        $dw->setExtraData(Waindigo_CustomPostFields_DataWriter_PostField::DATA_DESCRIPTION,
            XenForo_Helper_DevelopmentXml::processSimpleXmlCdata($document->description));

        $fieldChoices = XenForo_Helper_DevelopmentXml::fixPhpBug50670($document->field_choices->field_choice);

        foreach ($fieldChoices as $fieldChoice) {
            if ($fieldChoice && $fieldChoice['value']) {
                $fieldChoicesCombined[(string) $fieldChoice['value']] = XenForo_Helper_DevelopmentXml::processSimpleXmlCdata(
                    $fieldChoice);
            }
        }

        if (isset($fieldChoicesCombined))
            $dw->setFieldChoices($fieldChoicesCombined);

        $dw->save();

        $this->getModelFromCache('Waindigo_CustomFields_Model_Template')->importTemplatesFieldXml($document->templates);
        $this->getModelFromCache('Waindigo_CustomFields_Model_AdminTemplate')->importAdminTemplatesFieldXml(
            $document->admin_templates);
        $phraseModel->importPhrasesXml($document->phrases, 0);

        XenForo_Db::commit($db);

        if (XenForo_Application::$versionId < 1020000) {
            return array(
                'Template',
                'Phrase',
                'AdminTemplate'
            );
        }
        XenForo_Application::defer('Atomic',
            array(
                'simple' => array(
                    'Phrase',
                    'TemplateReparse',
                    'Template',
                    'AdminTemplateReparse',
                    'AdminTemplate'
                )
            ), 'customFieldRebuild', true);
        return true;
    } /* END importFieldXml */

    /**
     * Imports the add-on fields XML.
     *
     * @param SimpleXMLElement $xml XML element pointing to the root of the data
     * @param string $addOnId Add-on to import for
     * @param integer $maxExecution Maximum run time in seconds
     * @param integer $offset Number of elements to skip
     *
     * @return boolean integer on completion; false if the XML isn't correct;
     * integer otherwise with new offset value
     */
    public function importFieldsAddOnXml(SimpleXMLElement $xml, $addOnId, $maxExecution = 0, $offset = 0)
    {
        $db = $this->_getDb();

        XenForo_Db::beginTransaction($db);

        $startTime = microtime(true);

        $fields = XenForo_Helper_DevelopmentXml::fixPhpBug50670($xml->field);

        $current = 0;
        $restartOffset = false;
        foreach ($fields as $field) {
            $current++;
            if ($current <= $offset) {
                continue;
            }

            $fieldId = (string) $field['field_id'];

            if (!$field['addon_id']) {
                $field->addAttribute('addon_id', $addOnId);
            }

            $this->importFieldXml($field, 0, $fieldId);

            if ($maxExecution && (microtime(true) - $startTime) > $maxExecution) {
                $restartOffset = $current;
                break;
            }
        }

        XenForo_Db::commit($db);

        return ($restartOffset ? $restartOffset : true);
    } /* END importFieldsAddOnXml */

    /**
     *
     * @return XenForo_Model_Node
     */
    protected function _getNodeModel()
    {
        return $this->getModelFromCache('XenForo_Model_Node');
    } /* END _getNodeModel */

    /**
     *
     * @return XenForo_Model_Phrase
     */
    protected function _getPhraseModel()
    {
        return $this->getModelFromCache('XenForo_Model_Phrase');
    } /* END _getPhraseModel */
}