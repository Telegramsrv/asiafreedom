 <?php

class SimplePortal_ViewPublic_Index extends XenForo_ViewPublic_Base
{

    public function renderHtml()
    {
        if ($this->_params['items']){

            $bbCodeParser = $this->getParser();
            $bbCodeOptions = $this->getDefaultBbCodeOptions(isset($this->_params['showInlineAttachments']));

            $this->_params['items'] = SimplePortal_Helper_Item::getTemplates($this, $this->_params['items'], $bbCodeParser, $bbCodeOptions);
        }
    }


    protected function getParser()
    {
        return new XenForo_BbCode_Parser(XenForo_BbCode_Formatter_Base::create('Base', array('view' => $this)));
    }


    protected function getDefaultBbCodeOptions($viewAttachments = false)
    {

        $options = array(
            'states' => array(
                'viewAttachments' => $viewAttachments,
                'isPortal' => true
            )
        );

        return $options;
    }
}