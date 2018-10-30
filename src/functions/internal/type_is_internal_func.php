<?php declare(strict_types=1);

namespace Improved\Internal;

/**
 * Get the PHP function to check an internal type.
 * @internal
 *
 * @param string $type
 * @return callable|null
 */
function type_is_internal_func(string $type): ?callable
{
    // For some reason getting the string from an array makes it not evaluate as callable. Weird stuff in PHP 7.1.

    $fn = (TYPE_INTERNAL[$type] ?? false) !== false ? 'is_' . ($type === 'boolean' ? 'bool' : $type) : null;

    return isset($fn) && is_callable($fn) ? $fn : null;
}

/**
 * A list of PHP internal types and pseudo types.
 */
const TYPE_INTERNAL = [
    'array' => 'array',
    'bool' => 'bool',
    'boolean' => 'bool',
    'callable' => 'callable',
    'countable' => 'countable',
    'double' => 'float',
    'float' => 'float',
    'int' => 'int',
    'integer' => 'int',
    'iterable' => 'iterable',
    'long' => 'int',
    'null' => 'null',
    'numeric' => 'numeric',
    'object' => 'object',
    'real' => 'float',
    'resource' => 'resource',
    'scalar' => 'scalar',
    'string' => 'string',
];
