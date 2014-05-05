<?php

class SimplePortal_Listener_Controller
{

    /**
     * @xfcp XenForo_ControllerPublic_Forum
     * @param $className
     * @param $extend
     */
    public static function extendXenforoForum($className, &$extend)
    {
        $extend[] = 'SimplePortal_Extend_ControllerPublic_Forum';
    }

    /**
     * @xfcp XenForo_ControllerPublic_InlineMod_Thread
     * @param $className
     * @param $extend
     */
    public static function extendThreadInlineModeration($className, &$extend)
    {
        $extend[] = 'SimplePortal_Extend_ControllerPublic_InlineMod_Thread';
    }



}