<?php
//Our class name
class AnyTV_Listener
{
    /**
    * Listen to the "init_dependencies" code event.
    *
    * @param XenForo_Dependencies_Abstract $dependencies
    * @param array $data
    */
    public static function init(XenForo_Dependencies_Abstract $dependencies, array $data)
    {
        //Get the static variable $helperCallbacks and add a new item in the array.
        XenForo_Template_Helper_Core::$helperCallbacks += array(
            'getarray' => array('AnyTV_Helpers', 'helperArrayGet'),
            'cachebust' => array('AnyTV_Helpers', 'cacheBust')
        );
    }

    public static function templateCreate(&$templateName, array &$params, XenForo_Template_Abstract $template)
    {
        $options = $options = XenForo_Application::get('options');
        $featuredVideosSkip = 0;

        $params['featuredVideosNextPage'] = 2;
        $params['featuredVideosPrevPage'] = 1;

        if(isset($_GET['featuredVideosPage'])) {
            $featuredVideosSkip = ($_GET['featuredVideosPage']-1)*12;
            $params['featuredVideosNextPage'] = $_GET['featuredVideosPage']+1;
            $params['featuredVideosPrevPage'] = $_GET['featuredVideosPage'] > 1
                ? $_GET['featuredVideosPage'] - 1
                : 1;
        }

        switch($templateName) {
            case 'EWRblock_sliderNavigation':
                $params['joinUs'] = $options->joinUsLink;
                break;
            case 'EWRblock_latestVideos':
                $host = XenForo_Application::get('db')->getConfig()['host'];
                $m = new MongoClient($host); // connect
                $db = $m->selectDB("asiafreedom_youtubers");
                $videos = $db->videos
                    ->find(
                    )->sort(
                        array('snippet.publishedAt'=>-1)
                    )->limit(6);
                $params['videos'] = iterator_to_array($videos);

                $fieldModel = XenForo_Model::create('AnyTV_Models_CustomUserFieldModel');
                $values = $fieldModel->getFieldValuesByFieldId('youtube_id');
                $values = $values['youtube_id'];

                $users = array();
                foreach($values as $value) {
                    $users[$value['value']] = array(
                        'user_id' => $value['user']['user_id'],
                        'username' => $value['user']['username']
                    );
                }

                $params['users'] = $users;
                break;
            case 'EWRblock_FaceBook':
                $params['options'] = array('profile' => $options->facebookLink);
                break;
            case 'EWRblock_AnyTVFeaturedUsers':
                $params['title'] = new XenForo_Phrase('featured_users');
                $params['featured'] = AnyTV_Helpers::getFeaturedUsers();
                break;
            case 'EWRblock_AnyTVTabbedBlocks':
                $host = XenForo_Application::get('db')->getConfig()['host'];
                $m = new MongoClient($host); // connect
                $db = $m->selectDB("asiafreedom_youtubers");
                $videos = $db->videos
                    ->find(
                    )->sort(array('snippet.publishedAt'=>-1))->limit(12);
                $videosPage = 1;
                $params['prevVideosPage'] = 1;
                $params['nextVideosPage'] = 2;
                if(isset($_GET['videosPage'])) {
                    $params['prevVideosPage'] = $_GET['videosPage'] > 1
                        ? ($_GET['videosPage']-1)
                        : 1;
                    $params['nextVideosPage'] = $_GET['videosPage']+1;
                    $videos = $videos->skip(($_GET['videosPage']-1)*12);
                }

                $params['videos'] = iterator_to_array($videos);

                $fieldModel = XenForo_Model::create('AnyTV_Models_CustomUserFieldModel');
                $values = $fieldModel->getFieldValuesByFieldId('youtube_id');
                $values = $values['youtube_id'];

                $users = array();
                foreach($values as $value) {
                    $users[$value['value']] = array(
                        'user_id' => $value['user']['user_id'],
                        'username' => $value['user']['username']
                    );
                }

                $options = XenForo_Application::get('options');
                $games = AnyTV_Games::getFeatured();
                $fieldsModel =  XenForo_Model::create('AnyTV_Models_CustomUserFieldModel');
                $params['youtubers'] = $fieldsModel->getFieldValuesByFieldId('youtube_id');
                $params['featured'] = AnyTV_Helpers::getFeaturedUsers();
                $params['featuredVideos'] = AnyTV_Helpers::getFeaturedVideos($featuredVideosSkip);
                $params['games'] = $games;
                $params['users'] = $users;
                $params['featuredVideosHasNext'] = AnyTV_Helpers::hasNextFeatured($featuredVideosSkip+count($params['featuredVideos']));
                $params['blocks'] = array(
                    array('id' => 'mediaRecent', 'phrase' => new XenForo_Phrase('latest_videos')),
                    array('id' => 'featuredVideos', 'phrase' => new XenForo_Phrase('featured_videos')),
                    array('id' => 'youtubersBlock', 'phrase' => new XenForo_Phrase('youtubers')),
                    array('id' => 'gamesBlock', 'phrase' => new XenForo_Phrase('games'))
                );
                break;
        }
    }
}
?>
