<?php

/**
 * Exports a post field as XML.
 */
class Waindigo_CustomPostFields_ViewAdmin_PostField_Export extends XenForo_ViewAdmin_Base
{

    public function renderXml()
    {
        $this->setDownloadFileName('field-' . $this->_params['field']['field_id'] . '.xml');
        return $this->_params['xml']->saveXml();
    } /* END renderXml */
}