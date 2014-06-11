<?php

/**
 * Route prefix handler for user field categories in the admin control panel.
 */
class Waindigo_UserFieldCats_Route_PrefixAdmin_UserFieldCategories implements XenForo_Route_Interface
{
    /**
     * Match a specific route for an already matched prefix.
     *
     * @see XenForo_Route_Interface::match()
     */
    public function match($routePath, Zend_Controller_Request_Http $request, XenForo_Router $router)
    {
        $action = $router->resolveActionWithIntegerParam($routePath, $request, 'user_field_category_id');
        $action = $router->resolveActionAsPageNumber($action, $request);
        return $router->getRouteMatch('Waindigo_UserFieldCats_ControllerAdmin_UserFieldCategory', $action, 'userFieldCategories');
    } /* END match */

    /**
     * Method to build a link to the specified page/action with the provided
     * data and params.
     *
     * @see XenForo_Route_BuilderInterface
     */
    public function buildLink($originalPrefix, $outputPrefix, $action, $extension, $data, array &$extraParams)
    {
        return XenForo_Link::buildBasicLinkWithIntegerParam($outputPrefix, $action, $extension, $data, 'user_field_category_id', 'title');
    } /* END buildLink */
}