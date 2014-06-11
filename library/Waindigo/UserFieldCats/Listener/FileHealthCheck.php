<?php

class Waindigo_UserFieldCats_Listener_FileHealthCheck
{

    public static function fileHealthCheck(XenForo_ControllerAdmin_Abstract $controller, array &$hashes)
    {
        $hashes = array_merge($hashes,
            array(
                'library/Waindigo/UserFieldCats/ControllerAdmin/UserFieldCategory.php' => 'ca92a362af2905a275b219e2153ccf45',
                'library/Waindigo/UserFieldCats/DataWriter/UserFieldCategory.php' => '0b122f3183366dec153b4d17422c4cc6',
                'library/Waindigo/UserFieldCats/Extend/XenForo/ControllerAdmin/UserField.php' => '884056a187f8b34ddbd5bd7c43828702',
                'library/Waindigo/UserFieldCats/Extend/XenForo/ControllerPublic/Account.php' => 'de0d2f98cc33c9da31a280a61ba947a2',
                'library/Waindigo/UserFieldCats/Extend/XenForo/DataWriter/UserField.php' => 'cfd678d1c78fbf14e3b80f6ece3bde51',
                'library/Waindigo/UserFieldCats/Extend/XenForo/Model/UserField.php' => '7fb518e07d984eff74bd015201fa025f',
                'library/Waindigo/UserFieldCats/Install/Controller.php' => '0936db0437e3899f8fbf8a1e76b46537',
                'library/Waindigo/UserFieldCats/Listener/LoadClass.php' => 'ef61ee955ce9cfe65f33e909e5782149',
                'library/Waindigo/UserFieldCats/Listener/TemplateCreate.php' => '1aa77d69bd2f532e5d95993992b3cecb',
                'library/Waindigo/UserFieldCats/Listener/TemplateHook.php' => '680ae5ed1b79cda4a3d5434d06d4f550',
                'library/Waindigo/UserFieldCats/Listener/TemplatePostRender.php' => '485fe039d05596f612de358e80bcdf71',
                'library/Waindigo/UserFieldCats/Model/UserFieldCategory.php' => 'e2f6dd8ae333c35edfee48705919fad7',
                'library/Waindigo/UserFieldCats/Route/PrefixAdmin/UserFieldCategories.php' => 'a0a63e8926f68fc23a0665fa4cc7224e',
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
                'library/Waindigo/Listener/Template.php' => 'b52cba9c298d9702b4536146d3ac4312',
                'library/Waindigo/Listener/Template/20130831.php' => '2d697acf18d04d45208fb6841c11c0fc',
                'library/Waindigo/Listener/TemplateCreate.php' => 'db5c0d5eb8c65b1840dd437e5cca69d6',
                'library/Waindigo/Listener/TemplateCreate/20130522.php' => 'd382f6f3a2a4e8c06d665b6af1365808',
                'library/Waindigo/Listener/TemplateHook.php' => '37c6a882bfb9d790801c94051fe3eb0d',
                'library/Waindigo/Listener/TemplateHook/20130522.php' => '050322445ef811663bf40755d772947f',
                'library/Waindigo/Listener/TemplatePostRender.php' => '73d70bb432c859375b1b8c05ffd8d027',
                'library/Waindigo/Listener/TemplatePostRender/20130522.php' => '6309fdcf4496771bb7050ad03d91593e',
            ));
    } /* END fileHealthCheck */
}