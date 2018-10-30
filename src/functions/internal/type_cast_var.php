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
function type_cast_var($var, $type)
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

    $type = strtolower(ltrim($type, '?\\'));
    $type = $aliases[$type] ?? $type;

    $varType = $aliases[gettype($var)] ?? gettype($var);

    $fn = __NAMESPACE__ . "\\type_cast_{$varType}_{$type}";

    return is_callable($fn) ? $fn($var) : null;
}

/**
 * @internal
 * @param string $var
 * @return int|null
 */
function type_cast_string_int(string $var): ?int
{
    return is_numeric($var) && ($var + 0) <= PHP_INT_MAX ? (int)$var : null;
}

/**
 * @internal
 * @param string $var
 * @return float|null
 */
function type_cast_string_float(string $var): ?float
{
    return is_numeric($var) ? (float)$var : null;
}

/**
 * @internal
 * @param int $var
 * @return bool|null
 */
function type_cast_int_bool(int $var): ?bool
{
    return $var === 0 || $var === 1 ? (bool)$var : null;
}

/**
 * @internal
 * @param bool $var
 * @return int
 */
function type_cast_bool_int(bool $var): int
{
    return (int)$var;
}

/**
 * @internal
 * @param float $var
 * @return int|null
 */
function type_cast_float_int(float $var): ?int
{
    return $var <= PHP_INT_MAX ? (int)$var : null;
}

/**
 * @internal
 * @param int $var
 * @return float
 */
function type_cast_int_float(int $var): float
{
    return (float)$var;
}

/**
 * @internal
 * @param int $var
 * @return string
 */
function type_cast_int_string(int $var): string
{
    return (string)$var;
}

/**
 * @internal
 * @param float $var
 * @return string
 */
function type_cast_float_string(float $var): string
{
    return (string)$var;
}

/**
 * @internal
 * @param object $var
 * @return string|null
 */
function type_cast_object_string($var): ?string
{
    return method_exists($var, '__toString') ? (string)$var : null;
}

/**
 * @internal
 * @param object $var
 * @return array|null
 */
function type_cast_object_array($var): ?array
{
    return $var instanceof \stdClass ? get_object_vars($var) : null;
}

/**
 * @internal
 * @param array $var
 * @return null|\stdClass
 */
function type_cast_array_object(array $var): ?\stdClass
{
    return array_filter(array_keys($var), 'is_int') === [] ? (object)$var : null;
}

/**
 * @internal
 * @return string
 */
function type_cast_null_string(): string
{
    return '';
}

/**
 * @internal
 * @return int
 */
function type_cast_null_int(): int
{
    return 0;
}

/**
 * @internal
 * @return float
 */
function type_cast_null_float(): float
{
    return 0.0;
}

/**
 * @internal
 * @return bool
 */
function type_cast_null_bool(): bool
{
    return false;
}

/**
 * @internal
 * @return array
 */
function type_cast_null_array(): array
{
    return [];
}

/**
 * @internal
 * @return \stdClass
 */
function type_cast_null_object(): \stdClass
{
    return (object)[];
}
