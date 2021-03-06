<?php
namespace TYPO3\Fluid\Tests\Unit\Core\Widget;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

/**
 * Testcase for WidgetContext
 *
 */
class WidgetContextTest extends \TYPO3\Flow\Tests\UnitTestCase
{
    /**
     * @var \TYPO3\Fluid\Core\Widget\WidgetContext
     */
    protected $widgetContext;

    /**
     */
    public function setUp()
    {
        $this->widgetContext = new \TYPO3\Fluid\Core\Widget\WidgetContext();
    }

    /**
     * @test
     */
    public function widgetIdentifierCanBeReadAgain()
    {
        $this->widgetContext->setWidgetIdentifier('myWidgetIdentifier');
        $this->assertEquals('myWidgetIdentifier', $this->widgetContext->getWidgetIdentifier());
    }

    /**
     * @test
     */
    public function ajaxWidgetIdentifierCanBeReadAgain()
    {
        $this->widgetContext->setAjaxWidgetIdentifier(42);
        $this->assertEquals(42, $this->widgetContext->getAjaxWidgetIdentifier());
    }

    /**
     * @test
     */
    public function nonAjaxWidgetConfigurationIsReturnedWhenContextIsNotSerialized()
    {
        $this->widgetContext->setNonAjaxWidgetConfiguration(array('key' => 'value'));
        $this->widgetContext->setAjaxWidgetConfiguration(array('keyAjax' => 'valueAjax'));
        $this->assertEquals(array('key' => 'value'), $this->widgetContext->getWidgetConfiguration());
    }

    /**
     * @test
     */
    public function aWidgetConfigurationIsReturnedWhenContextIsSerialized()
    {
        $this->widgetContext->setNonAjaxWidgetConfiguration(array('key' => 'value'));
        $this->widgetContext->setAjaxWidgetConfiguration(array('keyAjax' => 'valueAjax'));
        $this->widgetContext = serialize($this->widgetContext);
        $this->widgetContext = unserialize($this->widgetContext);
        $this->assertEquals(array('keyAjax' => 'valueAjax'), $this->widgetContext->getWidgetConfiguration());
    }

    /**
     * @test
     */
    public function controllerObjectNameCanBeReadAgain()
    {
        $this->widgetContext->setControllerObjectName('TYPO3\My\Object\Name');
        $this->assertEquals('TYPO3\My\Object\Name', $this->widgetContext->getControllerObjectName());
    }

    /**
     * @test
     */
    public function viewHelperChildNodesCanBeReadAgain()
    {
        $viewHelperChildNodes = $this->getMock(\TYPO3\Fluid\Core\Parser\SyntaxTree\RootNode::class);
        $renderingContext = $this->getMock(\TYPO3\Fluid\Core\Rendering\RenderingContextInterface::class);

        $this->widgetContext->setViewHelperChildNodes($viewHelperChildNodes, $renderingContext);
        $this->assertSame($viewHelperChildNodes, $this->widgetContext->getViewHelperChildNodes());
        $this->assertSame($renderingContext, $this->widgetContext->getViewHelperChildNodeRenderingContext());
    }
}
