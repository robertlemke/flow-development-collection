<?php
namespace TYPO3\Flow\Annotations;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

/**
 * Declares a method as an around advice to be triggered around any
 * pointcut matching the given expression.
 *
 * @Annotation
 * @Target("METHOD")
 */
final class Around
{
    /**
     * The pointcut expression. (Can be given as anonymous argument.)
     * @var string
     */
    public $pointcutExpression;

    /**
     * @param array $values
     * @throws \InvalidArgumentException
     */
    public function __construct(array $values)
    {
        if (!isset($values['value']) && !isset($values['pointcutExpression'])) {
            throw new \InvalidArgumentException('An Around annotation must specify a pointcut expression.', 1318456614);
        }
        $this->pointcutExpression = isset($values['pointcutExpression']) ? $values['pointcutExpression'] : $values['value'];
    }
}
