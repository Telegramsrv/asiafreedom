<?php

class Waindigo_XenSso_Slave_Listener_TemplateHook extends Waindigo_Listener_TemplateHook
{

    protected function _getHooks()
    {
        return array(
            'page_container_head'
        );
    } /* END _getHooks */

    public static function templateHook($hookName, &$contents, array $hookParams, XenForo_Template_Abstract $template)
    {
        $templateHook = new Waindigo_XenSso_Slave_Listener_TemplateHook($hookName, $contents, $hookParams, $template);
        $contents = $templateHook->run();
    } /* END templateHook */

    protected function _pageContainerHead()
    {
        // Get info about visitor and session, and get XF options
        $visitor = XenForo_Visitor::getInstance();
        $options = XenForo_Application::get('options');

        // Set default action, in case neither of the following statements
        // trigger
        $include = false;

        // Check if we have an auth key
        // in case we do we need to try to transparently login on the master
        if (Waindigo_XenSso_Slave_Listener_ControllerPreDispatch::getAuthKey() and $visitor->user_id > 0) {
            // Set and encrypt auth data
            $authData = Waindigo_XenSso_Shared_Secure::encrypt(
                array(
                    'email' => $visitor->email,
                    'key' => Waindigo_XenSso_Slave_Listener_ControllerPreDispatch::getAuthKey()
                ));

            // Append javascript to contents
            $contents .= '<script>
				var xensso_auth_data = "' . $authData . '";
				var xensso_master_url = "' . $options->waindigo_xenSso_slave_masterUrl . '";
			</script>';

            // Update include variable so it knows we added content
            $include = true;
        }

        // Check if the user is not logged in, and if so, include javascript to
        // login transparently
        if ($visitor->user_id == 0) {

            // Get visitor session
            $session = XenForo_Application::get('session');
            $attempt = isset($_COOKIE['attemptLogin']) ? $_COOKIE['attemptLogin'] : false;

            // Check if this is a fresh session, we don't want to do this every
            // time someone switches to another page
            if (!$session->get('previousActivity') and !$attempt) {
                // Append javascript to contents
                $contents .= '
					<script>
						var xensso_attempt_login = true;
						var xensso_master_url = "' . $options->waindigo_xenSso_slave_masterUrl . '";
					</script>
				';

                // Update include variable so it knows we added content
                $include = true;

                // Update session so it doesn't try this again
                setcookie('attemptLogin', true);
            }
        }

        // Check if javascript was added, if so we'll need to include the js
        // library that actually uses it
        if ($include) {
            $this->_append('<script src="' . $options->boardUrl . '/js/waindigo/xensso/slave/slave.js"></script>');
        }
    } /* END _pageContainerHead */
}