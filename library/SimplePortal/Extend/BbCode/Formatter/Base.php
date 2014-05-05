<?php


/**
 * @xfcp XenForo_BbCode_Formatter_Base
 *
 */
class SimplePortal_Extend_BbCode_Formatter_Base extends
	XFCP_SimplePortal_Extend_BbCode_Formatter_Base
{
	protected $_tags;

	public function getTags()
	{
		$this->_tags = parent::getTags();
		$this->_tags['portalpreview'] = array(
			'callback' => array($this, 'portalPreview'),
			'trimLeadingLinesAfter' => 1,
		);

		return $this->_tags;
	}


	public function portalPreview(array $tag, array $rendererStates){

		if (isset($rendererStates['isPortal'])){
			$text = $this->renderSubTree($tag['children'], $rendererStates);
			return $text . SimplePortal_Static::PORTAL_PREVIEW_ENDSTRING;
		}
		else if (!isset($rendererStates['isPortal']) && !empty($tag['option']) && $tag['option'] == 'exclusive'){
			return'';
		}
		else {
			return $this->renderSubTree($tag['children'], $rendererStates);
		}
	}

// moved to the view
// will be activated once this can be controller via options
/*
	public function renderTagAttach(array $tag, array $rendererStates)
	{
		if ($rendererStates['isPortal'] ){
			return '';
		}
		return parent::renderTagAttach($tag, $rendererStates);
	}


	public function renderTagQuote(array $tag, array $rendererStates)
	{
		if ($rendererStates['isPortal'] ){
			return '';
		}
		return parent::renderTagQuote($tag, $rendererStates);
	}
*/
}