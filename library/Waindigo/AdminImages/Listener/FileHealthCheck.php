<?php

class Waindigo_AdminImages_Listener_FileHealthCheck
{

    public static function fileHealthCheck(XenForo_ControllerAdmin_Abstract $controller, array &$hashes)
    {
        $hashes = array_merge($hashes,
            array(
                'library/Waindigo/AdminImages/AdminImageHandler/Abstract.php' => 'a3e0b808964ea12d18f10c70fa5f1c3b',
                'library/Waindigo/AdminImages/AdminImageHandler/Node.php' => '1d6cb2e884fade46a80f90ff1be00ca8',
                'library/Waindigo/AdminImages/AttachmentHandler/Node.php' => 'a25745b25ea1e373e7f4c269f3a7667b',
                'library/Waindigo/AdminImages/ControllerAdmin/AdminImage.php' => 'ed71d7dd5709911af8a3334d5a8afc3b',
                'library/Waindigo/AdminImages/Extend/XenForo/Model/Attachment.php' => '6ebbda3e106898d547789ffc0c93b356',
                'library/Waindigo/AdminImages/Install/Controller.php' => '374f3c9b44cbe757bababd060fa1859d',
                'library/Waindigo/AdminImages/Listener/LoadClass.php' => '61c0c6299bff13043fc8c6d5b9b9534a',
                'library/Waindigo/AdminImages/Model/AdminImage.php' => '49895764535a1c78acd4e2e81867a67f',
                'library/Waindigo/AdminImages/Route/PrefixAdmin/Images.php' => '5fe6795fe373af655b17c8918b57bf5e',
                'library/Waindigo/AdminImages/ViewAdmin/AdminImage/Upload.php' => 'b248da0ee9127c33c109c9928fd7dfa5',
                'library/Waindigo/Install.php' => '00d8b93ea3458f18752c348a09a16c50',
                'library/Waindigo/Install/20130903.php' => '47a1ba4116a88ef6aa847285fd494803',
                'library/Waindigo/Deferred.php' => '4649953c0a44928b5e2d4a86e7d3f48a',
                'library/Waindigo/Deferred/20130725.php' => '699fb7a47bd443d53cb14f524321175a',
                'library/Waindigo/Listener/ControllerPreDispatch.php' => 'f51aeb4ef6c4acbce629188b04cd3643',
                'library/Waindigo/Listener/ControllerPreDispatch/20130828.php' => '2bc553577425f6e38bd9274923c3c7c0',
                'library/Waindigo/Listener/InitDependencies.php' => '5b755bcc0e553351c40871f4181ce5b0',
                'library/Waindigo/Listener/InitDependencies/20130730.php' => '6da1c81293332515d37b4beda8147f43',
                'library/Waindigo/Listener/LoadClass.php' => 'bfdfe90f8d484d81b05889037a4fb091',
                'library/Waindigo/Listener/LoadClass/20130625.php' => 'ed04276f4be17e707abbaf9f2710474e',
            ));
    } /* END fileHealthCheck */
}