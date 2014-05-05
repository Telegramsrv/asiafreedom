<?php

class SimplePortal_Listener_DataWriter
{

    public static function loadClassDatawriterAttachment($className, &$extend)
    {
        $extend[] = 'SimplePortal_Extend_DataWriter_Attachment';
    }

    /**
     * @xfcp XenForo_DataWriter_Discussion_Thread
     * @param $className
     * @param $extend
     */
    public static function loadClassDatawriterDiscussionThread($className, &$extend){
        $extend[] = 'SimplePortal_Extend_DataWriter_Discussion_Thread';
    }
}