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
        switch($templateName) {
            case 'EWRblock_latestVideos':
                $m = new MongoClient();
                $db = $m->selectDB("asiafreedom_youtubers");
                $videos = $db->videos
                    ->find(
                    )->sort(array('snippet.publishedAt'=>-1))->limit(6);
                $params['videos'] = iterator_to_array($videos);

                $fieldModel = XenForo_Model::create('AnyTV_Models_CustomUserFieldModel');
                $values = $fieldModel->getFieldValuesByFieldId('youtube_id');
                $values = $values['youtube_id'];

                $users = array();
                foreach($values as $value) {
                    $users[$value['value']] = array('user_id' => $value['user']['user_id'], 'username' => $value['user']['username']);
                }

                $params['users'] = $users;
                break;
            case 'EWRblock_AnyTVFeaturedUsers':
                $params['title'] = 'Featured Users';
                $params['featured'] = AnyTV_Helpers::getFeaturedUsers();
                break;
        }
    }
}
?>