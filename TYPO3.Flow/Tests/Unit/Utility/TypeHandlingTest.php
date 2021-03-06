<?php
namespace TYPO3\Flow\Tests\Unit\Utility;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

use TYPO3\Flow\Tests\UnitTestCase;
use TYPO3\Flow\Utility\TypeHandling;

/**
 * Testcase for the Utility\TypeHandling class
 *
 */
class TypeHandlingTest extends UnitTestCase
{
    /**
     * @test
     * @expectedException \TYPO3\Flow\Utility\Exception\InvalidTypeException
     */
    public function parseTypeThrowsExceptionOnInvalidType()
    {
        TypeHandling::parseType('something not a type');
    }

    /**
     * @test
     * @expectedException \TYPO3\Flow\Utility\Exception\InvalidTypeException
     */
    public function parseTypeThrowsExceptionOnInvalidElementTypeHint()
    {
        TypeHandling::parseType('string<integer>');
    }

    /**
     * data provider for parseTypeReturnsArrayWithInformation
     */
    public function types()
    {
        return array(
            array('int', array('type' => 'integer', 'elementType' => null)),
            array('string', array('type' => 'string', 'elementType' => null)),
            array('DateTime', array('type' => 'DateTime', 'elementType' => null)),
            array('TYPO3\Foo\Bar', array('type' => 'TYPO3\Foo\Bar', 'elementType' => null)),
            array('\TYPO3\Foo\Bar', array('type' => 'TYPO3\Foo\Bar', 'elementType' => null)),
            array('array<integer>', array('type' => 'array', 'elementType' => 'integer')),
            array('ArrayObject<string>', array('type' => 'ArrayObject', 'elementType' => 'string')),
            array('SplObjectStorage<TYPO3\Foo\Bar>', array('type' => 'SplObjectStorage', 'elementType' => 'TYPO3\Foo\Bar')),
            array('SplObjectStorage<\TYPO3\Foo\Bar>', array('type' => 'SplObjectStorage', 'elementType' => 'TYPO3\Foo\Bar')),
            array('Doctrine\Common\Collections\Collection<\TYPO3\Foo\Bar>', array('type' => 'Doctrine\Common\Collections\Collection', 'elementType' => 'TYPO3\Foo\Bar')),
            array('Doctrine\Common\Collections\ArrayCollection<\TYPO3\Foo\Bar>', array('type' => 'Doctrine\Common\Collections\ArrayCollection', 'elementType' => 'TYPO3\Foo\Bar')),
        );
    }

    /**
     * @test
     * @dataProvider types
     */
    public function parseTypeReturnsArrayWithInformation($type, $expectedResult)
    {
        $this->assertEquals(
            $expectedResult,
            TypeHandling::parseType($type),
            'Failed for ' . $type
        );
    }

    /**
     * data provider for extractCollectionTypeReturnsOnlyTheMainType
     */
    public function compositeTypes()
    {
        return array(
            array('integer', 'integer'),
            array('int', 'int'),
            array('array', 'array'),
            array('ArrayObject', 'ArrayObject'),
            array('SplObjectStorage', 'SplObjectStorage'),
            array('Doctrine\Common\Collections\Collection', 'Doctrine\Common\Collections\Collection'),
            array('Doctrine\Common\Collections\ArrayCollection', 'Doctrine\Common\Collections\ArrayCollection'),
            array('array<\Some\Other\Class>', 'array'),
            array('ArrayObject<int>', 'ArrayObject'),
            array('SplObjectStorage<\object>', 'SplObjectStorage'),
            array('Doctrine\Common\Collections\Collection<ElementType>', 'Doctrine\Common\Collections\Collection'),
            array('Doctrine\Common\Collections\ArrayCollection<>', 'Doctrine\Common\Collections\ArrayCollection'),
        );
    }


    /**
     * @test
     * @dataProvider compositeTypes
     */
    public function extractCollectionTypeReturnsOnlyTheMainType($type, $expectedResult)
    {
        $this->assertEquals(
            $expectedResult,
            TypeHandling::truncateElementType($type),
            'Failed for ' . $type
        );
    }

    /**
     * data provider for normalizeTypesReturnsNormalizedType
     */
    public function normalizeTypes()
    {
        return array(
            array('int', 'integer'),
            array('double', 'float'),
            array('bool', 'boolean'),
            array('string', 'string')
        );
    }

    /**
     * @test
     * @dataProvider normalizeTypes
     */
    public function normalizeTypesReturnsNormalizedType($type, $normalized)
    {
        $this->assertEquals(TypeHandling::normalizeType($type), $normalized);
    }

    /**
     * data provider for isLiteralReturnsFalseForNonLiteralTypes
     */
    public function nonLiteralTypes()
    {
        return array(
            array('DateTime'),
            array('\Foo\Bar'),
            array('array'),
            array('ArrayObject'),
            array('stdClass')
        );
    }

    /**
     * @test
     * @dataProvider nonliteralTypes
     */
    public function isLiteralReturnsFalseForNonLiteralTypes($type)
    {
        $this->assertFalse(TypeHandling::isLiteral($type), 'Failed for ' . $type);
    }

    /**
     * data provider for isLiteralReturnsTrueForLiteralType
     */
    public function literalTypes()
    {
        return array(
            array('integer'),
            array('int'),
            array('float'),
            array('double'),
            array('boolean'),
            array('bool'),
            array('string')
        );
    }

    /**
     * @test
     * @dataProvider literalTypes
     */
    public function isLiteralReturnsTrueForLiteralType($type)
    {
        $this->assertTrue(TypeHandling::isLiteral($type), 'Failed for ' . $type);
    }

    /**
     * data provider for isCollectionTypeReturnsTrueForCollectionType
     */
    public function collectionTypes()
    {
        return array(
            array('integer', false),
            array('int', false),
            array('float', false),
            array('double', false),
            array('boolean', false),
            array('bool', false),
            array('string', false),
            array('SomeClassThatIsUnknownToPhpAtThisPoint', false),
            array('array', true),
            array('ArrayObject', true),
            array('SplObjectStorage', true),
            array('Doctrine\Common\Collections\Collection', true),
            array('Doctrine\Common\Collections\ArrayCollection', true)
        );
    }

    /**
     * @test
     * @dataProvider collectionTypes
     */
    public function isCollectionTypeReturnsTrueForCollectionType($type, $expected)
    {
        $this->assertSame($expected, TypeHandling::isCollectionType($type), 'Failed for ' . $type);
    }
}
