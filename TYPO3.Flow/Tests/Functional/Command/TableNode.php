<?php
namespace TYPO3\Flow\Tests\Functional\Command;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

/**
 * A helper class for behat scenario parameters, needed when processing
 * behat scenarios/steps in an isolated process
 */
class TableNode
{
    /**
     * @var string
     */
    protected $hash;

    /**
     * @param string $hash The table source hash string
     */
    public function __construct($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return string The table source hash string
     */
    public function getHash()
    {
        return $this->hash;
    }
}
