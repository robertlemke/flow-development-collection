<?php
namespace TYPO3\Flow\Annotations;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

/**
 * Declares a named pointcut. The annotated method does not become an advice
 * but can be used as a named pointcut instead of the given expression.
 *
 * @Annotation
 * @Target("METHOD")
 */
final class Pointcut
{
    /**
     * The pointcut expression. (Can be given as anonymous argument.)
     * @var string
     */
    public $expression;

    /**
     * @param array $values
     * @throws \InvalidArgumentException
     */
    public function __construct(array $values)
    {
        if (!isset($values['value']) && !isset($values['expression'])) {
            throw new \InvalidArgumentException('A Pointcut annotation must specify a pointcut expression.', 1318456604);
        }
        $this->expression = isset($values['expression']) ? $values['expression'] : $values['value'];
    }
}
