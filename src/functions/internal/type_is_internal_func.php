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

    return isset($functions[$type]) ? 'is_' . ($type === 'boolean' ? 'bool' : $type) : null;
}
