<?php
namespace TYPO3\Flow\Tests\Functional\Property\Fixtures;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

use Doctrine\ORM\Mapping as ORM;

/**
 * A simple class for PropertyMapper test
 *
 */
class TestClass
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var integer
     */
    protected $size;

    /**
     * @var boolean
     */
    protected $signedCla;

    /**
     * This has no var annotation by intention.
     */
    public $somePublicPropertyWithoutVarAnnotation;

    /**
     * @see \TYPO3\Flow\Tests\Functional\Property\TypeConverter\ObjectConverterTest::getTypeOfChildPropertyReturnsCorrectTypeIfThatPropertyIsPubliclyPresent
     * @var float
     */
    public $somePublicProperty;

    /**
     * @see \TYPO3\Flow\Tests\Functional\Property\TypeConverter\ObjectConverterTest::convertFromUsesAppropriatePropertyPopulationMethodsInOrderConstructorSetterPublic
     * @var string
     */
    public $propertyMeantForConstructorUsage = 'default';

    /**
     * @see \TYPO3\Flow\Tests\Functional\Property\TypeConverter\ObjectConverterTest::convertFromUsesAppropriatePropertyPopulationMethodsInOrderConstructorSetterPublic
     * @var string
     */
    public $propertyMeantForSetterUsage = 'default';

    /**
     * @see \TYPO3\Flow\Tests\Functional\Property\TypeConverter\ObjectConverterTest::convertFromUsesAppropriatePropertyPopulationMethodsInOrderConstructorSetterPublic
     * @var string
     */
    public $propertyMeantForPublicUsage = 'default';

    /**
     * @see \TYPO3\Flow\Tests\Functional\Property\TypeConverter\ObjectConverterTest::getTypeOfChildPropertyReturnsCorrectTypeIfAConstructorArgumentForThatPropertyIsPresent
     * @see \TYPO3\Flow\Tests\Functional\Property\TypeConverter\ObjectConverterTest::convertFromUsesAppropriatePropertyPopulationMethodsInOrderConstructorSetterPublic
     * @param float $dummy
     * @param string $propertyMeantForConstructorUsage
     */
    public function __construct($dummy = null, $propertyMeantForConstructorUsage = null)
    {
        if ($propertyMeantForConstructorUsage !== null) {
            $this->propertyMeantForConstructorUsage = $propertyMeantForConstructorUsage . ' set via Constructor';
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param integer $size
     * @return void
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return boolean
     */
    public function getSignedCla()
    {
        return $this->signedCla;
    }

    /**
     * @param boolean $signedCla
     * @return void
     */
    public function setSignedCla($signedCla)
    {
        $this->signedCla = $signedCla;
    }

    /**
     * @see \TYPO3\Flow\Tests\Functional\Property\TypeConverter\ObjectConverterTest::getTypeOfChildPropertyReturnsCorrectTypeIfASetterForThatPropertyIsPresent
     * @param string $value
     */
    public function setAttributeWithStringTypeAnnotation($value)
    {
    }

    /**
     * @see \TYPO3\Flow\Tests\Functional\Property\TypeConverter\ObjectConverterTest::convertFromUsesAppropriatePropertyPopulationMethodsInOrderConstructorSetterPublic
     * @param string $value
     */
    public function setPropertyMeantForConstructorUsage($value)
    {
        $this->propertyMeantForConstructorUsage = $value . ' set via Setter';
    }

    /**
     * @see \TYPO3\Flow\Tests\Functional\Property\TypeConverter\ObjectConverterTest::convertFromUsesAppropriatePropertyPopulationMethodsInOrderConstructorSetterPublic
     * @param string $value
     */
    public function setPropertyMeantForSetterUsage($value)
    {
        $this->propertyMeantForSetterUsage = $value . ' set via Setter';
    }
}
