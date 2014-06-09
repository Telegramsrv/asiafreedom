<?php

/**
 * Route prefix handler for custom field attachments in the public system.
 */
class Waindigo_CustomFields_Route_Prefix_CustomFieldAttachments implements XenForo_Route_Interface
{

    /**
     * Match a specific route for an already matched prefix.
     *
     * @see XenForo_Route_Interface::match()
     */
    public function match($routePath, Zend_Controller_Request_Http $request, XenForo_Router $router)
    {
        $action = $router->resolveActionWithIntegerParam($routePath, $request, 'field_attachment_id');
        return $router->getRouteMatch('Waindigo_CustomFields_ControllerPublic_Attachment', $action);
    } /* END match */

    /**
     * Method to build a link to the specified page/action with the provided
     * data and params.
     *
     * @see XenForo_Route_BuilderInterface
     */
    public function buildLink($originalPrefix, $outputPrefix, $action, $extension, $data, array &$extraParams)
    {
        if (is_numeric($data)) {
            $data = array(
                'field_attachment_id' => $data
            );
        }

        return XenForo_Link::buildBasicLinkWithIntegerParam($outputPrefix, $action, $extension, $data,
            'field_attachment_id');
    } /* END buildLink */
}