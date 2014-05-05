<?php

/**
 *
 * Class SimplePortal_Deferred_Category
 */
class SimplePortal_Deferred_Category extends XenForo_Deferred_Abstract{


    public function execute(array $deferred, array $data, $targetRunTime, &$status)
    {
        $data = array_merge(array(
            'position' => 0,
            'batch' => 1
        ), $data);

        $data['batch'] = max(1, $data['batch']);
        /** @var SimplePortal_Model_Category $categoryModel */
        $categoryModel = XenForo_Model::create('SimplePortal_Model_Category');

        $categories = $categoryModel->getCategories(array(), array('limit' => $data['batch'], 'offset' => $data['position']));
        if (!$categories)
        {
            return false;
        }

        foreach ($categories AS $category)
        {
            $data['position'] ++;
            /** @var SimplePortal_DataWriter_Category $dw */
            $dw = XenForo_DataWriter::create('SimplePortal_DataWriter_Category', XenForo_DataWriter::ERROR_SILENT);
            if ($dw->setExistingData($category, true))
            {
                $dw->rebuildCounters();
                $dw->save();
            }
        }

        $rbPhrase = new XenForo_Phrase('rebuilding');
        $typePhrase = new XenForo_Phrase('categories');
        $status = sprintf('%s... %s (%s)', $rbPhrase, $typePhrase, XenForo_Locale::numberFormat($data['position']));

        return $data;
    }

}