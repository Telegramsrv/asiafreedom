<?php

class SimplePortal_DataWriter_PortalItem extends XenForo_DataWriter
{

    const OPTION_UPDATE_CATEGORY = 'updateContainer';
    CONST DATA_ADDITIONAL = 'additionalData';


	protected function _getFields()
	{
		return array(
			'xf_portalitem' => array(
				'portalItem_id' => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
				'content_type' => array('type' => self::TYPE_STRING,  'required' => true, 'maxLength' => 25),
				'content_id'   => array('type' => self::TYPE_UINT,    'required' => true),
				'display_order' => array('type' => self::TYPE_UINT,   'default' => 10),
				'category_id' => array('type' => self::TYPE_UINT, 'default' => 0),
				'attachment' => array('type' => self::TYPE_STRING, 'default' => ''),
				'attachment_id'   => array('type' => self::TYPE_UINT,    'required' => true,'default' => 0),
                'extra_data'        => array('type' => self::TYPE_UNKNOWN, 'default' => 'a:0:{}',
                            'verification' => array('$this', '_verifyExtraData')),
            )
        );

	}

	protected function _getExistingData($data)
	{
		if (!$id = $this->_getExistingPrimaryKey($data, 'portalItem_id')) {
			return false;
		}

		if (!$portalItem = $this->getItemModel()->getPortalItemById($id)) {
			return false;
		}

		return array('xf_portalitem' => $portalItem);

	}

    protected function _getDefaultOptions(){
        return array(
            self::OPTION_UPDATE_CATEGORY => true,
        );
    }


	protected function _getUpdateCondition($tableName)
	{
		return 'portalItem_id = ' . $this->_db->quote($this->getExisting('portalItem_id'));
	}

	protected function _postSave(){
		parent::_postSave();

        if ($this->getOption(self::OPTION_UPDATE_CATEGORY)){
            $this->rebuildCategoryCounter();
        }
	}


	protected function _postDelete(){
		parent::_postDelete();

        if ($this->getOption(self::OPTION_UPDATE_CATEGORY)){
            $this->rebuildCategoryCounter();
        }
	}

	protected function rebuildCategoryCounter(){
        $cat = false;
        if ($this->get('category_id') != 0){
            $cat = $this->get('category_id');
        }

        else if ($this->getExisting('category_id')!= 0){
           $cat = $this->getExisting('category_id');
        }
        if ($cat){
            /** @var $catDw SimplePortal_DataWriter_Category */
            $catDw = XenForo_DataWriter::create('SimplePortal_DataWriter_Category');
            $catDw->setExistingData($cat);
            $catDw->rebuildCounters();
            $catDw->save();
        }
	}

    protected function _verifyExtraData(&$extraData)
    {
        if ($extraData === null)
        {
            $extraData = '';
            return true;
        }

        return XenForo_DataWriter_Helper_Denormalization::verifySerialized($extraData, $this, 'extra_data');
    }


	/**
	 * Returns the user model
	 *
	 * @return SimplePortal_Model_PortalItem
	 */
	protected function getItemModel()
	{
		return $this->getModelFromCache('SimplePortal_Model_PortalItem');
	}
}