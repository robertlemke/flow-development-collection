<?php
namespace TYPO3\Flow\Persistence\Doctrine\Mapping;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

use Doctrine\Common\Persistence\Mapping\ReflectionService as DoctrineReflectionService;
use TYPO3\Flow\Reflection\ClassReflection;

/**
 * A ClassMetadata instance holds all the object-relational mapping metadata
 * of an entity and it's associations.
 */
class ClassMetadata extends \Doctrine\ORM\Mapping\ClassMetadata
{
    /**
     * Gets the ReflectionClass instance of the mapped class.
     *
     * @return ClassReflection
     */
    public function getReflectionClass()
    {
        if ($this->reflClass === null) {
            $this->_initializeReflection();
        }
        return $this->reflClass;
    }

    /**
     * Initializes $this->reflClass and a number of related variables.
     *
     * @param DoctrineReflectionService $reflService
     * @return void
     */
    public function initializeReflection($reflService)
    {
        $this->_initializeReflection();
    }

    /**
     * Restores some state that can not be serialized/unserialized.
     *
     * @param DoctrineReflectionService $reflService
     * @return void
     */
    public function wakeupReflection($reflService)
    {
        parent::wakeupReflection($reflService);
        $this->reflClass = new ClassReflection($this->name);
    }

    /**
     * Initializes $this->reflClass and a number of related variables.
     *
     * @return void
     */
    protected function _initializeReflection()
    {
        $this->reflClass = new ClassReflection($this->name);
        $this->namespace = $this->reflClass->getNamespaceName();
        $this->name = $this->rootEntityName = $this->reflClass->getName();
        $this->table['name'] = $this->reflClass->getShortName();
    }
}
