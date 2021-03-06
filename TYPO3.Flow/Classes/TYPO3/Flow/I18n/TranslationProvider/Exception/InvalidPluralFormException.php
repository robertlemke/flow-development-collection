<?php
namespace TYPO3\Flow\I18n\TranslationProvider\Exception;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

/**
 * An "Invalid Plural Form" exception
 *
 * This exception is thrown when one requests translation from the translation
 * provider, passing as parameter plural form which is not used in language
 * defined in provided locale.
 *
 * @api
 */
class InvalidPluralFormException extends \TYPO3\Flow\I18n\Exception
{
}
