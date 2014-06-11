<?php

class Waindigo_CustomFields_Listener_FrontControllerPostView extends Waindigo_Listener_FrontControllerPostView
{

    /**
     *
     * @see Waindigo_Listener_Template::run()
     */
    public function run()
    {
        switch ($this->_routePath) {
            case 'user-fields':
                $this->_userFields();
                break;
            default:
                if (preg_match("#^user-fields/(.*)/edit#", $this->_routePath, $matches)) {
                    $this->_userFieldsEdit($matches[1]);
                }
        }
        return parent::run();
    } /* END run */

    public static function frontControllerPostView(XenForo_FrontController $fc, &$output)
    {
        $frontControllerPostView = new Waindigo_CustomFields_Listener_FrontControllerPostView($fc, $output);
        $output = $frontControllerPostView->_run();
    } /* END frontControllerPostView */

    protected function _userFields()
    {
        $this->_appendTemplateAfterTopCtrl('waindigo_user_fields_topctrl_import_customfields');
    } /* END _userFields */

    /**
     *
     * @param string $fieldId
     */
    protected function _userFieldsEdit($fieldId)
    {
        $this->_assertResponseCode(200);
        $viewParams['field'] = array(
            'field_id' => $fieldId
        );
        $this->_appendTemplateAfterTopCtrl('waindigo_user_fields_topctrl_export_customfields', $viewParams);
    } /* END _userFieldsEdit */
}