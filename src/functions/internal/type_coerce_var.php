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
        'real' => 'float'
    ];

    $type = $aliases[$type] ?? ltrim($type, '/');
    $varType = $aliases[gettype($var)] ?? gettype($var);

    switch ($varType . ':' . $type) {
        case 'string:int':
            return is_numeric($var) && ($var + 0) <= PHP_INT_MAX ? (int)$var : null;
        case 'string:float':
            return is_numeric($var) ? (float)$var : null;
        case 'int:bool':
            return $var === 0 || $var === 1 ? (bool)$var : null;
        case 'bool:int':
            return (int)$var;
        case 'float:int':
            return $var <= PHP_INT_MAX ? (int)$var : null;
        case 'int:float':
            return (float)$var;
        case 'int:string':
        case 'float:string':
            return (string)$var;
        case 'object:string':
            return is_callable([$var, '__toString']) ? (string)$var : null;
        case 'array:object':
        case 'array:stdClass':
            return array_filter(array_keys($var), 'is_int') === [] ? (object)$var : null;
        case 'object:array':
        case 'object:iterable':
            return $var instanceof \stdClass ? get_object_vars($var) : null;
        default:
            return null;
    }
}
