<?php

/**
 *
 * @see XenForo_Model_AddOn
 */
class Waindigo_CustomPostFields_Extend_XenForo_Model_AddOn extends XFCP_Waindigo_CustomPostFields_Extend_XenForo_Model_AddOn
{

    /**
     *
     * @see XenForo_Model_AddOn::getAddOnXml()
     */
    public function getAddOnXml(array $addOn)
    {
        /* @var $document DOMDocument */
        $document = parent::getAddOnXml($addOn);

        $rootNode = $document->getElementsByTagName('addon')->item(0);
        $addOnId = $rootNode->attributes->getNamedItem('addon_id')->textContent;

        $dataNode = $document->createElement('post_fields');
        $this->getModelFromCache('Waindigo_CustomPostFields_Model_PostField')->appendFieldsAddOnXml($dataNode, $addOnId);
        $this->_appendNodeAlphabetically($rootNode, $dataNode);

        return $document;
    } /* END getAddOnXml */

    /**
     *
     * @see XenForo_Model_AddOn::importAddOnExtraDataFromXml()
     */
    public function importAddOnExtraDataFromXml(SimpleXMLElement $xml, $addOnId)
    {
        parent::importAddOnExtraDataFromXml($xml, $addOnId);

        try {
            $this->getModelFromCache('Waindigo_CustomPostFields_Model_PostField')->importFieldsAddOnXml($xml->post_fields,
                $addOnId);
        } catch (Exception $e) {
            // do nothing
        }
    } /* END importAddOnExtraDataFromXml */
}