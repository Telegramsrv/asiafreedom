<?php
class SimplePortal_ControllerHelper_Category  extends XenForo_ControllerHelper_Abstract{

    /**
     * @param $categoryId
     * @return int (0) if category doesn't exist
     */
    public function assertCategoryValid($categoryId){
		$category = $this->_controller->getCategoryModel()->getCategoryById($categoryId);
		if (!$category){
			// no errormessage because the indexpage doesn't need a category
            // TODO why not return null here?
			return 0;
		}

        if ($category['style_id']){
            $this->_controller->setViewStateChange('styleId', $category['style_id']);
        }

		return $category;
	}
}