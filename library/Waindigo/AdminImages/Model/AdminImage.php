<?php

/**
 * Model for adminImages.
 *
 * @package XenForo_adminImage
 */
class Waindigo_AdminImages_Model_AdminImage extends XenForo_Model
{
	/**
	 * Gets all admin image handler objects, or one for the specified content type.
	 *
	 * @param string|array|null $limitContentType If specified, gets handler for specified type(s) only
	 *
	 * @return Waindigo_AdminImages_AdminImageHandler_Abstract|array|false
	 */
	public function getAdminImageHandlers($limitContentType = null)
	{
		$contentTypes = $this->_getLocalCacheData('adminImageHandlerPairs');
		if ($contentTypes === false)
		{
			$contentTypes = $this->_getDb()->fetchPairs('
				SELECT content_type, field_value
				FROM xf_content_type_field
				WHERE field_name = \'admin_image_handler_class\'
			');

			$this->setLocalCacheData('adminImageHandlerPairs', $contentTypes);
		}

		if (is_string($limitContentType))
		{
			if (isset($contentTypes[$limitContentType]))
			{
				$class = $contentTypes[$limitContentType];
				return new $class();
			}
			else
			{
				return false;
			}
		}
		else if (is_array($limitContentType))
		{
			$handlers = array();
			foreach ($contentTypes AS $contentType => $handlerClass)
			{
				if (in_array($contentType, $limitContentType))
				{
					$handlers[$contentType] = new $handlerClass();
				}
			}
		}
		else
		{
			$handlers = array();
			foreach ($contentTypes AS $contentType => $handlerClass)
			{
				$handlers[$contentType] = new $handlerClass();
			}
		}

		return $handlers;
	} /* END getAdminImageHandlers */
	
	public function associateAttachment($type, $typeId)
	{
		$this->_getDb()->update('xf_attachment', array(
			'content_type' => $type,
			'content_id' => $typeId,
			'temp_hash' => '',
			'unassociated' => 0
		), 'temp_hash = ' . $this->_getDb()->quote('adminimage-'.$type.'-'.$typeId));
	} /* END associateAttachment */
}