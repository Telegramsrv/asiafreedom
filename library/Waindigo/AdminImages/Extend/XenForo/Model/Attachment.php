<?php

/**
 *
 * @see XenForo_Model_Attachment
 */
class Waindigo_AdminImages_Extend_XenForo_Model_Attachment extends XFCP_Waindigo_AdminImages_Extend_XenForo_Model_Attachment
{

    /**
     *
     * @return string
     */
    public function prepareAttachmentConditions(array $conditions, array &$fetchOptions)
    {
        $sqlConditions = array(
            parent::prepareAttachmentConditions($conditions, $fetchOptions)
        );
        $db = $this->_getDb();
        
        if (!empty($conditions['content_ids'])) {
            $sqlConditions[] = 'attachment.content_id IN (' . $db->quote($conditions['content_ids']) . ')';
        }
        
        return $this->getConditionsForClause($sqlConditions);
    } /* END prepareAttachmentConditions */
}