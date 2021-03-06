<?php
namespace TYPO3\Flow\Persistence;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

/**
 * An empty result list
 *
 * @api
 */
class EmptyQueryResult implements  QueryResultInterface
{
    /**
     * @var \TYPO3\Flow\Persistence\QueryInterface
     */
    protected $query;

    /**
     * Constructor
     *
     * @param \TYPO3\Flow\Persistence\QueryInterface $query
     */
    public function __construct(\TYPO3\Flow\Persistence\QueryInterface $query)
    {
        $this->query = $query;
    }

    /**
     * Returns a clone of the query object
     *
     * @return \TYPO3\Flow\Persistence\QueryInterface
     * @api
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Returns NULL
     *
     * @return object Returns NULL in this case
     * @api
     */
    public function getFirst()
    {
        return null;
    }

    /**
     * Returns an empty array
     *
     * @return array
     * @api
     */
    public function toArray()
    {
        return array();
    }

    /**
     * @return object Returns NULL in this case
     */
    public function current()
    {
        return null;
    }

    /**
     * @return void
     */
    public function next()
    {
    }

    /**
     * @return integer Returns 0 in this case
     */
    public function key()
    {
        return 0;
    }

    /**
     * @return boolean Returns FALSE in this case
     */
    public function valid()
    {
        return false;
    }

    /**
     * @return void
     */
    public function rewind()
    {
    }

    /**
     * @param mixed $offset
     * @return boolean Returns FALSE in this case
     */
    public function offsetExists($offset)
    {
        return false;
    }

    /**
     * @param mixed $offset
     * @return mixed Returns NULL in this case
     */
    public function offsetGet($offset)
    {
        return null;
    }

    /**
     * @param mixed $offset The offset is ignored in this case
     * @param mixed $value The value is ignored in this case
     * @return void
     */
    public function offsetSet($offset, $value)
    {
    }

    /**
     * @param mixed $offset The offset is ignored in this case
     * @return void
     */
    public function offsetUnset($offset)
    {
    }

    /**
     * @return integer Returns 0 in this case
     */
    public function count()
    {
        return 0;
    }
}
