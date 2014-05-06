<?php

/**
 * Model for custom user fields.
 */
class AnyTV_Models_CustomUserModel extends XenForo_Model_User
{
	/**
	 * Gets users that match the specified conditions.
	 *
	 * @param array $conditions
	 * @param array $fetchOptions
	 *
	 * @return array Format: [user id] => user info
	 */
	public function getUsers(array $conditions, array $fetchOptions = array())
	{
		$whereClause = $this->prepareUserConditions($conditions, $fetchOptions);

		$orderClause = $this->prepareUserOrderOptions($fetchOptions, 'user.username');
		$joinOptions = $this->prepareUserFetchOptions($fetchOptions);
		$limitOptions = $this->prepareLimitFetchOptions($fetchOptions);

		return $this->fetchAllKeyed($this->limitQueryResults(
			'
				SELECT user.*
					' . $joinOptions['selectFields'] . '
				FROM xf_user AS user
				' . $joinOptions['joinTables'] . '
				WHERE ' . $whereClause . '
				' . $orderClause . '
			', $limitOptions['limit'], $limitOptions['offset']
		), 'user_id');
	}
}