<?php

class SimplePortal_Helper_Item
{
    public static function getTemplates(XenForo_View $view, array $items, XenForo_BbCode_Parser $bbCodeParser, array $bbCodeOptions, array $handlers = null)
    {

        if (!$handlers) {
            /** @var $model SimplePortal_Model_PortalItem */
            $model = XenForo_Model::create('SimplePortal_Model_PortalItem');
            $handlers = $model->getPortalItemHandlerClasses();
        }

        foreach ($items AS $id => $item) {
            /** @var SimplePortal_ItemHandler_Abstract $handler */

            if (isset($item['data'])) {
                $handler = $handlers[$item['content_type']];
                $item = self::prepareMessage($item, $bbCodeParser, $bbCodeOptions, $view);

                $items[$id]['renderedTemplate'] = $handler->renderHtml($item, $view);
            }

        }

        return $items;
    }

    public static function prepareMessage($item, XenForo_BbCode_Parser $bbCodeParser, array $bbCodeOptions, XenForo_View $view)
    {
        $message = $item['data']['message'];

        if ($bbCodeOptions['states']['viewAttachments']) {
            $string = preg_replace('#\[(quoteee)[^\]]*\].*\[/\\1\]#siU', ' ', $message);
        } else {
            $string = preg_replace('#\[(attach|quote)[^\]]*\].*\[/\\1\]#siU', ' ', $message);
        }

        $formatter = XenForo_BbCode_Formatter_Base::create('ImageCount');
        $parser = XenForo_BbCode_Parser::create($formatter);
        $parser->render($string);

        if (isset($item['data']['attachments'])){
            $item['attachments']  = $item['data']['attachments'];
        }


        $item['mediaCount'] = $formatter->getMediaCount();
        $item['message'] = XenForo_Helper_String::wholeWordTrim($string, SimplePortal_Static::option('charlimit'));

        $item['messageHtml'] = XenForo_ViewPublic_Helper_Message::getBbCodeWrapper($item, $bbCodeParser, $bbCodeOptions);

        if (strpos($item['messageHtml'], SimplePortal_Static::PORTAL_PREVIEW_ENDSTRING)) {
            $item['messageHtml'] = strstr($item['messageHtml'], SimplePortal_Static::PORTAL_PREVIEW_ENDSTRING, true);
        }
        return $item;
    }
}