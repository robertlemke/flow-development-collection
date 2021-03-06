<?php
namespace TYPO3\Fluid\Tests\Unit\ViewHelpers\Form;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

require_once(__DIR__ . '/FormFieldViewHelperBaseTestcase.php');

/**
 * Test for the "Hidden" Form view helper
 */
class HiddenViewHelperTest extends \TYPO3\Fluid\Tests\Unit\ViewHelpers\Form\FormFieldViewHelperBaseTestcase
{
    /**
     * @var \TYPO3\Fluid\ViewHelpers\Form\HiddenViewHelper
     */
    protected $viewHelper;

    public function setUp()
    {
        parent::setUp();
        $this->viewHelper = $this->getAccessibleMock(\TYPO3\Fluid\ViewHelpers\Form\HiddenViewHelper::class, array('setErrorClassAttribute', 'getName', 'getValueAttribute', 'registerFieldNameForFormTokenGeneration'));
        $this->injectDependenciesIntoViewHelper($this->viewHelper);
        $this->viewHelper->initializeArguments();
    }

    /**
     * @test
     */
    public function renderCorrectlySetsTagNameAndDefaultAttributes()
    {
        $mockTagBuilder = $this->getMock(\TYPO3\Fluid\Core\ViewHelper\TagBuilder::class, array('setTagName', 'addAttribute'));
        $mockTagBuilder->expects($this->once())->method('setTagName')->with('input');
        $mockTagBuilder->expects($this->at(1))->method('addAttribute')->with('type', 'hidden');
        $mockTagBuilder->expects($this->at(2))->method('addAttribute')->with('name', 'foo');
        $this->viewHelper->expects($this->once())->method('registerFieldNameForFormTokenGeneration')->with('foo');
        $mockTagBuilder->expects($this->at(3))->method('addAttribute')->with('value', 'bar');

        $this->viewHelper->expects($this->once())->method('getName')->will($this->returnValue('foo'));
        $this->viewHelper->expects($this->once())->method('getValueAttribute')->will($this->returnValue('bar'));
        $this->viewHelper->injectTagBuilder($mockTagBuilder);

        $this->viewHelper->initialize();
        $this->viewHelper->render();
    }
}
