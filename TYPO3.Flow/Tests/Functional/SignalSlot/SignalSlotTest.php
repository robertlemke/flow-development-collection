<?php
namespace TYPO3\Flow\Tests\Functional\SignalSlot;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

/**
 * Test suite for Signal Slot
 *
 */
class SignalSlotTest extends \TYPO3\Flow\Tests\FunctionalTestCase
{
    /**
     * @test
     */
    public function signalsDeclaredInAbstractClassesAreFunctionalInSubClasses()
    {
        $subClass = new Fixtures\SubClass();

        $dispatcher = $this->objectManager->get(\TYPO3\Flow\SignalSlot\Dispatcher::class);
        $dispatcher->connect(\TYPO3\Flow\Tests\Functional\SignalSlot\Fixtures\SubClass::class, 'something', $subClass, 'somethingSlot');

        $subClass->triggerSomethingSignalFromSubClass();
        $this->assertTrue($subClass->slotWasCalled, 'from sub class');

        $subClass->slotWasCalled = false;

        $subClass->triggerSomethingSignalFromAbstractClass();
        $this->assertTrue($subClass->slotWasCalled, 'from abstract class');
    }
}
