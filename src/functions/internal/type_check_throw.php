<?php declare(strict_types=1);

namespace Improved\Internal;

/**
 * @internal
 *
 * @param mixed           $var
 * @param string|string[] $type
 * @param \Throwable      $throwable  Exception or Error
 * @throws \Throwable
 */
function type_check_throw($var, $type, \Throwable $throwable = null): void
{
    $throwable = $throwable ?? new \TypeError('Expected %2$s, %1$s given');

    if (strpos($throwable->getMessage(), '%') === false) {
        throw $throwable;
    }

    $typeDescs = array_map(function ($type) {
        return $type . (type_is_internal_func($type) !== null || substr($type, -9) === ' resource' ? '' : ' object');
    }, is_scalar($type) ? [$type] : $type);

    $last = (string)array_pop($typeDescs);
    $expected = (count($typeDescs) === 0 ? "" : join(', ', $typeDescs) . ' or ') . $last;

    $class = get_class($throwable);
    $message = sprintf($throwable->getMessage(), \Improved\type_describe($var), $expected);
    $code = $throwable->getCode();

    throw new $class($message, $code);
}
