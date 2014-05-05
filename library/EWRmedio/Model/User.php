<?php

class EWRmedio_Model_User extends XFCP_EWRmedio_Model_User
{
	public function mergeUsers(array $target, array $source)
	{
		$response = parent::mergeUsers($target, $source);
		
		XenForo_Db::beginTransaction();
		
		$this->_getDb()->query("UPDATE EWRmedio_comments SET username = ? WHERE user_id = ?", array($target['username'], $source['user_id']));
		$this->_getDb()->query("UPDATE EWRmedio_comments SET user_id = ? WHERE user_id = ?", array($target['user_id'], $source['user_id']));
		$this->_getDb()->query("UPDATE EWRmedio_media SET username = ? WHERE user_id = ?", array($target['username'], $source['user_id']));
		$this->_getDb()->query("UPDATE EWRmedio_media SET user_id = ? WHERE user_id = ?", array($target['user_id'], $source['user_id']));
		$this->_getDb()->query("UPDATE EWRmedio_playlists SET user_id = ? WHERE user_id = ?", array($target['user_id'], $source['user_id']));
		
		$this->_getDb()->query("DELETE FROM EWRmedio_read WHERE user_id = ?", $source['user_id']);
		$this->_getDb()->query("DELETE FROM EWRmedio_users WHERE user_id = ?", $source['user_id']);
		$this->_getDb()->query("DELETE FROM EWRmedio_watch WHERE user_id = ?", $source['user_id']);

		XenForo_Db::commit();
		
		return $response;
	}
}