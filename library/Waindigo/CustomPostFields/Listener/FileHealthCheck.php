<?php

class Waindigo_CustomPostFields_Listener_FileHealthCheck
{

    public static function fileHealthCheck(XenForo_ControllerAdmin_Abstract $controller, array &$hashes)
    {
        $hashes = array_merge($hashes,
            array(
                'library/Waindigo/CustomPostFields/ControllerAdmin/PostField.php' => 'f5b25f4e1079e3f1d56f8892bf69731e',
                'library/Waindigo/CustomPostFields/DataWriter/PostField.php' => '7445b8386f426bbb283d39823af27cc0',
                'library/Waindigo/CustomPostFields/DataWriter/PostFieldGroup.php' => 'f9d20aa9f04069db5912ad9734f584be',
                'library/Waindigo/CustomPostFields/Definition/PostField.php' => '9d74bb4210fde6d2a26b3252dfaf89a1',
                'library/Waindigo/CustomPostFields/Extend/Waindigo/Library/ControllerAdmin/Library.php' => '014e40606721b1cdf6f01bdbc52783f3',
                'library/Waindigo/CustomPostFields/Extend/Waindigo/Library/ControllerPublic/Article.php' => '67c98874a4149271f7ae892df43942f1',
                'library/Waindigo/CustomPostFields/Extend/Waindigo/Library/ControllerPublic/ArticlePage.php' => '950e43f04d1e35458cf8a58679d17b14',
                'library/Waindigo/CustomPostFields/Extend/Waindigo/Library/ControllerPublic/Library.php' => '25da3b2cfba45e8edf1cf881a3f2c91f',
                'library/Waindigo/CustomPostFields/Extend/Waindigo/Library/DataWriter/ArticlePage.php' => '6e0f09777e1669877f0cfe5069ac70fe',
                'library/Waindigo/CustomPostFields/Extend/Waindigo/Library/DataWriter/Library.php' => '8baf568f09eae737890f2ab5cd79effc',
                'library/Waindigo/CustomPostFields/Extend/Waindigo/Library/Install/Controller.php' => 'e47871bd0c3b72b543d6fadd9030827d',
                'library/Waindigo/CustomPostFields/Extend/XenForo/ControllerAdmin/Forum.php' => '08530ffe6c69cab79f802b2f503217a8',
                'library/Waindigo/CustomPostFields/Extend/XenForo/ControllerPublic/Forum.php' => '9f63f5825b4e4202d5e93297a7e28c53',
                'library/Waindigo/CustomPostFields/Extend/XenForo/ControllerPublic/Post.php' => 'db898af4162fd59da2ae9c817e2fae44',
                'library/Waindigo/CustomPostFields/Extend/XenForo/ControllerPublic/Thread.php' => '7df8e1a923258bbf59d71dd761509b3c',
                'library/Waindigo/CustomPostFields/Extend/XenForo/DataWriter/DiscussionMessage/Post.php' => 'a265e9fbfcb3914066b938b88b8b356d',
                'library/Waindigo/CustomPostFields/Extend/XenForo/DataWriter/Forum.php' => '5eca835293e181f5a68d2d3d7154a380',
                'library/Waindigo/CustomPostFields/Extend/XenForo/Model/AddOn.php' => 'b912f024d6060cbfbdf07644e86bc9ea',
                'library/Waindigo/CustomPostFields/Extend/XenForo/Search/DataHandler/Post.php' => 'b4ff0f1ac42fb8e581f292cce601476d',
                'library/Waindigo/CustomPostFields/Install/Controller.php' => '86d8357a758c2860348237c6da437599',
                'library/Waindigo/CustomPostFields/Listener/LoadClass.php' => '621f1be9fe2ac55e1f978e912a379ae3',
                'library/Waindigo/CustomPostFields/Listener/TemplateHook.php' => 'd7585355c1093e70826117c43933ae74',
                'library/Waindigo/CustomPostFields/Listener/TemplatePostRender.php' => 'e3f9e7a6a31ebfc78fb1d42b6082d4e6',
                'library/Waindigo/CustomPostFields/Model/PostField.php' => '334be170d265a059e0f78b120f4f3756',
                'library/Waindigo/CustomPostFields/Route/PrefixAdmin/PostFields.php' => 'c9e163880b1a3e67c2d97e42ee8346da',
                'library/Waindigo/CustomPostFields/ViewAdmin/PostField/Export.php' => '08c1e160f3490b9bf9f744d2713dca60',
                'library/Waindigo/Install.php' => '00d8b93ea3458f18752c348a09a16c50',
                'library/Waindigo/Install/20140226.php' => 'f841e05a670aa3ade0dc9aa01e7a0a15',
                'library/Waindigo/Deferred.php' => '4649953c0a44928b5e2d4a86e7d3f48a',
                'library/Waindigo/Deferred/20130725.php' => '699fb7a47bd443d53cb14f524321175a',
                'library/Waindigo/Listener/ControllerPreDispatch.php' => 'f51aeb4ef6c4acbce629188b04cd3643',
                'library/Waindigo/Listener/ControllerPreDispatch/20140326.php' => 'aeb6464a3fbb3179dea259683b4ec1a1',
                'library/Waindigo/Listener/InitDependencies.php' => '5b755bcc0e553351c40871f4181ce5b0',
                'library/Waindigo/Listener/InitDependencies/20140401.php' => 'ad2422e10f1d880f569601c785c0b8d2',
                'library/Waindigo/Listener/LoadClass.php' => '1f9470e8129c18ec6ffea38a1a0b427e',
                'library/Waindigo/Listener/LoadClass/20131003.php' => 'e3cd73a6c98c045050a307426997d806',
                'library/Waindigo/Listener/Template.php' => 'b52cba9c298d9702b4536146d3ac4312',
                'library/Waindigo/Listener/Template/20140101.php' => '2522395ad7d95866de2b87576a60e9f6',
                'library/Waindigo/Listener/TemplateHook.php' => '37c6a882bfb9d790801c94051fe3eb0d',
                'library/Waindigo/Listener/TemplateHook/20130522.php' => '050322445ef811663bf40755d772947f',
                'library/Waindigo/Listener/TemplatePostRender.php' => '73d70bb432c859375b1b8c05ffd8d027',
                'library/Waindigo/Listener/TemplatePostRender/20130522.php' => '6309fdcf4496771bb7050ad03d91593e',
            ));
    } /* END fileHealthCheck */
}