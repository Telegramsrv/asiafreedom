<?php

class Waindigo_XenSso_Slave_Install_Controller extends Waindigo_Install
{

    protected $_resourceManagerUrl = 'http://xenforo.com/community/resources/single-sign-on-slave-by-waindigo.2211/';

    protected function _preInstall()
    {
        $this->_uninstallOlderVersions();
    } /* END _preInstall */

    protected function _uninstallOlderVersions()
    {
        $options = array(
            'XenSSOMasterUrl' => 'waindigo_xenSso_slave_masterUrl',
            'XenSSOSlaveSecretPrivate' => 'waindigo_xenSso_slave_secretPrivate',
            'XenSSOSlaveSecretPublic' => 'waindigo_xenSso_slave_secretPublic'
        );

        foreach ($options as $oldOption => $newOption) {
            $this->_db->update('xf_option', array(
                'option_id' => $newOption,
                'addon_id' => 'Waindigo_XenSso_Slave'
            ), 'option_id = ' . $this->_db->quote($oldOption));
        }

        $addOn = $this->getModelFromCache('XenForo_Model_AddOn')->getAddOnById('xensso_slave');

        if ($addOn) {
            $dw = XenForo_DataWriter::create('XenForo_DataWriter_AddOn');
            $dw->setExistingData($addOn);
            $dw->set('uninstall_callback_class', '');
            $dw->set('uninstall_callback_method', '');
            $dw->delete();
        }
    } /* END _uninstallOlderVersions */

    protected function _getTables()
    {
        return array(
            'xensso_slave_user' => array(
                'openid_identity' => 'varchar(255) NOT NULL DEFAULT \'\' PRIMARY KEY', /* END 'openid_identity' */
			    'openid_sreg' => 'mediumtext NOT NULL', /* END 'openid_sreg' */
			    'user_id' => 'int(10) unsigned NOT NULL DEFAULT 0', /* END 'user_id' */
    		), /* END 'xensso_slave_user' */
		);
    } /* END _getTables */

    protected function _postUninstall()
    {
        $this->_purgeStorage();
    } /* END _postUninstall */

    /**
     * Purge OpenID storage
     *
     * @return void
     */
    protected function _purgeStorage()
    {
        $tmp = getenv('TMP');
        if (empty($tmp)) {
            $tmp = getenv('TEMP');
            if (empty($tmp)) {
                $tmp = "/tmp";
            }
        }

        $user = get_current_user();
        if (is_string($user) && !empty($user)) {
            $tmp .= '/' . $user;
        }

        $dir = $tmp . '/openid/consumer/';

        if (!is_dir($dir)) {
            return;
        }

        $files = scandir($dir);

        foreach ($files as $file) {
            if (is_dir($file)) {
                continue;
            }

            @unlink($dir . $file);
        }
    } /* END _purgeStorage */ /* END purgeStorage */
}