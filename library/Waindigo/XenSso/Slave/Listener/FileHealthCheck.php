<?php

class Waindigo_XenSso_Slave_Listener_FileHealthCheck
{

    public static function fileHealthCheck(XenForo_ControllerAdmin_Abstract $controller, array &$hashes)
    {
        $hashes = array_merge($hashes,
            array(
                'library/Waindigo/XenSso/Slave/ControllerAdmin/Sync.php' => 'e647463fc0878a720ac3b1cfa4348276',
                'library/Waindigo/XenSso/Slave/ControllerPublic/Consumer.php' => 'd27cffbeca9a0b9e549ff2725d1a1c3f',
                'library/Waindigo/XenSso/Slave/ControllerPublic/Sync.php' => '2e604c50d54fd515a4f1ba8607c60c9f',
                'library/Waindigo/XenSso/Slave/DataWriter/User.php' => '3f61d2b1e63811cf5ab5f998409f9a8f',
                'library/Waindigo/XenSso/Slave/Extend/XenForo/ControllerAdmin/User.php' => '41c3151ce550c0146976d68c2c3d231b',
                'library/Waindigo/XenSso/Slave/Extend/XenForo/ControllerPublic/AccountConfirmation.php' => 'ff1af0b717da8862545b09aae96bccf9',
                'library/Waindigo/XenSso/Slave/Extend/XenForo/ControllerPublic/Login.php' => 'ac07a471b44f818ec2dac403ba27356a',
                'library/Waindigo/XenSso/Slave/Extend/XenForo/ControllerPublic/Register.php' => '468699d9dea1a63104a72f66f198a665',
                'library/Waindigo/XenSso/Slave/Extend/XenForo/DataWriter/User.php' => 'a2a088ea08511c053c91de8ef9de5c84',
                'library/Waindigo/XenSso/Slave/Install/Controller.php' => '90258cc39a2f5a6f2281c6e9dc7b7f0d',
                'library/Waindigo/XenSso/Slave/Listener/ControllerPreDispatch.php' => 'f4dae0b09416d5b7ea13b157a291f48e',
                'library/Waindigo/XenSso/Slave/Listener/LoadClass.php' => '3f69cdfc228040eb6e853b18efe6c68a',
                'library/Waindigo/XenSso/Slave/Listener/TemplateHook.php' => '74c648cfab129d83d8c8081557a8473c',
                'library/Waindigo/XenSso/Slave/Model/User.php' => '46831a01df0cc43454f211d9dfebafc5',
                'library/Waindigo/XenSso/Slave/OpenId/Consumer.php' => '05fb12a2eb2164818fb2b2624a4ca832',
                'library/Waindigo/XenSso/Slave/Route/Prefix/Consumer.php' => '52c56bf873af4a01bd686bbe4639af84',
                'library/Waindigo/XenSso/Slave/Route/Prefix/Sync.php' => 'c5cd790da4ccb55c35393a1437e06154',
                'library/Waindigo/XenSso/Slave/Route/PrefixAdmin/Sync.php' => '0c6610215e4dcb133705196e47dd880b',
                'library/Waindigo/XenSso/Slave/Session.php' => '3d2bda1c6665f8e8c959ae92a40e3c80',
                'library/Waindigo/XenSso/Slave/Sync.php' => 'c4f59eec18d818245d955e3c3686a5e4',
                'library/Waindigo/XenSso/Shared/Helpers.php' => 'b76ed089ab1756486076d6abda97a411',
                'library/Waindigo/XenSso/Shared/Model/Auth.php' => 'd620111660e292720db4532077746f2a',
                'library/Waindigo/XenSso/Shared/Secure.php' => '55d9a4827e27efb0c3e220bb1e99cf63',
                'library/Waindigo/XenSso/Shared/User.php' => '3910304c3886ddca7d6acb24bd955535',
                'library/Zend/Session/Abstract.php' => '8beba42648054c62916a413129865524',
                'library/Zend/Session/Exception.php' => '2b557ec931a2eb42fdb9510ad1b277ad',
                'library/Zend/Session/Namespace.php' => 'd86ab70579fc20d556402fc794f688fe',
                'library/Zend/Session/SaveHandler/DbTable.php' => '9844987dc0a1c2b45d1c690abe935de4',
                'library/Zend/Session/SaveHandler/Exception.php' => '4dd3a01ea8fba9fe46102014c70a40b3',
                'library/Zend/Session/SaveHandler/Interface.php' => 'f27f2b6f050869a7f7aef5a5385a8e05',
                'library/Zend/Session/Validator/Abstract.php' => '2e44236ea90f47f05b7f4a243f0525c2',
                'library/Zend/Session/Validator/HttpUserAgent.php' => '5a9b4f64366391e21668ee9b1e2a61ac',
                'library/Zend/Session/Validator/Interface.php' => 'bbbb5dfd24df96e4d8d115bc62aa1b7c',
                'library/Waindigo/Install.php' => '00d8b93ea3458f18752c348a09a16c50',
                'library/Waindigo/Install/20140226.php' => 'f841e05a670aa3ade0dc9aa01e7a0a15',
                'library/Waindigo/Deferred.php' => '4649953c0a44928b5e2d4a86e7d3f48a',
                'library/Waindigo/Deferred/20130725.php' => '699fb7a47bd443d53cb14f524321175a',
                'library/Waindigo/Listener/ControllerPreDispatch.php' => 'f51aeb4ef6c4acbce629188b04cd3643',
                'library/Waindigo/Listener/ControllerPreDispatch/20131003.php' => '7ad68f6ed984c7123cacf75e1093ff04',
                'library/Waindigo/Listener/InitDependencies.php' => '5b755bcc0e553351c40871f4181ce5b0',
                'library/Waindigo/Listener/InitDependencies/20140101.php' => 'b7745aba37ee138e7d6af5806599f21a',
                'library/Waindigo/Listener/LoadClass.php' => 'bfdfe90f8d484d81b05889037a4fb091',
                'library/Waindigo/Listener/LoadClass/20131003.php' => 'e3cd73a6c98c045050a307426997d806',
                'library/Waindigo/Listener/Template.php' => 'b52cba9c298d9702b4536146d3ac4312',
                'library/Waindigo/Listener/Template/20140101.php' => '2522395ad7d95866de2b87576a60e9f6',
                'library/Waindigo/Listener/TemplateHook.php' => '37c6a882bfb9d790801c94051fe3eb0d',
                'library/Waindigo/Listener/TemplateHook/20130522.php' => '050322445ef811663bf40755d772947f',
            ));
    } /* END fileHealthCheck */
}