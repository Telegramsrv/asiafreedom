<?php

class AnyTV_Listeners_ViewPublic
{
    public static function view($class, array &$extend)
    {
		switch ($class)
		{
			case 'XenForo_ControllerPublic_Account':
				//$extend[] = 'XenForo_ControllerPublic_Abstract';
				$extend[] = 'AnyTV_ControllerPublic_Account';
				break;
			case 'XenForo_ControllerPublic_Member':
				//$extend[] = 'XenForo_ControllerPublic_Abstract';
				$extend[] = 'AnyTV_ControllerPublic_Member';
				break;
		}
    }
}