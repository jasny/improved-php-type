<?php declare(strict_types=1);

namespace Improved\Internal;

/**
 * Return an error or exception for a failed type check.
 * @internal
 *
 * @param mixed           $var
 * @param string|string[] $type
 * @param \Throwable      $throwable  Exception or Error
 * @param array           $extraArgs  Additional args to be parsed into message
 * @return \Throwable
 */
function type_check_error($var, $type, \Throwable $throwable, array $extraArgs = []): \Throwable
{
    if (strpos($throwable->getMessage(), '%') === false) {
        return $throwable;
    }

    $args = array_merge(
        [\Improved\type_describe($var, true)],
        $extraArgs,
        [type_join_descriptions(is_scalar($type) ? [$type] : $type, ',', ' or ')]
    );

    $class = get_class($throwable);
    $message = sprintf($throwable->getMessage(), ...$args);
    $code = $throwable->getCode();

    return new $class($message, $code);
}
