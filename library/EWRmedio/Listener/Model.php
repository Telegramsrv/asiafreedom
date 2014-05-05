<?php

class EWRmedio_Listener_Model
{
    public static function model($class, array &$extend)
    {
		switch ($class)
		{
			case 'XenForo_Model_User':
				$extend[] = 'EWRmedio_Model_User';
				break;
		}
    }
}