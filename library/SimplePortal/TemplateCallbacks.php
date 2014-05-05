<?php

class SimplePortal_TemplateCallbacks
{
    static $itemModel = null;

    /**
     * @return SimplePortal_Model_PortalItem
     */
    protected  static function getItemModel(){
        if (!self::$itemModel){
            self::$itemModel = XenForo_Model::create('SimplePortal_Model_PortalItem');
        }
        return self::$itemModel;
    }


    /**
     * @param $content
     * @param $params
     * @param XenForo_Template_Abstract $template
     * @return XenForo_Template_Abstract
     */
    public static function getPromoteLink($content, $params, XenForo_Template_Abstract $template)
    {
        $contentType = $params['content_type'];
        $contentData = $params['content_data'];     // if we'll ever need it, we have it now, without changing anything in the templates:)
        $contentId = $params['content_id'];

        if (self::getItemModel()->canPromoteItem($contentType, $contentData))
        {
            $promoteLink = XenForo_Link::buildPublicLink('portal/manage-items/edit',
                null,
                array(
                    'content_type' => $contentType,
                    'content_id' => $contentId)
            );

            return $template->create('el_portal_promotelink', array('promoteLink' => $promoteLink));
        }
    }



    public static function getPromoteFormElement($content, $params, XenForo_Template_Abstract $template){
        $contentType = $params['content_type'];
        $contentData = $params['content_data'];     // if we'll ever need it, we have it now, without changing anything in the templates:)
        $contentId = $params['content_id'];

        if (self::getItemModel()->canPromoteItem($contentType, $contentData))
        {
            if ($contentType == 'thread' && in_array($contentData['node_id'], SimplePortal_Static::option('autopromotenodes'))){
                //thread will be promoted automatically... we don't need the form
                return '';
            }

            $viewParams = array(
                'categories' => self::getItemModel()->getModelFromCache('SimplePortal_Model_Category')->getAllCategories(),
                'content_type' => 'thread',
                'item' => self::getItemModel()->getDefaultItem()
            );

            return $template->create('el_portal_promothread', array_merge($template->getParams(), $viewParams));
        }
    }
}