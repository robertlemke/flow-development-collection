<?php
namespace TYPO3\Fluid\Tests\Unit\ViewHelpers;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

require_once(__DIR__ . '/ViewHelperBaseTestcase.php');

/**
 * Testcase for RenderViewHelper
 */
class RenderViewHelperTest extends \TYPO3\Fluid\ViewHelpers\ViewHelperBaseTestcase
{
    /**
     * @var \TYPO3\Fluid\ViewHelpers\RenderViewHelper
     */
    protected $viewHelper;

    public function setUp()
    {
        parent::setUp();
        $this->templateVariableContainer = new \TYPO3\Fluid\Core\ViewHelper\TemplateVariableContainer();
        $this->renderingContext->injectTemplateVariableContainer($this->templateVariableContainer);
        $this->viewHelper = $this->getAccessibleMock(\TYPO3\Fluid\ViewHelpers\RenderViewHelper::class, array('dummy'));
        $this->injectDependenciesIntoViewHelper($this->viewHelper);
    }

    /**
     * @test
     */
    public function loadSettingsIntoArgumentsSetsSettingsIfNoSettingsAreSpecified()
    {
        $arguments = array(
            'someArgument' => 'someValue'
        );
        $expected = array(
            'someArgument' => 'someValue',
            'settings' => 'theSettings'
        );
        $this->templateVariableContainer->add('settings', 'theSettings');

        $actual = $this->viewHelper->_call('loadSettingsIntoArguments', $arguments);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function loadSettingsIntoArgumentsDoesNotOverrideGivenSettings()
    {
        $arguments = array(
            'someArgument' => 'someValue',
            'settings' => 'specifiedSettings'
        );
        $expected = array(
            'someArgument' => 'someValue',
            'settings' => 'specifiedSettings'
        );
        $this->templateVariableContainer->add('settings', 'theSettings');

        $actual = $this->viewHelper->_call('loadSettingsIntoArguments', $arguments);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function loadSettingsIntoArgumentsDoesNotThrowExceptionIfSettingsAreNotInTemplateVariableContainer()
    {
        $arguments = array(
            'someArgument' => 'someValue'
        );
        $expected = array(
            'someArgument' => 'someValue'
        );

        $actual = $this->viewHelper->_call('loadSettingsIntoArguments', $arguments);
        $this->assertEquals($expected, $actual);
    }
}
