<?php
namespace TYPO3\Flow\Tests\Unit\Session\Aspect;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

/**
 * Testcase for the SessionObjectMethodsPointcutFilter
 *
 */
class SessionObjectMethodsPointcutFilterTest extends \TYPO3\Flow\Tests\UnitTestCase
{
    /**
     * @test
     */
    public function reduceTargetClassNamesFiltersAllClassesNotBeeingConfiguredAsScopeSession()
    {
        $availableClassNames = array(
            'TestPackage\Subpackage\Class1',
            'TestPackage\Class2',
            'TestPackage\Subpackage\SubSubPackage\Class3',
            'TestPackage\Subpackage2\Class4'
        );
        sort($availableClassNames);
        $availableClassNamesIndex = new \TYPO3\Flow\Aop\Builder\ClassNameIndex();
        $availableClassNamesIndex->setClassNames($availableClassNames);

        $mockCompileTimeObjectManager = $this->getMock(\TYPO3\Flow\Object\CompileTimeObjectManager::class, array(), array(), '', false);
        $mockCompileTimeObjectManager->expects($this->any())->method('getClassNamesByScope')->with(\TYPO3\Flow\Object\Configuration\Configuration::SCOPE_SESSION)->will($this->returnValue(array('TestPackage\Subpackage\Class1', 'TestPackage\Subpackage\SubSubPackage\Class3', 'SomeMoreClass')));

        $sessionObjectMethodsPointcutFilter = new \TYPO3\Flow\Session\Aspect\SessionObjectMethodsPointcutFilter();
        $sessionObjectMethodsPointcutFilter->injectObjectManager($mockCompileTimeObjectManager);

        $expectedClassNames = array(
            'TestPackage\Subpackage\Class1',
            'TestPackage\Subpackage\SubSubPackage\Class3'
        );
        sort($expectedClassNames);
        $expectedClassNamesIndex = new \TYPO3\Flow\Aop\Builder\ClassNameIndex();
        $expectedClassNamesIndex->setClassNames($expectedClassNames);

        $result = $sessionObjectMethodsPointcutFilter->reduceTargetClassNames($availableClassNamesIndex);

        $this->assertEquals($expectedClassNamesIndex, $result, 'The wrong class names have been filtered');
    }
}
