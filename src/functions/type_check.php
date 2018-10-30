<?php declare(strict_types=1);

namespace Improved;

/**
 * Check the variable has a specific type, otherwise throw an exception.
 *
 * @param mixed           $var
 * @param string|string[] $type
 * @param \Throwable|null $throwable  Exception or Error
 * @return mixed
 */
function type_check($var, $type, ?\Throwable $throwable = null)
{
    if (!type_is($var, $type)) {
        /** @var \TypeError $error */
        $error = Internal\type_check_error($var, $type, $throwable);
        throw $error;
    }

    return $var;
}
