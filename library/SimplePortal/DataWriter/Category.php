<?php

class SimplePortal_DataWriter_Category extends XenForo_DataWriter
{

    protected $_existingDataErrorPhrase = 'requested_category_not_found';

	protected function _getFields()
	{
		return array(
			'xf_portalcategory' => array(
				'category_id' => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
				'title' => array('type' => self::TYPE_STRING),
				'display_order'         => array('type' => self::TYPE_UINT,   'default' => 1),
				'item_count'       => array('type' => self::TYPE_UINT, 'default' => 0),
                'style_id'           => array('type' => self::TYPE_UINT, 'default' => 0),
			));
	}

	public function rebuildCounters($adjust = null){
		if ($adjust === null)
		{
			$this->set('item_count', $this->_db->fetchOne("
				SELECT COUNT(*)
				FROM xf_portalitem
				WHERE category_id = ?
			", $this->get('category_id')));
		}
		else
		{
			$this->set('item_count', $this->get('item_count') + $adjust);
		}
	}

	protected function _postDelete(){
		$this->_db->update('xf_portalitem', array('category_id' => 0), 'category_id=' . $this->getExisting('category_id'));
	}

	protected function _getExistingData($data)
	{

		if (!$id = $this->_getExistingPrimaryKey($data, 'category_id')) {
			return false;
		}

		if (!$category = $this->getCategoryModel()->getCategoryById($id)) {
			return false;
		}

		return array('xf_portalcategory' => $category);

	}


	protected function _getUpdateCondition($tableName)
	{
		return 'category_id = ' . $this->_db->quote($this->getExisting('category_id'));
	}

	/**
	 *
	 * @return SimplePortal_Model_Category
	 */
	protected function getCategoryModel()
	{
		return $this->getModelFromCache('SimplePortal_Model_Category');
	}
}