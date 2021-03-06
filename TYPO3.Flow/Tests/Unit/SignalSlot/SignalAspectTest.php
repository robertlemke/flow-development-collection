<?php
namespace TYPO3\Flow\Tests\Unit\SignalSlot;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */


/**
 * Testcase for the Signal Aspect
 *
 */
class SignalAspectTest extends \TYPO3\Flow\Tests\UnitTestCase
{
    /**
     * @test
     */
    public function forwardSignalToDispatcherForwardsTheSignalsMethodArgumentsToTheDispatcher()
    {
        $mockJoinPoint = $this->getMock(\TYPO3\Flow\Aop\JoinPoint::class, array(), array(), '', false);
        $mockJoinPoint->expects($this->any())->method('getClassName')->will($this->returnValue('SampleClass'));
        $mockJoinPoint->expects($this->any())->method('getMethodName')->will($this->returnValue('emitSignal'));
        $mockJoinPoint->expects($this->any())->method('getMethodArguments')->will($this->returnValue(array('arg1' => 'val1', 'arg2' => array('val2'))));

        $mockDispatcher = $this->getMock(\TYPO3\Flow\SignalSlot\Dispatcher::class);
        $mockDispatcher->expects($this->once())->method('dispatch')->with('SampleClass', 'signal', array('arg1' => 'val1', 'arg2' => array('val2')));

        $aspect = $this->getAccessibleMock(\TYPO3\Flow\SignalSlot\SignalAspect::class, array('dummy'));
        $aspect->_set('dispatcher', $mockDispatcher);
        $aspect->forwardSignalToDispatcher($mockJoinPoint);
    }
}
