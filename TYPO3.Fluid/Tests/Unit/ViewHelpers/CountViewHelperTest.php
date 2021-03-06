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
 * Testcase for CountViewHelper
 */
class CountViewHelperTest extends \TYPO3\Fluid\ViewHelpers\ViewHelperBaseTestcase
{
    /**
     * @var \TYPO3\Fluid\ViewHelpers\CountViewHelper
     */
    protected $viewHelper;

    public function setUp()
    {
        parent::setUp();
        $this->viewHelper = $this->getAccessibleMock(\TYPO3\Fluid\ViewHelpers\CountViewHelper::class, array('renderChildren'));
        $this->injectDependenciesIntoViewHelper($this->viewHelper);
        $this->viewHelper->initializeArguments();
    }

    /**
     * @test
     */
    public function renderReturnsNumberOfElementsInAnArray()
    {
        $expectedResult = 3;
        $actualResult = $this->viewHelper->render(array('foo', 'bar', 'Baz'));
        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @test
     */
    public function renderReturnsNumberOfElementsInAnArrayObject()
    {
        $expectedResult = 2;
        $actualResult = $this->viewHelper->render(new \ArrayObject(array('foo', 'bar')));
        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @test
     */
    public function renderReturnsZeroIfGivenArrayIsEmpty()
    {
        $expectedResult = 0;
        $actualResult = $this->viewHelper->render(array());
        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @test
     */
    public function renderUsesChildrenAsSubjectIfGivenSubjectIsNull()
    {
        $this->viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(array('foo', 'bar', 'baz')));
        $expectedResult = 3;
        $actualResult = $this->viewHelper->render(null);
        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @test
     */
    public function renderReturnsZeroIfGivenSubjectIsNullAndRenderChildrenReturnsNull()
    {
        $this->viewHelper->expects($this->once())->method('renderChildren')->will($this->returnValue(null));
        $expectedResult = 0;
        $actualResult = $this->viewHelper->render(null);
        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @test
     * @expectedException \TYPO3\Fluid\Core\ViewHelper\Exception
     */
    public function renderThrowsExceptionIfGivenSubjectIsNotCountable()
    {
        $object = new \stdClass();
        $this->viewHelper->render($object);
    }
}
