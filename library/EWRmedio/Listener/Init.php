<?php

class EWRmedio_Listener_Init
{
	public static function listen(XenForo_Dependencies_Abstract $dependencies, array $data)
	{
		XenForo_DataWriter_User::$usernameChangeUpdates['permanent']['ewrmedio_media_username'] = array('EWRmedio_media', 'username', 'user_id');
		XenForo_DataWriter_User::$usernameChangeUpdates['permanent']['ewrmedio_comments_username'] = array('EWRmedio_comments', 'username', 'user_id');

        XenForo_Template_Helper_Core::$helperCallbacks['medio'] = array('EWRmedio_Template_Helper', 'getMedioHighUrl');
        XenForo_Template_Helper_Core::$helperCallbacks['medio2'] = array('EWRmedio_Template_Helper', 'getMedioLowUrl');
        XenForo_Template_Helper_Core::$helperCallbacks['keyword_icon'] = array('EWRmedio_Template_Helper', 'getKeywordIcon');
	}
}