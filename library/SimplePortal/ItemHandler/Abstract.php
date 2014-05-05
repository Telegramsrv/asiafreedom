<?php

abstract class SimplePortal_ItemHandler_Abstract
{

    /**
     * factory method to get the content itemhandler
     * @param $class
     * @return SimplePortal_ItemHandler_Abstract
     * @throws XenForo_Exception
     */
    public static function create($class)
    {
        $classResolved = XenForo_Application::resolveDynamicClass($class);

        if (XenForo_Application::autoload($classResolved)) {

            $obj = new $classResolved();

            if ($obj instanceof SimplePortal_ItemHandler_Abstract) {
                return $obj;
            }

        }

        throw new XenForo_Exception("Invalid extraportal_handler '$class' specified");
    }

    abstract public function canPromote(array $item = array(), array $viewingUser = null);

    /**
     * allows you to set additional data to the item datawriter
     * e.g. to set the item attachment
     *
     * @param XenForo_DataWriter $itemDataWriter
     * @param                    $itemData
     *
     * @return mixed
     */
    abstract function processAdditonalSaveData(XenForo_DataWriter &$itemDataWriter, $itemData);


    /** returns the url to the create new item form, if no permissions or not available => false */
    abstract function getCreateNewUrl();

    abstract function canIncludeAttachments();

    abstract function getContentTypeKeyPhrase();

    abstract function getContentTypeKey();

    abstract function getItemById($id);

    abstract function getItemsByIds(array $ids);

    abstract function getFetchOptions(array $extraData = array());

    /**
     *  this method have to return the full url
     * @param array $item
     * @return mixed
     */
    abstract function getItemUrl(array $item);

    final public function prepareContent(array $item)
    {
        $item['url'] = $this->getItemUrl($item);
        $item['extra'] = @unserialize($item['extra_data']);
        $this->_prepareContent($item);
        return $item;
    }

    /**
     * @param array $item
     */
    protected function _prepareContent(array &$item)
    {
    }

    /**
     * @param $contentId
     * @return array
     */
    public function getAttachmentsForContent($contentId)
    {
        /** @var SimplePortal_Model_Attachment $model */
        $model = $this->getModelFromCache('SimplePortal_Model_Attachment');
        return $model->getPreviewImages($this->getContentTypeKey(), $contentId);
    }

    public function renderHtml(array $item, XenForo_View $view)
    {
        $item['templateTitle'] = $this->getDefaultTemplateTitle($item);
        return $view->createTemplateObject($item['templateTitle'], array('item' => $item));
    }

    protected function getDefaultTemplateTitle(array $item)
    {
        return 'el_portal_item_bit';
    }


    public function getModelFromCache($class)
    {
        if (!isset($this->_modelCache[$class]))
        {
            $this->_modelCache[$class] = XenForo_Model::create($class);
        }

        return $this->_modelCache[$class];
    }

    public function isAlreadyPromoted($contentType, $contentId){
        return $this->getModelFromCache('SimplePortal_Model_PortalItem')->getPortalItem(
            array('content_type' => $contentType,
                'content_id' => $contentId));
    }

    protected $_modelCache = array();


}