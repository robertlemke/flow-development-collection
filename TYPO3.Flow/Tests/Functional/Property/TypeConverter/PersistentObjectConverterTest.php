<?php
namespace TYPO3\Flow\Tests\Functional\Property\TypeConverter;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

use TYPO3\Flow\Tests\FunctionalTestCase;

class PersistentObjectConverterTest extends FunctionalTestCase
{
    /**
     *
     * @var \TYPO3\Flow\Property\PropertyMapper
     */
    protected $propertyMapper;

    protected $sourceProperties = array(
        'name' => 'Christian M',
        'age' => '34',
        'averageNumberOfKids' => '0'
    );

    protected static $testablePersistenceEnabled = true;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->propertyMapper = $this->objectManager->get(\TYPO3\Flow\Property\PropertyMapper::class);
    }

    /**
     * @test
     */
    public function entityWithImmutablePropertyIsCreatedCorrectly()
    {
        $result = $this->propertyMapper->convert($this->sourceProperties, \TYPO3\Flow\Tests\Functional\Property\Fixtures\TestEntityWithImmutableProperty::class);
        $this->assertInstanceOf(\TYPO3\Flow\Tests\Functional\Property\Fixtures\TestEntityWithImmutableProperty::class, $result);
        $this->assertEquals('Christian M', $result->getName());
    }

    /**
     * @test
     */
    public function entityWithImmutablePropertyCanBeUpdatedIfImmutablePropertyIsNotGiven()
    {
        $result = $this->propertyMapper->convert($this->sourceProperties, \TYPO3\Flow\Tests\Functional\Property\Fixtures\TestEntityWithImmutableProperty::class);
        $identifier = $this->persistenceManager->getIdentifierByObject($result);
        $this->persistenceManager->add($result);
        $this->persistenceManager->persistAll();
        $this->persistenceManager->clearState();

        $update = array(
            '__identity' => $identifier,
            'age' => '25'
        );

        $result = $this->propertyMapper->convert($update, \TYPO3\Flow\Tests\Functional\Property\Fixtures\TestEntityWithImmutableProperty::class);

        $this->assertInstanceOf(\TYPO3\Flow\Tests\Functional\Property\Fixtures\TestEntityWithImmutableProperty::class, $result);
        $this->assertEquals('Christian M', $result->getName());
    }

    /**
     * @test
     */
    public function entityWithImmutablePropertyCanBeUpdatedIfImmutablePropertyIsGivenAndSameAsBefore()
    {
        $result = $this->propertyMapper->convert($this->sourceProperties, \TYPO3\Flow\Tests\Functional\Property\Fixtures\TestEntityWithImmutableProperty::class);
        $identifier = $this->persistenceManager->getIdentifierByObject($result);
        $this->persistenceManager->add($result);
        $this->persistenceManager->persistAll();
        $this->persistenceManager->clearState();

        $update = array(
            '__identity' => $identifier,
            'age' => '25',
            'name' => 'Christian M'
        );

        $result = $this->propertyMapper->convert($update, \TYPO3\Flow\Tests\Functional\Property\Fixtures\TestEntityWithImmutableProperty::class);

        $this->assertInstanceOf(\TYPO3\Flow\Tests\Functional\Property\Fixtures\TestEntityWithImmutableProperty::class, $result);
        $this->assertEquals('Christian M', $result->getName());
    }

    /**
     * @test
     * @expectedException \TYPO3\Flow\Property\Exception
     */
    public function entityWithImmutablePropertyCanNotBeUpdatedWhenImmutablePropertyChanged()
    {
        $result = $this->propertyMapper->convert($this->sourceProperties, \TYPO3\Flow\Tests\Functional\Property\Fixtures\TestEntityWithImmutableProperty::class);
        $identifier = $this->persistenceManager->getIdentifierByObject($result);
        $this->persistenceManager->add($result);
        $this->persistenceManager->persistAll();
        $this->persistenceManager->clearState();

        $update = array(
            '__identity' => $identifier,
            'age' => '25',
            'name' => 'Christian D'
        );

        $result = $this->propertyMapper->convert($update, \TYPO3\Flow\Tests\Functional\Property\Fixtures\TestEntityWithImmutableProperty::class);

        $this->assertInstanceOf(\TYPO3\Flow\Tests\Functional\Property\Fixtures\TestEntityWithImmutableProperty::class, $result);
        $this->assertEquals('Christian M', $result->getName());
    }
}
