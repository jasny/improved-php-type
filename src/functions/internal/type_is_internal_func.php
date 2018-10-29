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

    $functions = [
        'array' => '',
        'bool' => '',
        'boolean' => '',
        'callable' => '',
        'countable' => '',
        'double' => '',
        'float' => '',
        'int' => '',
        'integer' => '',
        'iterable' => '',
        'long' => '',
        'null' => '',
        'numeric' => '',
        'object' => '',
        'real' => '',
        'resource' => '',
        'scalar' => '',
        'string' => '',
    ];

    $fn = isset($functions[$type]) ? 'is_' . ($type === 'boolean' ? 'bool' : $type) : null;

    return isset($fn) && is_callable($fn) ? $fn : null;
}
