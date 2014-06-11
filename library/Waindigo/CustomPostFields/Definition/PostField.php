<?php

/**
 * Custom post field definition.
 */
class Waindigo_CustomPostFields_Definition_PostField extends Waindigo_CustomFields_Definition_Abstract
{

    /**
     * Gets the structure of the custom field record.
     *
     * @return array
     */
    protected function _getFieldStructure()
    {
        return array(
            'table' => 'xf_post_field', /* END 'table' */
        );
    } /* END _getFieldStructure */
}