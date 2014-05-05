<?php
class SimplePortal_Listener_Proxy{


/**
* @xfcp: XenForo_Model_Thread
*
*/
public static function loadClass ($class, &$extend){
    $extend[] = 'SimplePortal_Extend_XenForo_Model_Thread';
}

}