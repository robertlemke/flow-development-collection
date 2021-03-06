<?php
namespace TYPO3\Flow\Security\Exception;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

/**
 * An "AccessDenied" Exception
 *
 * @api
 */
class AuthenticationRequiredException extends \TYPO3\Flow\Security\Exception
{
    /**
     * @var integer
     */
    protected $statusCode = 401;
}
