<?php declare(strict_types=1);

namespace Improved;

/**
 * Check the variable has a specific type or can be castd to that type, otherwise throw an exception.
 *
 * @param mixed           $var
 * @param string          $type
 * @param \Throwable|null $throwable Exception or Error
 * @return mixed
 */
function type_cast($var, $type, ?\Throwable $throwable = null)
{
    $valid = type_is($var, $type);
    $casted = $valid ? $var : Internal\type_cast_var($var, $type);

    if (!$valid && !isset($casted)) {
        /** @var \TypeError $error */
        $error = Internal\type_check_error($var, ltrim($type, '?'), $throwable, 'Unable to cast to %2$s, %1$s given');
        throw $error;
    }

    return $casted;
}
