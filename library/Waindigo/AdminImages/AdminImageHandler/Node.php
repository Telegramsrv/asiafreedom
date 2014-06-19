<?php

/**
 * Admin image handler for nodes.
 */
class Waindigo_AdminImages_AdminImageHandler_Node extends Waindigo_AdminImages_AdminImageHandler_Abstract
{
	/**
	 * Gets the option for the moderator add "choice" page.
	 * @see XenForo_ModeratorHandler_Abstract::getAddModeratorOption()
	 */
	public function getUploadAdminImageOption(XenForo_View $view, $selectedContentId, $contentType)
	{
		$nodeModel = $this->_getNodeModel();

		$visitor = XenForo_Visitor::getInstance();
		
		$nodes = $nodeModel->getAllNodes();
		
		/* @var $adminModel XenForo_Model_Admin */
		$adminModel = XenForo_Model::create('XenForo_Model_Admin');
		$admin = $adminModel->getAdminById($visitor['user_id']);
		
		$nodeIds = array();
		if (!$visitor->isSuperAdmin() && isset($admin['is_content_admin']) && $admin['is_content_admin'])
		{
			$nodeIds = $this->getModeratedNodeIds();
		
			$allNodes = $nodes;
			$nodes = array();
			foreach ($allNodes as $node)
			{
				if (in_array($node['node_id'], $nodeIds))
				{
					$nodes[$node['node_id']] = $node;
				}
			}
		}
		
		$nodes = array('0' => array('value' => 0, 'label' => '')) + $nodeModel->getNodeOptionsArray($nodes);
	
		return array(
				'value' => $contentType,
				'label' => new XenForo_Phrase('forum') . ':',
				'disabled' => array(
						XenForo_Template_Helper_Admin::select("type_id[$contentType]", $selectedContentId, $nodes)
				)
		);
	} /* END getUploadAdminImageOption */
	
	/**
	 * Gets the title of multiple pieces of content in this content type.
	 * The return should be an array keyed by matching keys of the IDs param.
	 * Note that an ID value may occur multiple times.
	 *
	 * Note that the title may be more than just the title, if necessary. It may
	 * include other necessary disambiguation, such as the content type ("Forum - Name").
	 *
	 * @param array $ids
	 * @param array $admin
	 *
	 * @return array Format: [id key (key of param)] => title
	 */
	public function getContentTitles(array $ids, array $admin = array())
	{
		$nodes = $this->_getNodeModel()->getAllNodes();
		$titles = array();
		foreach ($ids AS $key => $id)
		{
			if (isset($nodes[$id]))
			{
				$node = $nodes[$id];
				$titles[$key] = new XenForo_Phrase('node_type_' . $node['node_type_id']) . " - $node[title]";
			}
		}
		
		return $titles;
	} /* END getContentTitles */

	public function getModeratedNodeIds()
	{
		$visitor = XenForo_Visitor::getInstance();
			
		/* @var $moderatorModel XenForo_Model_Moderator */
		$moderatorModel = XenForo_Model::create('XenForo_Model_Moderator');
		$moderators = $moderatorModel->getContentModerators(array(
				'content_type' => 'node',
				'user_id' => $visitor['user_id']
		));
			
		$nodeIds = array();
		foreach ($moderators as $moderator)
		{
			$nodeIds[] = $moderator['content_id'];
		}
		
		return $nodeIds;
	} /* END getModeratedNodeIds */
	
	/**
	 * @return XenForo_Model_Node
	 */
	protected function _getNodeModel()
	{
		return XenForo_Model::create('XenForo_Model_Node');
	} /* END _getNodeModel */
}