<?php
namespace TYPO3\Flow\Tests\Functional\Persistence\Fixtures;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

/**
 * A repository for SubSubEntity
 * @TYPO3\Flow\Annotations\Scope("singleton")
 */
class SubSubEntityRepository extends \TYPO3\Flow\Persistence\Repository
{
    /**
     * @var string
     */
    const ENTITY_CLASSNAME = \TYPO3\Flow\Tests\Functional\Persistence\Fixtures\SubSubEntity::class;

    public function findAll()
    {
        $result = parent::findAll();
        foreach ($result as $instance) {
            $instance->setContent($instance->getContent() . ' - touched by SubSubEntityRepository');
        }
        return $result;
    }
}
