<?php

class EWRmedio_Model_Parser extends XenForo_Model
{
	public function parseSidebar()
	{
		$options = XenForo_Application::get('options');

		$sidebar['categories'] = $this->getModelFromCache('EWRmedio_Model_Lists')->getCategoryList();
		$sidebar['users'] = $this->getModelFromCache('EWRmedio_Model_Lists')->getUserList();
		$sidebar['keywords'] = $this->getModelFromCache('EWRmedio_Model_Keywords')->getKeywordCloud($options->EWRmedio_cloudcount, $options->EWRmedio_mincloud, $options->EWRmedio_maxcloud);
		
		if ($options->EWRmedio_animatedcloud && $sidebar['keywords'])
		{
			$sidebar['animated'] = $this->getModelFromCache('EWRmedio_Model_Keywords')->getAnimatedCloud($sidebar['keywords']);
		}
		
		$sidebar['stats'] = $this->getTotals();
		$sidebar['stats']['categories'] = $this->getModelFromCache('EWRmedio_Model_Categories')->getCategoryCount();

        return $sidebar;
	}

	public function parseReplace($replace, $ap = true)
	{
		$options = XenForo_Application::get('options');
		
		$external = $options->boardUrl.'/'.XenForo_Application::$externalDataPath.'/local';
		$scriptjw = $options->boardUrl.'/styles/8wayrun/jw';

		if ($replace['service_width'] <= 100)
		{
			$replace['service_width'] .= '%';
		}

		$valuesOld = array(
			"{serviceVAL}", "{serviceVAL2}", "{domain}",
			"{external}", "{scriptjw}", "{w}", "{h}",
			"{ap10}", "{apTF}", "{apYN}", "{ap10r}", "{apTFr}", "{apYNr}"
		);

		$valuesNew = array(
			$replace['service_value'], $replace['service_value2'], $options->boardUrl,
			$external, $scriptjw, $replace['service_width'], $replace['service_height']
		);

		if ($options->EWRmedio_autoplay && $ap)
		{
			$valuesNew[] = "1";
			$valuesNew[] = "true";
			$valuesNew[] = "yes";
			$valuesNew[] = "0";
			$valuesNew[] = "false";
			$valuesNew[] = "no";
		}
		else
		{
			$valuesNew[] = "0";
			$valuesNew[] = "false";
			$valuesNew[] = "no";
			$valuesNew[] = "1";
			$valuesNew[] = "true";
			$valuesNew[] = "yes";
		}

		$replace['service_url'] = str_replace($valuesOld, $valuesNew, $replace['service_url']);
		$replace['service_embed'] = str_replace($valuesOld, $valuesNew, $replace['service_embed']);
		$replace['content_loc'] = str_replace($valuesOld, $valuesNew, '{external}/{serviceVAL}');

		return $replace;
	}
	
	public function getTotals()
	{
        $media = $this->_getDb()->fetchRow("
			SELECT COUNT(*) AS media, SUM(media_comments) AS comments, SUM(media_likes) AS likes, SUM(media_views) AS views
				FROM EWRmedio_media
		");
		
        $playlist = $this->_getDb()->fetchRow("
			SELECT COUNT(*) AS playlists, SUM(playlist_likes) AS likes, SUM(playlist_views) AS views
				FROM EWRmedio_playlists
		");
		
		return array(
			'media' => $media['media'],
			'playlists' => $playlist['playlists'],
			'comments' => $media['comments'],
			'likes' => $media['likes'] + $playlist['likes'],
			'views' => $media['views'] + $playlist['views'],
		);
	}
}