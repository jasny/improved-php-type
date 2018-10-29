<?php declare(strict_types=1);

namespace Improved;

/**
 * Check the variable has a specific type, otherwise throw an exception.
 *
 * @param mixed           $var
 * @param string|string[] $type
 * @param \Throwable|null $throwable  Exception or Error
 * @return mixed
 * @throws \Throwable
 */
function type_check($var, $type, ?\Throwable $throwable = null)
{
    if (!type_is($var, $type)) {
        throw Internal\type_check_error($var, $type, $throwable ?? new \TypeError('Expected %2$s, %1$s given'));
    }

    return $var;
}
