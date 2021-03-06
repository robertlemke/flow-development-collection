<?php
namespace TYPO3\Flow\Tests\Unit\Log\Backend;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

/**
 * Testcase for the abstract log backend
 *
 */
class AbstractBackendTest extends \TYPO3\Flow\Tests\UnitTestCase
{
    /**
     * @var \TYPO3\Flow\Log\Backend\AbstractBackend
     */
    protected $backendClassName;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->backendClassName = 'ConcreteBackend_' . md5(uniqid(mt_rand(), true));
        eval('
			class ' . $this->backendClassName . ' extends \TYPO3\Flow\Log\Backend\AbstractBackend {
				public function open() {}
				public function append($message, $severity = 1, $additionalData = NULL, $packageKey = NULL, $className = NULL, $methodName = NULL) {}
				public function close() {}
				public function setSomeOption($value) {
					$this->someOption = $value;
				}
				public function getSomeOption() {
					return $this->someOption;
				}
			}
		');
    }

    /**
     * @test
     */
    public function theConstructorCallsSetterMethodsForAllSpecifiedOptions()
    {
        $className = $this->backendClassName;
        $backend = new $className(array('someOption' => 'someValue'));
        $this->assertSame('someValue', $backend->getSomeOption());
    }
}
