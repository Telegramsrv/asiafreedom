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

        $extraTabs['youtubers'] = array(
            'id' => 'youtubers',
            'title' => new XenForo_Phrase('youtubers'),
            'href' => '?pages/youtubers',
            'selected' => ($selectedTabId == 'youtubers')
        );

		$extraTabs['gameslist'] = array(
			'id' => 'gameslist',
			'title' => new XenForo_Phrase('games'),
			'href' => '?pages/gameslist',
			'selected' => ($selectedTabId == 'gameslist')
		);

		$extraTabs['streams'] = array(
			'id' => 'streams',
			'title' => new XenForo_Phrase('streams'),
			'href' => '?pages/streams',
			'selected' => ($selectedTabId == 'streams')
		);
	}
}
