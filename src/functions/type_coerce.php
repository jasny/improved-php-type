<?php declare(strict_types=1);

namespace Improved;

/**
 * Check the variable has a specific type or can be coerced to that type, otherwise throw an exception.
 *
 * @param mixed      $var
 * @param string     $type
 * @param \Throwable $throwable  Exception or Error
 * @return mixed
 */
function type_coerce($var, $type, \Throwable $throwable = null)
{
    if (type_is($var, $type)) {
        return $var;
    }

    $coerced = Internal\type_coerce_var($var, $type);

    if (!isset($coerced)) {
        Internal\type_check_throw($var, $type, $throwable);
    }

    return $coerced;
}
