<?php

class Waindigo_AdminImages_Install_Controller extends Waindigo_Install
{
    protected $_resourceManagerUrl = 'http://xenforo.com/community/resources/admin-images-by-waindigo.1266/';
    
    
	protected function _getContentTypeFields()
	{
		return array(
			'node' => array(
				'admin_image_handler_class' => 'Waindigo_AdminImages_AdminImageHandler_Node',
				'attachment_handler_class' => 'Waindigo_AdminImages_AttachmentHandler_Node',
			),
		);
	} /* END _getContentTypeFields */
}