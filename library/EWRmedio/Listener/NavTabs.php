<?php

class EWRmedio_Listener_NavTabs
{
	public static function listen(array &$extraTabs, $selectedTabId)
	{
		$permsModel = XenForo_Model::create('EWRmedio_Model_Perms');
		$perms = $permsModel->getPermissions();
		
		if ($perms['mod'])
		{
			$mediaModel = XenForo_Model::create('EWRmedio_Model_Lists');
			$counter = $mediaModel->getQueueCount();
		}

		$extraTabs['media'] = array(
			'title' => new XenForo_Phrase('media'),
			'href' => XenForo_Link::buildPublicLink('full:media'),
			'position' => 'middle',
			'linksTemplate' => 'EWRmedio_Navtabs',
			'perms' => $perms,
			'media' => $selectedTabId == 'media' ? true : false,
			'counter' => !empty($counter) ? $counter : false,
		);
	}
}