<?php

/**
 *
 * @see Waindigo_Library_Install_Controller
 */
class Waindigo_CustomPostFields_Extend_Waindigo_Library_Install_Controller extends XFCP_Waindigo_CustomPostFields_Extend_Waindigo_Library_Install_Controller
{

    protected function _getTables()
    {
        $tables = parent::_getTables();
        $tables['xf_library'] = array_merge($tables['xf_library'],
            $this->_getTableChangesForAddOn('Waindigo_CustomPostFields', 'xf_library'));
        $tables['xf_article_page'] = array_merge($tables['xf_article_page'],
            $this->_getTableChangesForAddOn('Waindigo_CustomPostFields', 'xf_article_page'));
        return $tables;
    } /* END _getTables */
}