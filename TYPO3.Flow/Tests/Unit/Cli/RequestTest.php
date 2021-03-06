<?php
namespace TYPO3\Flow\Tests\Unit\Cli;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

use TYPO3\Flow\Cli\Request;

/**
 * Testcase for the CLI Request class
 */
class RequestTest extends \TYPO3\Flow\Tests\UnitTestCase
{
    /**
     * @test
     */
    public function getCommandReturnsTheCommandObjectReflectingTheRequestInformation()
    {
        $request = new Request();
        $request->setControllerObjectName(\TYPO3\Flow\Command\CacheCommandController::class);
        $request->setControllerCommandName('flush');

        $command = $request->getCommand();
        $this->assertEquals('typo3.flow:cache:flush', $command->getCommandIdentifier());
    }

    /**
     * @test
     */
    public function setControllerObjectNameAndSetControllerCommandNameUnsetTheBuiltCommandObject()
    {
        $request = new Request();
        $request->setControllerObjectName(\TYPO3\Flow\Command\CacheCommandController::class);
        $request->setControllerCommandName('flush');
        $request->getCommand();

        $request->setControllerObjectName('TYPO3\Flow\Command\BeerCommandController');
        $request->setControllerCommandName('drink');

        $command = $request->getCommand();
        $this->assertEquals('typo3.flow:beer:drink', $command->getCommandIdentifier());
    }
}
