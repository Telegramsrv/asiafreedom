<?php

class SimplePortal_Model_PortalItem extends XenForo_Model
{

    CONST FETCH_CATEGORY = 0x01;


    public function getDefaultItem($contentType = 'thread')
    {
        return array(
            'display_order' => 10,
            'content_type' => $contentType
        );
    }

    public function getDefaultFetchOptions(array $fetchOptions = array())
    {

        if (SimplePortal_Static::option('newsSystem')) {
            $fetchOptions['order'] = 'id';
            $fetchOptions['direction'] = 'desc';
        }
        return $fetchOptions;
    }

    /**
     * verifies if the user is able to promote AT LEAST 1 content type!
     * @return bool
     */
    public function canPromoteItems()
    {
        $handlers = $this->getPortalItemHandlerClasses();

        if ($handlers) {
            foreach ($handlers AS $handler) {
                /** @var $handler SimplePortal_ItemHandler_Abstract */
                if ($handler->canPromote()) {
                    return true;
                }
            }
        }
        return false;
    }

    public function canPromoteItem($type, array $data, &$errorPhraseKey = '', array $nodePermissions = null, array $viewingUser = null)
    {
        $canPromote = false;
        $this->standardizeViewingUserReference($viewingUser);
        if ($type) {
            $handler = SimplePortal_Static::getItemModel()->getPortalItemHandlerClass($type);
            if ($handler) {
                $canPromote = $handler->canPromote($data);
            }
        } else {
            $canPromote = $this->canPromoteItems();
        }

        return $canPromote;
    }


