<?php
namespace TYPO3\Flow\Tests\Unit\Validation\Validator;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

require_once('AbstractValidatorTestcase.php');

/**
 * Testcase for the locale identifier validator
 *
 */
class LocaleIdentifierValidatorTest extends \TYPO3\Flow\Tests\Unit\Validation\Validator\AbstractValidatorTestcase
{
    protected $validatorClassName = \TYPO3\Flow\Validation\Validator\LocaleIdentifierValidator::class;

    /**
     * @test
     */
    public function localeIdentifierReturnsNoErrorIfLocaleIsEmpty()
    {
        $this->assertFalse($this->validator->validate('')->hasErrors());
        $this->assertFalse($this->validator->validate(null)->hasErrors());
    }

    /**
     * @test
     */
    public function localeIdentifierReturnsNoErrorIfLocaleIsValid()
    {
        $this->assertFalse($this->validator->validate('de_DE')->hasErrors());
        $this->assertFalse($this->validator->validate('en_Latn_US')->hasErrors());
        $this->assertFalse($this->validator->validate('de')->hasErrors());
        $this->assertFalse($this->validator->validate('AR-arab_ae')->hasErrors());
    }

    /**
     * @test
     */
    public function localeIdentifierReturnsErrorIfLocaleIsInvalid()
    {
        $this->assertTrue($this->validator->validate('ThisIsOfCourseNoValidLocaleIdentifier')->hasErrors());
    }
}
