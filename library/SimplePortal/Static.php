<?php

class SimplePortal_Static
{
    CONST PORTAL_PREVIEW_ENDSTRING = '<!-- end -->';


    CONST MOD_ACTION_PROMOTE = 'portal_add';
    CONST MOD_ACTION_DEMOTE = 'portal_remove';
    CONST MOD_ACTION_EDIT = 'portal_edit';



    /**
     * @var SimplePortal_Model_PortalItem
     */
    static $itemModel = null;

	public static function canManageCategories()
	{
        return XenForo_Visitor::getInstance()->hasPermission('simple_portal','createCategory');
	}

	public static function option($optionName)
	{
		switch ($optionName) {
			case 'perPage':
				return XenForo_Application::getOptions()->portal_itemsPerPage;
			case 'charlimit':
				return XenForo_Application::getOptions()->portal_charlimit;
			case 'newsSystem':
				return XenForo_Application::getOptions()->portal_use_as_newspage;
            case 'autopromotenodes':
                return XenForo_Application::getOptions()->portal_autopromoteforums;
            case 'hometabText':
                return XenForo_Application::getOptions()->portal_hometab_text;
            case 'hometabPos':
                return XenForo_Application::getOptions()->portal_hometab_position;
            case 'defaultSidebar':
                return XenForo_Application::getOptions()->portal_include_default_sidebar;
		}
		return false;
	}


	/**
	 * @return SimplePortal_Model_PortalItem
	 */
	public static function getItemModel()
	{
		if (! self::$itemModel) {
			self::$itemModel = XenForo_Model::create('SimplePortal_Model_PortalItem');
		}
		return self::$itemModel;
	}


	public static function insertNavtab(array &$extraTabs, $selectedTabId)
	{
		$extraTabs['portal'] = array(
			'title' => self::option('hometabText'),
			'href' => XenForo_Link::buildPublicLink('full:portal'),
			'position' => self::option('hometabPos'),
            'linksTemplate' => 'el_portal_navbar',
            'canManage' => self::canManageCategories(),
            'canCreate' => self::hasPermission('useCreateNew')
		);
	}

    public static function hasPermission($perm)
    {
        // we have to keep "simple_portal" prefix
        return XenForo_Visitor::getInstance()->hasPermission('simple_portal', $perm);
    }


	public static function publicContainerParams(array &$params, XenForo_Dependencies_Abstract $dependencies)
	{
        if (self::option('hometabPos') == 'home'){
            $params['showHomeLink'] = false;
        }
	}


    public static function templateCreate(&$templateName, array &$params, XenForo_Template_Abstract $template){
        $template->preloadTemplate('el_portal_navbar');
    }
}