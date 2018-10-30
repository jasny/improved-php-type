<?php /** @noinspection PhpUnhandledExceptionInspection PhpDocMissingThrowsInspection */ declare(strict_types=1);

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
    if (type_is($var, $type)) {
        return $var;
    }

    $casted = Internal\type_cast_var($var, $type);

    if (isset($casted)) {
        return $casted;
    }

    throw Internal\type_check_error(
        $var,
        ltrim($type, '?'),
        $throwable ?? new \TypeError('Unable to cast to %2$s, %1$s given')
    );
}
