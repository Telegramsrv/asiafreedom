<?php

class SimplePortal_Listener_Model
{

    /**
     * @xfcp XenForo_Model_Attachment
     * @param $className
     * @param $extend
     */
    public static function extendXenforoAttachment($className, &$extend)
    {
        $extend[] = 'SimplePortal_Extend_Model_Attachment';
    }

    /**
     * @xfcp XenForo_Model_Thread
     * @param $className
     * @param $extend
     */
    public static function extendThread($className, &$extend){
        $extend[] = 'SimplePortal_Extend_Model_Thread';
    }

}