    public function getPortalItemById($portalItemId)
    {
        return $this->_getDb()->fetchRow('
            SELECT *
            FROM xf_portalitem
            WHERE portalItem_id = ?
            ', $portalItemId);
    }

    public function getAllPortalItems()
    {
        return $this->getPortalItems();
    }

    public function countItems(array $conditions = array(), array $fetchOptions = array())
    {
        $whereConditions = $this->preparePortalItemConditions($conditions, $fetchOptions);

        return $this->_getDb()->fetchOne('
			SELECT COUNT(*)
			FROM xf_portalitem as portalItem
			WHERE ' . $whereConditions
        );
    }


    public function getPortalItem($conditions, $fetchOptions = array())
    {
        $data = $this->getPortalItems($conditions, $fetchOptions);

        return reset($data);
    }

    public function prepareItemJoinOptions(array $fetchOptions)
    {
        $selectFields = '';
        $joinTables = '';
        $orderBy = '';

        if (!empty($fetchOptions['join'])) {
            if ($fetchOptions['join'] & self::FETCH_CATEGORY) {
                $selectFields .= ',
					category.title as categorytitle';
                $joinTables .= '
					LEFT JOIN xf_portalcategory AS category ON
						(category.category_id = portalItem.category_id)';
            }
        }

        return array(
            'selectFields' => $selectFields,
            'joinTables' => $joinTables,
            'orderClause' => ($orderBy ? "ORDER BY $orderBy" : '')
        );
    }

    public function getPortalItems(array $conditions = array(), array $fetchOptions = array(), $key = 'portalItem_id')
    {

        $whereConditions = $this->preparePortalItemConditions($conditions, $fetchOptions);
        $joinOptions = $this->prepareItemJoinOptions($fetchOptions);
        $orderClause = $this->prepareItemOrderOptions($fetchOptions);
        $limitOptions = $this->prepareLimitFetchOptions($fetchOptions);

        $q = '
        SELECT portalItem.*
        	' . $joinOptions['selectFields'] . '
        FROM xf_portalitem as portalItem
        ' . $joinOptions['joinTables'] . '
        WHERE ' . $whereConditions . '
       ' . $orderClause;

        $query = $this->limitQueryResults($q
            , $limitOptions['limit'], $limitOptions['offset']
        );
        return $this->fetchAllKeyed($query, $key);
    }

    public function prepareItemOrderOptions(array &$fetchOptions, $defaultOrderSql = 'portalItem.display_order')
    {

        $choices = array(
            'id' => 'portalItem.portalItem_id',
            'display_order' => 'portalItem.display_order, portalItem.portalItem_id'
        );
        return $this->getOrderByClause($choices, $fetchOptions, $defaultOrderSql);
    }

    public function preparePortalItemConditions(array $conditions, array &$fetchOptions)
    {
        $sqlConditions = array();
        $db = $this->_getDb();

        if (isset($conditions['portalItem_id']) && $conditions['portalItem_id'] != 0) {
            if (is_array($conditions['portalItem_id'])) {
                $sqlConditions[] = 'portalItem.portalItem_id IN ( ' . $db->quote($conditions['portalItem_id']) . ')';
            } else {
                $sqlConditions[] = 'portalItem.portalItem_id = ' . $db->quote($conditions['portalItem_id']);
            }
        }


        if (isset($conditions['content_type'])) {
            $sqlConditions[] = 'portalItem.content_type = ' . $db->quote($conditions['content_type']);
        }

        if (isset($conditions['content_id'])) {
            if (is_array($conditions['content_id'])) {
                $sqlConditions[] = 'portalItem.content_id IN (' . $db->quote($conditions['content_id']) . ')';
            } else {
                $sqlConditions[] = 'portalItem.content_id = ' . $db->quote($conditions['content_id']);
            }
        }

        if (isset($conditions['category_id']) && $conditions['category_id'] > 0) {
            $sqlConditions[] = 'portalItem.category_id = ' . $db->quote($conditions['category_id']);
        }

        return $this->getConditionsForClause($sqlConditions);
    }

    public function fetchPortalItemsData($portalItems)
    {
        $grouped = array();
        foreach ($portalItems AS $item) {
            $grouped[$item['content_type']][$item['content_id']] = $item;
        }

        foreach ($grouped AS $contentType => $portalItemData) {

            /** @var $handler SimplePortal_ItemHandler_Abstract */
            $handler = $this->getPortalItemHandlerClass($contentType);

            if ($handler instanceof SimplePortal_ItemHandler_Abstract) {
                $data = $handler->getItemsByIds(array_keys($portalItemData));
            }

            foreach ($portalItems AS $portalId => $itemData) {
                if (isset($data[$itemData['content_id']])) {
                    $preparedData = $handler->prepareContent($data[$itemData['content_id']]);
                    $portalItems[$portalId]['data'] = $preparedData;
                }
            }
        }

        return $portalItems;
    }


    protected function getPortalItemHandlers()
    {
        return $this->getContentTypesWithField('simpleportal_handler_class');
    }

    /**
     * @param $contentType
     * @return SimplePortal_ItemHandler_Abstract
     */
    public function getPortalItemHandlerClass($contentType)
    {
        if (!$contentType) {
            return null;
        }

        $cacheKey = "elPortalHandler_$contentType";

        $object = $this->_getLocalCacheData($cacheKey);

        if ($object === false) {

            $class = $this->getContentTypeField($contentType, 'simpleportal_handler_class');

            $object = SimplePortal_ItemHandler_Abstract::create($class);
            $this->setLocalCacheData($cacheKey, $object);
        }

        return $object;

    }

    /**
     * returns array with all handlers
     * @return array
     */
    public function getPortalItemHandlerClasses()
    {
        if (($classes = $this->_getLocalCacheData('simpleportal_handlers')) !== false) {
            return $classes;
        }
        $handlers = $this->getPortalItemHandlers();

        $classes = array();
        foreach ($handlers AS $contentType => $handler) {
            $class = SimplePortal_ItemHandler_Abstract::create($handler);
            $classes[$contentType] = $class;
        }

        $this->setLocalCacheData('simpleportal_handlers', $classes);
        return $classes;
    }


    public function getAdditonalTypesForCreateNewForm()
    {
        $handlers = $this->getPortalItemHandlerClasses();

        $createItems = array();
        foreach ($handlers AS $handler) {
            /* @var $handler SimplePortal_ItemHandler_Abstract */
            if ($url = $handler->getCreateNewUrl()) {
                $phrase = $handler->getContentTypeKeyPhrase();
                $createItems[$phrase] = $url;
            }
        }

        return $createItems;
    }


    public function logModerationAction($contentType, $contendId, $action)
    {
        $handler = $this->getPortalItemHandlerClass($contentType);
        $contentData = $handler->getItemById($contendId);
        if ($contentData) {
            XenForo_Model_Log::logModeratorAction($contentType, $contentData, $action);
        }
    }
}