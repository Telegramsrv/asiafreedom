<?php

class SimplePortal_Listener_Bb
{
    /**
     * @xfcp XenForo_BbCode_Formatter_Base
     * @param $className
     * @param $extend
     */
    public static function extendFormatterBase($className, &$extend)
    {
        $extend[] = 'SimplePortal_Extend_BbCode_Formatter_Base';
    }

    /**
     * @xfcp XenForo_BbCode_Formatter_Text
     * @param $className
     * @param $extend
     */
    public static function extendFormatterText($className, &$extend)
    {
        $extend[] = 'SimplePortal_Extend_BbCode_Formatter_Text';
    }
}