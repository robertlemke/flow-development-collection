<?php
namespace TYPO3\Flow\Tests\Functional\Error;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

use TYPO3\Flow\Core\ApplicationContext;
use TYPO3\Flow\Reflection\ObjectAccess;
use TYPO3\Flow\Tests\FunctionalTestCase;
use TYPO3\Flow\Error\Debugger;
use TYPO3\Flow\Utility\Arrays;

/**
 * Functional tests for the Debugger
 */
class DebuggerTest extends FunctionalTestCase
{
    /**
     * @var \TYPO3\Flow\Configuration\ConfigurationManager
     */
    protected $configurationManager;


    public function setUp()
    {
        parent::setUp();
        $this->configurationManager = $this->objectManager->get(\TYPO3\Flow\Configuration\ConfigurationManager::class);
        Debugger::clearState();
    }


    /**
     * @test
     */
    public function ignoredClassesCanBeOverwrittenBySettings()
    {
        $object = new ApplicationContext('Development');
        $this->assertEquals('TYPO3\Flow\Core\ApplicationContext prototype object', Debugger::renderDump($object, 10, true));
        Debugger::clearState();

        $currentConfiguration = ObjectAccess::getProperty($this->configurationManager, 'configurations', true);
        $configurationOverwrite['Settings']['TYPO3']['Flow']['error']['debugger']['ignoredClasses']['TYPO3\\\\Flow\\\\Core\\\\.*'] = false;
        $newConfiguration = Arrays::arrayMergeRecursiveOverrule($currentConfiguration, $configurationOverwrite);
        ObjectAccess::setProperty($this->configurationManager, 'configurations', $newConfiguration, true);

        $this->assertContains('rootContextString', Debugger::renderDump($object, 10, true));
    }
}
