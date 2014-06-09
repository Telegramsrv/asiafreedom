<?php

/**
 *
 * @see XenForo_ControllerPublic_Account
 */
class Waindigo_UserFieldCats_Extend_XenForo_ControllerPublic_Account extends XFCP_Waindigo_UserFieldCats_Extend_XenForo_ControllerPublic_Account
{

    /**
     *
     * @see XenForo_ControllerPublic_Account::actionUserFieldCategory()
     */
    public function actionUserFieldCategory()
    {
        $visitor = XenForo_Visitor::getInstance()->toArray();

        $userFieldCategoryId = $this->_input->filterSingle('user_field_category_id', XenForo_Input::UINT);

        /* @var $userFieldCategoryModel Waindigo_UserFieldCats_Model_UserFieldCategory */
        $userFieldCategoryModel = $this->getModelFromCache('Waindigo_UserFieldCats_Model_UserFieldCategory');

        $userFieldCategory = $userFieldCategoryModel->getUserFieldCategoryById($userFieldCategoryId);

        if (!$userFieldCategory) {
            return $this->responseNoPermission();
        }

        /* @var $userModel XenForo_Model_User */
        $userModel = $this->_getUserModel();

        if ($userFieldCategory['user_group_ids']) {
            $userGroupIds = explode(',', $userFieldCategory['user_group_ids']);

            if ($userGroupIds && !$userModel->isMemberOfUserGroup($visitor, $userGroupIds)) {
                return $this->responseNoPermission();
            }
        }

        $customFields = $this->_getFieldModel()->getUserFields(
            array(
                'display_group' => 'custom',
                'user_field_category_id' => $userFieldCategoryId
            ), array(
                'valueUserId' => $visitor['user_id']
            ));

        $viewParams = array(
            'userFieldCategory' => $userFieldCategory,
            'customFields' => $this->_getFieldModel()->prepareUserFields($customFields, true)
        );

        return $this->_getWrapper('account', 'userFieldCategory' . $userFieldCategoryId,
            $this->responseView('Waindigo_UserFieldCats_ViewPublic_Account_UserFieldCategory',
                'waindigo_account_user_field_category_userfieldcats', $viewParams));
    } /* END actionUserFieldCategory */

    /**
     *
     * @return XenForo_ControllerResponse_Redirect
     */
    public function actionUserFieldCategorySave()
    {
        $this->_assertPostOnly();

        $visitor = XenForo_Visitor::getInstance()->toArray();

        $userFieldCategoryId = $this->_input->filterSingle('user_field_category_id', XenForo_Input::UINT);

        /* @var $userFieldCategoryModel Waindigo_UserFieldCats_Model_UserFieldCategory */
        $userFieldCategoryModel = $this->getModelFromCache('Waindigo_UserFieldCats_Model_UserFieldCategory');

        $userFieldCategory = $userFieldCategoryModel->getUserFieldCategoryById($userFieldCategoryId);

        if (!$userFieldCategory) {
            return $this->responseNoPermission();
        }

        /* @var $userModel XenForo_Model_User */
        $userModel = $this->_getUserModel();

        if ($userFieldCategory['user_group_ids']) {
            $userGroupIds = explode(',', $userFieldCategory['user_group_ids']);

            if ($userGroupIds && !$userModel->isMemberOfUserGroup($visitor, $userGroupIds)) {
                return $this->responseNoPermission();
            }
        }

        $customFields = $this->_input->filterSingle('custom_fields', XenForo_Input::ARRAY_SIMPLE);
        $customFieldsShown = $this->_input->filterSingle('custom_fields_shown', XenForo_Input::STRING,
            array(
                'array' => true
            ));

        if(isset($customFields['youtube_id'])) {
            $youtube = strlen($customFields['youtube_id']) ? $customFields['youtube_id'] : '';
            $youtube = explode('/', $youtube);
            if(!is_array($youtube)) {
                $youtube = array($youtube);
            }
            $channelId = array();
            foreach ($youtube as $key => $value) {
                if($value && strlen($value)) {
                    $channelId[] = $value;
                }
            }

            $channelId = count($channelId) ? $channelId[count($channelId)-1] : "";

            $customFields['youtubeUploads'] = "";
            $customFieldsShown[] = 'youtubeUploads';
            if(strlen($channelId)) {
                $url = 'https://gdata.youtube.com/feeds/api/users/'.$channelId.'/uploads';
                $params = array( 'v' => 2, 'alt' => 'json' );
                $res = $this->curlGet($url, $params, false);

                $channelId = $res['feed']['entry'][0]['media$group']['yt$uploaderId']['$t'];
                $customFields['youtube_id'] = $channelId;

                $playlist = "";
                $url = 'https://www.googleapis.com/youtube/v3/channels';//?id='.$channelId.'&key=AIzaSyAP14m25_1uScfmZObKqRI4lCwveb9E8Vk&part=contentDetails';
                $params = array(
                    'id'    => $channelId,
                    'key'   => 'AIzaSyAP14m25_1uScfmZObKqRI4lCwveb9E8Vk',
                    'part'  => 'contentDetails'
                );
                $res = $this->curlGet($url, $params);
                if(isset($res['items'],     $res['items'][0],   $res['items'][0]['contentDetails'],
                        $res['items'][0]['contentDetails']['relatedPlaylists'], $res['items'][0]['contentDetails']['relatedPlaylists']['uploads'])) {
                    $playlist = $res['items'][0]['contentDetails']['relatedPlaylists']['uploads'];
                }

                $customFields['youtubeUploads'] = $playlist;
                $customFieldsShown[] = 'youtubeUploads';
            }
        }


        $writer = XenForo_DataWriter::create('XenForo_DataWriter_User');
        $writer->setExistingData(XenForo_Visitor::getUserId());
        $writer->setCustomFields($customFields, $customFieldsShown);

        $writer->preSave();

        if ($dwErrors = $writer->getErrors()) {
            return $this->responseError($dwErrors);
        }

        $writer->save();

        return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
            XenForo_Link::buildPublicLink('account/user-field-category', '',
                array(
                    'user_field_category_id' => $userFieldCategoryId
                )));
    } /* END actionUserFieldCategorySave */

    /**
     *
     * @see XenForo_ControllerPublic_Account::_getWrapper()
     */
    protected function _getWrapper($selectedGroup, $selectedLink, XenForo_ControllerResponse_View $subView)
    {
        $wrapper = parent::_getWrapper($selectedGroup, $selectedLink, $subView);

        if ($wrapper instanceof XenForo_ControllerResponse_View) {
            /* @var $userFieldCategoryModel Waindigo_UserFieldCats_Model_UserFieldCategory */
            $userFieldCategoryModel = $this->getModelFromCache('Waindigo_UserFieldCats_Model_UserFieldCategory');

            $visitor = XenForo_Visitor::getInstance()->toArray();

            $wrapper->params['userFieldCategories'] = $userFieldCategoryModel->getViewableUserFieldCategories($visitor);
        }

        return $wrapper;
    } /* END _getWrapper */
}