<?php

abstract class Waindigo_Listener_FrontControllerPostView extends Waindigo_Listener_Template
{

    /**
     *
     * @var XenForo_FrontController
     */
    protected $_fc = null;

    protected $_routePath = null;

    /**
     *
     * @param XenForo_FrontController $fc
     * @param string $contents
     */
    public function __construct(XenForo_FrontController $fc, &$contents)
    {
        $this->_fc = $fc;
        $this->_routePath = $this->_fetchRoutePath();
        parent::__construct($contents, null);
    } /* END __construct */

    // This only works on PHP 5.3+, so method should be overridden for now
    public static function frontControllerPostView(XenForo_FrontController $fc, &$output)
    {
        $class = get_called_class();
        $frontControllerPostView = new $class($fc, $output);
        $output = $frontControllerPostView->run();
    } /* END frontControllerPostView */

    /**
     *
     * @return true if successful, false otherwise
     * @param string $templateName
     * @param array|null $viewParams
     * @param string|null $contents
     */
    protected function _appendTemplateAtTopCtrl($templateName, $viewParams = null, &$contents = null, $after = true)
    {
        $rendered = $this->_render($templateName, $viewParams);

        preg_match('#<div class="topCtrl">(.*)</div>#Us', $rendered, $matches);

        if (isset($matches[1])) {
            if ($after) {
                $replacement = '$1' . $matches[1];
            } else {
                $replacement = $matches[1] . '$1';
            }
            $this->_contents = preg_replace('#<div class="topCtrl">(.*)</div>#Us',
                '<div class="topCtrl">' . $replacement . '</div>', $this->_contents, 1, $count);
            if ($count)
                return true;
        }

        // START legacy code
        preg_match('#<h1>(.*)</h1>#Us', $rendered, $matches);
        if (isset($matches[1])) {
            $this->_contents = preg_replace('#<div class="titleBar">(.*)</div>#Us',
                '<div class="titleBar">' . $matches[1] . '$1</div>', $this->_contents, 1, $count);
            if ($count)
                return true;
        }
        // END legacy code


        preg_match('#<div class="titleBar">(.*)</div>#s', $rendered, $matches);
        if (isset($matches[1])) {
            $this->_contents = preg_replace('#<div class="titleBar">(.*)</div>#Us',
                '<div class="titleBar">' . $matches[1] . '$1</div>', $this->_contents, 1, $count);
            if ($count)
                return true;
        }

        return false;
    } /* END _appendTemplateAtTopCtrl */

    /**
     *
     * @return true if successful, false otherwise
     * @param string $templateName
     * @param array|null $viewParams
     * @param string|null $contents
     */
    protected function _appendTemplateAfterTopCtrl($templateName, $viewParams = null, &$contents = null)
    {
        return $this->_appendTemplateAtTopCtrl($templateName, $viewParams, $contents, true);
    } /* END _appendTemplateAfterTopCtrl */

    /**
     *
     * @return true if successful, false otherwise
     * @param string $templateName
     * @param array|null $viewParams
     * @param string|null $contents
     */
    protected function _appendTemplateBeforeTopCtrl($templateName, $viewParams = null, &$contents = null)
    {
        return $this->_appendTemplateAtTopCtrl($templateName, $viewParams, $contents, false);
    } /* END _appendTemplateBeforeTopCtrl */

    /**
     *
     * @return boolean true if response code match, false otherwise
     * @param int $responseCode
     */
    protected function _assertResponseCode($responseCode)
    {
        if ($this->_fc->getResponse()->getHttpResponseCode() != $responseCode) {
            throw new XenForo_Exception('Incorrect response code');
        }
    } /* END _assertResponseCode */

    /**
     *
     * @return string
     */
    protected function _fetchRoutePath()
    {
        return rtrim($this->_fc->getRequest()->getParam('_matchedRoutePath'), "/");
    } /* END _fetchRoutePath */

    /**
     *
     * @see Waindigo_Listener_Template::_render()
     */
    protected function _render($templateName, $viewParams = null)
    {
        if (!$viewParams)
            $viewParams = $this->_fetchViewParams();
        return $this->_fc->getDependencies()
            ->createTemplateObject($templateName, $viewParams)
            ->render();
    } /* END _render */
}