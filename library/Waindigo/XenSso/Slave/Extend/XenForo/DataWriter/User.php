<?php

class Waindigo_XenSso_Slave_Extend_XenForo_DataWriter_User extends XFCP_Waindigo_XenSso_Slave_Extend_XenForo_DataWriter_User
{

    public static $_validateWithMaster = true;

    /**
     * Verification callback to check that a username is valid
     *
     * @param string Username
     *
     * @return bool
     */
    protected function _verifyUsername(&$username)
    {
        if (!parent::_verifyUsername($username)) {
            return false;
        }

        if (!self::$_validateWithMaster || ($this->isUpdate() && $username === $this->getExisting('username'))) {
            return true;
        }

        $ignoreIf = Waindigo_XenSso_Slave_Sync::getIgnoreTerms($username);

        if (Waindigo_XenSso_Slave_Sync::validateMasterExists(array(
            'username' => $username
        ), $ignoreIf) != false) {
            $this->error(new XenForo_Phrase('usernames_must_be_unique'), 'username');
            return false;
        } else {
            return true;
        }
    } /* END _verifyUsername */

    /**
     * Verification callback to check that an email address is valid
     *
     * @param string Email
     *
     * @return bool
     */
    protected function _verifyEmail(&$email)
    {
        // essentially this function is disabled until the conflict resolver is
        // implemented
        return parent::_verifyEmail($email);

        if (!parent::_verifyEmail($email)) {
            return false;
        }

        if (!self::$_validateWithMaster or ($this->isUpdate() && $email === $this->getExisting('email'))) {
            return true;
        }

        if (!self::$_validateWithMaster) {
            return true;
        }

        if (Waindigo_XenSso_Slave_Sync::validateMasterExists(array(
            'email' => $email
        )) != false) {
            $this->error(new XenForo_Phrase('email_addresses_must_be_unique'), 'email');
            return false;
        } else {
            return true;
        }
    } /* END _verifyEmail */

    /**
     * Delete related OpenID entry when username or email changes
     *
     * @return void
     */
    protected function _postSave()
    {
        if ($this->isChanged('username') || $this->isChanged('email')) {
            $db = $this->_db;
            $userId = $this->get('user_id');
            $userIdQuoted = $db->quote($userId);

            $db->delete('xensso_slave_user', "user_id = $userIdQuoted");
        }

        if ($this->isChanged('user_state')) {
            Waindigo_XenSso_Slave_Sync::activateAccount($this->getMergedData());
        }

        parent::_postSave();
    } /* END _postSave */

    /**
     * Delete related OpenID entry
     *
     * @return void
     */
    protected function _postDelete()
    {
        $db = $this->_db;
        $userId = $this->get('user_id');
        $userIdQuoted = $db->quote($userId);

        $db->delete('xensso_slave_user', "user_id = $userIdQuoted");

        parent::_postDelete();
    } /* END _postDelete */
}