<?php

class Waindigo_CustomFields_Listener_FileHealthCheck
{

    public static function fileHealthCheck(XenForo_ControllerAdmin_Abstract $controller, array &$hashes)
    {
        $hashes = array_merge($hashes,
            array(
                'library/Waindigo/CustomFields/AttachmentHandler/CustomField.php' => 'ae94faa04aa9043d3fd92bd33427942b',
                'library/Waindigo/CustomFields/ControllerAdmin/Abstract.php' => '642028363bc0ccbc3cab6995ab2acae3',
                'library/Waindigo/CustomFields/ControllerAdmin/ResourceField.php' => 'b6580b3cd66eed52f1531a6cdeded684',
                'library/Waindigo/CustomFields/ControllerAdmin/SocialForumField.php' => 'a0bfda7f10db1fbf6d34603cbda16461',
                'library/Waindigo/CustomFields/ControllerAdmin/ThreadField.php' => '8e5e1d3c9f3b08d02729f54ff8e989e1',
                'library/Waindigo/CustomFields/ControllerPublic/Attachment.php' => '0fc4d98cc91e8ca21f914043dcbd9cae',
                'library/Waindigo/CustomFields/DataWriter/AbstractField.php' => '96667045934575906a4e0f30a51e6efb',
                'library/Waindigo/CustomFields/DataWriter/Attachment.php' => 'a3c21688bc7659de414734990bfa0d13',
                'library/Waindigo/CustomFields/DataWriter/ResourceField.php' => '41778406ce056719284a3d001ed594d7',
                'library/Waindigo/CustomFields/DataWriter/ResourceFieldGroup.php' => '172fa15791b36229d853fdf592095086',
                'library/Waindigo/CustomFields/DataWriter/SocialForumField.php' => 'eb45692e8fbd130688ea440fe3578744',
                'library/Waindigo/CustomFields/DataWriter/SocialForumFieldGroup.php' => '9d6e0f14f30692cfb7759085d0546676',
                'library/Waindigo/CustomFields/DataWriter/ThreadField.php' => '4faab27c5bd7fe1eb77d76668f5bcaba',
                'library/Waindigo/CustomFields/DataWriter/ThreadFieldGroup.php' => '9d13d255fa77eb600c5e98599acb9fc4',
                'library/Waindigo/CustomFields/Definition/Abstract.php' => '6b3bb9fd2a65615e7be1c7cf4860a3c5',
                'library/Waindigo/CustomFields/Definition/ResourceField.php' => '5fd647104e5fa80d80c3abe1f442b510',
                'library/Waindigo/CustomFields/Definition/SocialForumField.php' => 'b0fdb6efc4db81dbc44f66c96a8a2d55',
                'library/Waindigo/CustomFields/Definition/ThreadField.php' => '99c6cd1032b01b342adfe7ada41ce9f9',
                'library/Waindigo/CustomFields/Extend/Waindigo/Library/ControllerAdmin/Library.php' => '0aa06782b809e1c843c297a30b65be71',
                'library/Waindigo/CustomFields/Extend/Waindigo/Library/ControllerPublic/Article.php' => 'b2ac61caa428b2f807af7abba785def9',
                'library/Waindigo/CustomFields/Extend/Waindigo/Library/ControllerPublic/Library.php' => '779cce140b79f367b9efb381afc04ee5',
                'library/Waindigo/CustomFields/Extend/Waindigo/Library/DataWriter/Article.php' => '300ee751096860e9e8305a9f68121454',
                'library/Waindigo/CustomFields/Extend/Waindigo/Library/DataWriter/Library.php' => '48a54947c73983a258299a1d9c8db7ca',
                'library/Waindigo/CustomFields/Extend/Waindigo/Library/Install/Controller.php' => 'ea52f98f11d913d5d442eec06ec98c17',
                'library/Waindigo/CustomFields/Extend/Waindigo/Library/Install.php' => '95e5f59ba16c3aa43524bb3ed2c18860',
                'library/Waindigo/CustomFields/Extend/Waindigo/Library/Search/DataHandler/ArticlePage.php' => 'd365d3e5fba6c85bb81a7cd64ddd4bb6',
                'library/Waindigo/CustomFields/Extend/Waindigo/Library/ViewPublic/Article/View.php' => 'dd7aee019300e21162bac71d540af95c',
                'library/Waindigo/CustomFields/Extend/Waindigo/NoForo/Model/NoForo.php' => '8436e047c3145371b3c5d2e6a240bd6c',
                'library/Waindigo/CustomFields/Extend/Waindigo/SocialGroups/ControllerAdmin/SocialCategory.php' => '92a9e74725bee79563db7dbdb155c899',
                'library/Waindigo/CustomFields/Extend/Waindigo/SocialGroups/ControllerPublic/SocialCategory.php' => 'cf8fc689786fef316772323c7bddddad',
                'library/Waindigo/CustomFields/Extend/Waindigo/SocialGroups/ControllerPublic/SocialForum.php' => 'c568eb055f0965e8b024fd8f7a121042',
                'library/Waindigo/CustomFields/Extend/Waindigo/SocialGroups/DataWriter/SocialForum.php' => '832a6386cacb46b1de21a5ec7fbaad45',
                'library/Waindigo/CustomFields/Extend/Waindigo/SocialGroups/Install/Controller.php' => '106192564003e651c5a69f78ec176a83',
                'library/Waindigo/CustomFields/Extend/Waindigo/SocialGroups/ViewPublic/SocialForum/View.php' => '12c507c702099cc9a8c475129b8bd808',
                'library/Waindigo/CustomFields/Extend/Waindigo/UserSearch/Search/DataHandler/User.php' => '465d29d60be5489b674ec0e739a3ad64',
                'library/Waindigo/CustomFields/Extend/XenForo/ControllerAdmin/Forum.php' => '6f245705be19dae28f49323c06186204',
                'library/Waindigo/CustomFields/Extend/XenForo/ControllerAdmin/UserField.php' => 'abb0f8514f0a21730bea919281a2b7e9',
                'library/Waindigo/CustomFields/Extend/XenForo/ControllerPublic/Forum.php' => '6cfa8cb69dc413ba3ced319574e7af23',
                'library/Waindigo/CustomFields/Extend/XenForo/ControllerPublic/Search.php' => 'b5ada74180fc0605e45d1f8671833ddd',
                'library/Waindigo/CustomFields/Extend/XenForo/ControllerPublic/Thread.php' => '4fab9fac6d17ddb5f0f18b18e006a4c0',
                'library/Waindigo/CustomFields/Extend/XenForo/DataWriter/Discussion/Thread.php' => '71cd0720547fd367cfa375a8450d3633',
                'library/Waindigo/CustomFields/Extend/XenForo/DataWriter/Forum.php' => '0f6ab94f0b7d3f50e1f1fadefc2d5a47',
                'library/Waindigo/CustomFields/Extend/XenForo/DataWriter/User.php' => 'b1be463f28b4fc6d2c06c189a6df0bff',
                'library/Waindigo/CustomFields/Extend/XenForo/DataWriter/UserField.php' => '2f7c1914649853501100130eee21a32c',
                'library/Waindigo/CustomFields/Extend/XenForo/Model/AddOn.php' => '570fe019085304f7f66fd7d9c8523a37',
                'library/Waindigo/CustomFields/Extend/XenForo/Model/Phrase.php' => '938a714d587f2d7ae2054a29de89c40d',
                'library/Waindigo/CustomFields/Extend/XenForo/Model/Search.php' => 'b77cb3c80576d7e54d75f0a89e73e1ca',
                'library/Waindigo/CustomFields/Extend/XenForo/Model/Thread.php' => '0b6125e0c7928819739eceea831263fb',
                'library/Waindigo/CustomFields/Extend/XenForo/Model/ThreadRedirect.php' => 'ce83d4e60142d72c9266481c4245da4f',
                'library/Waindigo/CustomFields/Extend/XenForo/Model/UserField.php' => '5c32a93385608628c326a8b44b565254',
                'library/Waindigo/CustomFields/Extend/XenForo/Search/DataHandler/Post.php' => 'a3d8afab6419de2364a060909601e1a9',
                'library/Waindigo/CustomFields/Extend/XenForo/ViewPublic/Forum/View.php' => 'b62f2bd4a1a389e324998e448f3e54d0',
                'library/Waindigo/CustomFields/Extend/XenForo/ViewPublic/Thread/View.php' => 'c392f36764d97e7e4d0bee32edacc5e8',
                'library/Waindigo/CustomFields/Extend/XenForo/ViewPublic/Thread/ViewPosts.php' => '267050043974ff5e151fe35b1b2b7eec',
                'library/Waindigo/CustomFields/Extend/XenResource/ControllerAdmin/Category.php' => '276055505ac9c8b02b11cb8a4557c4c8',
                'library/Waindigo/CustomFields/Extend/XenResource/ControllerAdmin/Field.php' => 'd1e10e16b0af6e1f510e6c2d86ac0032',
                'library/Waindigo/CustomFields/Extend/XenResource/ControllerPublic/Resource.php' => 'f915ffd93bcb75838e1c38704cdf0034',
                'library/Waindigo/CustomFields/Extend/XenResource/DataWriter/Category.php' => '07e0bd566d524476439c1bdbf4f697bf',
                'library/Waindigo/CustomFields/Extend/XenResource/DataWriter/Resource.php' => 'c52f821971fe717b9c30dd766142c0c0',
                'library/Waindigo/CustomFields/Extend/XenResource/DataWriter/ResourceField.php' => 'cfa3c0c68ee70219e3aa86ee87cb7ca0',
                'library/Waindigo/CustomFields/Extend/XenResource/Model/ResourceField.php' => '1f4ac9bf5762547751d4300371706f1f',
                'library/Waindigo/CustomFields/Extend/XenResource/Search/DataHandler/Update.php' => '1c2e3ba3d6a17611da34e479e4baa7fe',
                'library/Waindigo/CustomFields/Extend/XenResource/ViewPublic/Resource/View.php' => 'a34d483dd426b38bbc1bb26bf5071a15',
                'library/Waindigo/CustomFields/Install/Controller.php' => 'c8bdc3807c1ae73926ae53634e40c699',
                'library/Waindigo/CustomFields/Listener/ContainerAdminParams.php' => '28961e4845cfabfc63211c4502a28da9',
                'library/Waindigo/CustomFields/Listener/FrontControllerPostView.php' => 'edc4b382ba9395f8466a0410de012ce6',
                'library/Waindigo/CustomFields/Listener/LoadClass.php' => '14810110eccf29a1fd889d995648e859',
                'library/Waindigo/CustomFields/Listener/TemplateCreate.php' => '7451d8622b3d645238d4747c490c243c',
                'library/Waindigo/CustomFields/Listener/TemplateHook.php' => '9628c4d9345d1433f7a83c17f7437c6f',
                'library/Waindigo/CustomFields/Listener/TemplatePostRender.php' => '1947129adf3555cd2053ed100b843810',
                'library/Waindigo/CustomFields/Model/AdminTemplate.php' => '7472165ccfe82424b5068c9a7897a5d6',
                'library/Waindigo/CustomFields/Model/Attachment.php' => '415c1797f2ac658ce99abd268c885f5e',
                'library/Waindigo/CustomFields/Model/ResourceField.php' => '8270f2fc4688c8ef52e6773a1cc5829c',
                'library/Waindigo/CustomFields/Model/SocialForumField.php' => 'c520fc41df91331bd40d4f7da82f684a',
                'library/Waindigo/CustomFields/Model/Template.php' => '102530b0edc5d475cd670a2fb10fc861',
                'library/Waindigo/CustomFields/Model/ThreadField.php' => 'ecac80a959b0dee8c581114fd5856bbd',
                'library/Waindigo/CustomFields/Route/Prefix/CustomFieldAttachments.php' => 'e6f3515288c0ae975ddb19216c86e3c8',
                'library/Waindigo/CustomFields/Route/PrefixAdmin/ResourceFields.php' => '5dc3220c42f41b647a0d1c7bc8e0f83a',
                'library/Waindigo/CustomFields/Route/PrefixAdmin/SocialForumFields.php' => '1af409d9fe3e6e15f6c50ea43654a914',
                'library/Waindigo/CustomFields/Route/PrefixAdmin/ThreadFields.php' => '080bcaf3ba59848ed09d653193fa6798',
                'library/Waindigo/CustomFields/ViewAdmin/ResourceField/Export.php' => '82a954239327741de90994c8d76ee7d6',
                'library/Waindigo/CustomFields/ViewAdmin/SocialForumField/Export.php' => 'b04f334ed4607b72ac7f338ea3122a36',
                'library/Waindigo/CustomFields/ViewAdmin/ThreadField/Export.php' => 'c59fc67ea52bf1d3522a56f1bce2053c',
                'library/Waindigo/CustomFields/ViewAdmin/UserField/Export.php' => '176f2e2f6aa5c3b4bd9fda90bbafa7b3',
                'library/Waindigo/CustomFields/ViewPublic/Helper/Resource.php' => 'e1175f00334b5d0d1629c46244c556c6',
                'library/Waindigo/CustomFields/ViewPublic/Helper/SocialForum.php' => 'c1458d7769c9073aa02c67f0184393fe',
                'library/Waindigo/CustomFields/ViewPublic/Helper/Thread.php' => '393ce3de9a7f183f933987719ed945f0',
                'library/Waindigo/Install.php' => '00d8b93ea3458f18752c348a09a16c50',
                'library/Waindigo/Install/20140226.php' => 'f841e05a670aa3ade0dc9aa01e7a0a15',
                'library/Waindigo/Deferred.php' => '4649953c0a44928b5e2d4a86e7d3f48a',
                'library/Waindigo/Deferred/20130725.php' => '699fb7a47bd443d53cb14f524321175a',
                'library/Waindigo/Listener/ControllerPreDispatch.php' => 'f51aeb4ef6c4acbce629188b04cd3643',
                'library/Waindigo/Listener/ControllerPreDispatch/20140326.php' => 'aeb6464a3fbb3179dea259683b4ec1a1',
                'library/Waindigo/Listener/InitDependencies.php' => '5b755bcc0e553351c40871f4181ce5b0',
                'library/Waindigo/Listener/InitDependencies/20140401.php' => 'ad2422e10f1d880f569601c785c0b8d2',
                'library/Waindigo/Listener/Template.php' => 'b52cba9c298d9702b4536146d3ac4312',
                'library/Waindigo/Listener/Template/20140101.php' => '2522395ad7d95866de2b87576a60e9f6',
                'library/Waindigo/Listener/FrontControllerPostView.php' => 'c00cebbeb74816a0fa2d21932631d2d8',
                'library/Waindigo/Listener/FrontControllerPostView/20130522.php' => '3924dd98f601f180b6b92c7cb8f1f57e',
                'library/Waindigo/Listener/LoadClass.php' => 'bfdfe90f8d484d81b05889037a4fb091',
                'library/Waindigo/Listener/LoadClass/20131003.php' => 'e3cd73a6c98c045050a307426997d806',
                'library/Waindigo/Listener/TemplateCreate.php' => 'db5c0d5eb8c65b1840dd437e5cca69d6',
                'library/Waindigo/Listener/TemplateCreate/20130522.php' => 'd382f6f3a2a4e8c06d665b6af1365808',
                'library/Waindigo/Listener/TemplateHook.php' => '37c6a882bfb9d790801c94051fe3eb0d',
                'library/Waindigo/Listener/TemplateHook/20130522.php' => '050322445ef811663bf40755d772947f',
                'library/Waindigo/Listener/TemplatePostRender.php' => '73d70bb432c859375b1b8c05ffd8d027',
                'library/Waindigo/Listener/TemplatePostRender/20130522.php' => '6309fdcf4496771bb7050ad03d91593e',
            ));
    } /* END fileHealthCheck */
}