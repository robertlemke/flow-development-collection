<?php
namespace TYPO3\Flow\Security\Cryptography;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

/**
 * A password hashing strategy interface
 *
 */
interface PasswordHashingStrategyInterface
{
    /**
     * Hash a password for storage
     *
     * @param string $password Cleartext password that will be hashed
     * @param string $staticSalt Optional static salt that will not be stored in the hashed password
     * @return string The hashed password with dynamic salt (if used)
     */
    public function hashPassword($password, $staticSalt = null);

    /**
     * Validate a hashed password against a cleartext password
     *
     * @param string $password
     * @param string $hashedPasswordAndSalt Hashed password with dynamic salt (if used)
     * @param string $staticSalt Optional static salt that will not be stored in the hashed password
     * @return boolean TRUE if the given cleartext password matched the hashed password
     */
    public function validatePassword($password, $hashedPasswordAndSalt, $staticSalt = null);
}
