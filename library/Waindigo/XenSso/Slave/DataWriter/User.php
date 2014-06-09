<?php

/**
 * Datawriter for xensso_slave_user table
 */
class Waindigo_XenSso_Slave_DataWriter_User extends XenForo_DataWriter
{

    /**
     * Get fields managed by this datawriter
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            'xensso_slave_user' => array(
                'openid_identity' => array(
                    'type' => self::TYPE_STRING,
                    'required' => true,
                    'default' => '',
                    'maxLength' => 255
                ),
                'openid_sreg' => array(
                    'type' => self::TYPE_STRING,
                    'required' => true,
                    'default' => ''
                ),
                'user_id' => array(
                    'type' => self::TYPE_INT,
                    'required' => true,
                    'default' => 0
                )
            )
        );
    } /* END _getFields */

    /**
     * Get existing data
     *
     * @param int $data
     * @return array
     */
    protected function _getExistingData($data)
    {
        if (!$openId = $this->_getExistingPrimaryKey($data, 'openid_identity')) {
            return false;
        }

        return array(
            'xensso_slave_user' => $this->getModelFromCache('Waindigo_XenSso_Slave_Model_User')->getUserIdByOpenId(
                $openId)
        );
    } /* END _getExistingData */

    /**
     * Get update condition
     *
     * @param string $tableName
     * @return string
     */
    protected function _getUpdateCondition($tableName)
    {
        return 'openid_identity = ' . $this->_db->quote($this->getExisting('openid_identity'));
    } /* END _getUpdateCondition */
}