<?php
namespace TYPO3\Flow\Mvc\Exception;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

/**
 * A "No Such Action" exception
 *
 * @api
 */
class NoSuchActionException extends \TYPO3\Flow\Mvc\Exception
{
    /**
     * @var integer
     */
    protected $statusCode = 404;
}
