<?php declare(strict_types=1);

namespace Improved;

/**
 * Check the variable has a specific type, otherwise throw an exception.
 *
 * @param mixed           $var
 * @param string|string[] $type
 * @param \Throwable      $throwable  Exception or Error
 * @return mixed
 */
function type_check($var, $type, \Throwable $throwable = null)
{
    if (!type_is($var, $type)) {
        Internal\type_check_throw($var, $type, $throwable);
    }

    return $var;
}
