<?php

class AnyTV_Listeners_NavigationTabs
{
    public static function navtabs(array &$extraTabs, $selectedTabId)
	{
		//echo $selectedTabId;
		$extraTabs['about-us'] = array(
			'id' => 'about-us',
			'title' => new XenForo_Phrase('about_us'),
			'href' => '?pages/about-us',
			'selected' => ($selectedTabId == 'about-us')
		);

		$extraTabs['gameslist'] = array(
			'id' => 'gameslist',
			'title' => new XenForo_Phrase('games'),
			'href' => '?pages/gameslist',
			'selected' => ($selectedTabId == 'gameslist')
		);
	}
}