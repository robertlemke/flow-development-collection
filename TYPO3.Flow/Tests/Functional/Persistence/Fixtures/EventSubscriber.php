<?php
namespace TYPO3\Flow\Tests\Functional\Persistence\Fixtures;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Events;

/**
 * A sample event subscriber
 *
 * @Flow\Scope("singleton")
 */
class EventSubscriber implements \Doctrine\Common\EventSubscriber
{
    public $preFlushCalled = false;

    public $onFlushCalled = false;

    public $postFlushCalled = false;

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(Events::preFlush, Events::onFlush, Events::postFlush);
    }

    public function preFlush(\Doctrine\ORM\Event\PreFlushEventArgs $args)
    {
        $this->preFlushCalled = true;
    }

    public function onFlush(\Doctrine\ORM\Event\OnFlushEventArgs $args)
    {
        $this->onFlushCalled = true;
    }

    public function postFlush(\Doctrine\ORM\Event\PostFlushEventArgs $args)
    {
        $this->postFlushCalled = true;
    }
}
