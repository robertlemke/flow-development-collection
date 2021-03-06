<?php
namespace TYPO3\Flow;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

/**
 * A generic Flow Exception
 *
 * @api
 */
class Exception extends \Exception
{
    /**
     * @var string
     */
    protected $referenceCode;

    /**
     * @var integer
     */
    protected $statusCode = 500;

    /**
     * Returns a code which can be communicated publicly so that whoever experiences the exception can refer
     * to it and a developer can find more information about it in the system log.
     *
     * @return string
     * @api
     */
    public function getReferenceCode()
    {
        if (!isset($this->referenceCode)) {
            $this->referenceCode = date('YmdHis', $_SERVER['REQUEST_TIME']) . substr(md5(rand()), 0, 6);
        }
        return $this->referenceCode;
    }

    /**
     * Returns the HTTP status code this exception corresponds to (defaults to 500).
     *
     * @return integer
     * @api
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
