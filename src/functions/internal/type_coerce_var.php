<?php declare(strict_types=1);

namespace Improved\Internal;

/**
 * Try coercing variable to given type.
 * Must be sensible.
 * @internal
 *
 * @param mixed  $var
 * @param string $type
 * @return mixed
 */
function type_coerce_var($var, $type)
{
    $aliases = [
        'boolean' => 'bool',
        'integer' => 'int',
        'long' => 'int',
        'double' => 'float',
        'real' => 'float',
        'stdclass' => 'object',
        'iterable' => 'array'
    ];

    $type = $aliases[strtolower(ltrim($type, '/'))] ?? ltrim($type, '/');
    $varType = $aliases[gettype($var)] ?? gettype($var);

    $fn = __NAMESPACE__ . "\\type_coerce_{$varType}_{$type}";

    return is_callable($fn) ? $fn($var) : null;
}

/**
 * @internal
 */
function type_coerce_string_int(string $var): ?int
{
    return is_numeric($var) && ($var + 0) <= PHP_INT_MAX ? (int)$var : null;
}

/**
 * @internal
 */
function type_coerce_string_float(string $var): ?float
{
    return is_numeric($var) ? (float)$var : null;
}

/**
 * @internal
 */
function type_coerce_int_bool(int $var): ?bool
{
    return $var === 0 || $var === 1 ? (bool)$var : null;
}

/**
 * @internal
 */
function type_coerce_bool_int(bool $var): int
{
    return (int)$var;
}

/**
 * @internal
 */
function type_coerce_float_int(float $var): ?int
{
    return $var <= PHP_INT_MAX ? (int)$var : null;
}

/**
 * @internal
 */
function type_coerce_int_float(int $var): float
{
    return (float)$var;
}

/**
 * @internal
 */
function type_coerce_int_string(int $var): string
{
    return (string)$var;
}

/**
 * @internal
 */
function type_coerce_float_string(float $var): string
{
    return (string)$var;
}

/**
 * @internal
 * @param object $var
 * @return string|null
 */
function type_coerce_object_string($var): ?string
{
    return is_callable([$var, '__toString']) ? (string)$var : null;
}

/**
 * @internal
 * @param object $var
 * @return array|null
 */
function type_coerce_object_array($var): ?array
{
    return $var instanceof \stdClass ? get_object_vars($var) : null;
}

/**
 * @internal
 */
function type_coerce_array_object(array $var): ?\stdClass
{
    return array_filter(array_keys($var), 'is_int') === [] ? (object)$var : null;
}
