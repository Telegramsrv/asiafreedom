<?php

/**
 * Custom resource field definition.
 */
class Waindigo_CustomFields_Definition_ResourceField extends Waindigo_CustomFields_Definition_Abstract
{

    /**
     * Gets the structure of the custom field record.
     *
     * @return array
     */
    protected function _getFieldStructure()
    {
        return array(
            'table' => 'xf_resource_field', /* END 'table' */
        );
    } /* END _getFieldStructure */
}