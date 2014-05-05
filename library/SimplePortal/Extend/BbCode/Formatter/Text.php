<?php


/**
 * @xfcp XenForo_BbCode_Formatter_Text
 *
 */
class SimplePortal_Extend_BbCode_Formatter_Text extends
	XFCP_SimplePortal_Extend_BbCode_Formatter_Text
{

	protected $_tags;

	public function getTags()
	{
		$this->_tags = parent::getTags();
		$this->_tags['portalpreview'] = array(
			'callback' => array($this, 'handleTag')
		);
		return $this->_tags;
	}

	public function handleTag(array $tag, array $rendererStates)
	{
		$tagName = $tag['tag'];
		if ($tagName == 'portalpreview') {

			if ($tag['option'] && $tag['option'] == 'exclusive') {
				return '';
			}
		}
		return parent::handleTag($tag, $rendererStates);
	}
}