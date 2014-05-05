<?php


class SimplePortal_Helper_Install
{


    public static function addHandlerClass($contentType, $handlerClass, $cacheRebuild = true)
    {
        $db = XenForo_Application::getDb();

        $query = "
         INSERT IGNORE INTO `xf_content_type_field` (
`content_type` ,
`field_name` ,
`field_value`
)
VALUES (
?, 'simpleportal_handler_class',?
);";

        $db->query($query, array($contentType, $handlerClass));
        if ($cacheRebuild){
            XenForo_Model::create('XenForo_Model_ContentType')->rebuildContentTypeCache();
        }
    }


    public static function removeHandlerClass($contentType,  $cacheRebuild = true)
    {
        $db = XenForo_Application::getDb();
        $where = 'content_type = ' . $db->quote($contentType) . ' AND field_name = ' . $db->quote('simpleportal_handler_class');
        $db->delete('xf_content_type_field', $where);
        if ($cacheRebuild){
            XenForo_Model::create('XenForo_Model_ContentType')->rebuildContentTypeCache();
        }
    }

    public static function removePortelItems($contentType){
        $items = SimplePortal_Static::getItemModel()->getPortalItems(array('content_type' =>$contentType));

        foreach ($items AS $itemId => $data){
            $dw = XenForo_DataWriter::create('SimplePortal_DataWriter_PortalItem');
            $dw->setExistingData($itemId);
            $dw->delete();
            unset($dw);
        }
    }
}