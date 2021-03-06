<?php
namespace TYPO3\Flow\Mvc;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * This is a container for all Flash Messages.
 *
 * @Flow\Scope("session")
 * @api
 */
class FlashMessageContainer
{
    /**
     * @var array
     */
    protected $messages = array();

    /**
     * Add a flash message object.
     *
     * @param \TYPO3\Flow\Error\Message $message
     * @return void
     * @Flow\Session(autoStart=true)
     * @api
     */
    public function addMessage(\TYPO3\Flow\Error\Message $message)
    {
        $this->messages[] = $message;
    }

    /**
     * Returns all currently stored flash messages.
     *
     * @param string $severity severity of messages (from \TYPO3\Flow\Error\Message::SEVERITY_* constants) to return.
     * @return array<\TYPO3\Flow\Error\Message>
     * @api
     */
    public function getMessages($severity = null)
    {
        if ($severity === null) {
            return $this->messages;
        }

        $messages = array();
        foreach ($this->messages as $message) {
            if ($message->getSeverity() === $severity) {
                $messages[] = $message;
            }
        }
        return $messages;
    }

    /**
     * Remove messages from this container.
     *
     * @param string $severity severity of messages (from \TYPO3\Flow\Error\Message::SEVERITY_* constants) to remove.
     * @return void
     * @Flow\Session(autoStart=true)
     * @api
     */
    public function flush($severity = null)
    {
        if ($severity === null) {
            $this->messages = array();
        } else {
            foreach ($this->messages as $index => $message) {
                if ($message->getSeverity() === $severity) {
                    unset($this->messages[$index]);
                }
            }
        }
    }

    /**
     * Get all flash messages (with given severity) currently available and remove them from the container.
     *
     * @param string $severity severity of the messages (One of the \TYPO3\Flow\Error\Message::SEVERITY_* constants)
     * @return array<\TYPO3\Flow\Error\Message>
     * @api
     */
    public function getMessagesAndFlush($severity = null)
    {
        $messages = $this->getMessages($severity);
        if (count($messages) > 0) {
            $this->flush($severity);
        }
        return $messages;
    }
}
