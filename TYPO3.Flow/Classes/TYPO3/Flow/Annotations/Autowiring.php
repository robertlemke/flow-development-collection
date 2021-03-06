<?php
namespace TYPO3\Flow\Annotations;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

/**
 * Used to disable autowiring for Dependency Injection on the
 * whole class or on the annotated property only.
 *
 * @Annotation
 * @Target({"METHOD", "CLASS"})
 */
final class Autowiring
{
    /**
     * Whether autowiring is enabled. (Can be given as anonymous argument.)
     * @var boolean
     */
    public $enabled = true;

    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['enabled'])) {
            $this->enabled = (boolean)$values['enabled'];
        } elseif (isset($values['value'])) {
            $this->enabled = (boolean)$values['value'];
        }
    }
}
