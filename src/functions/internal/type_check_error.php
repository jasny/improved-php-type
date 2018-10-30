<?php declare(strict_types=1);

namespace Improved\Internal;

/**
 * Return an error or exception for a failed type check.
 * @internal
 *
 * @param mixed           $var
 * @param string|string[] $type
 * @param \Throwable|null $throwable   Exception or Error
 * @param string          $defaultMsg  Default error message
 * @param array           $extraArgs   Additional args to be parsed into message
 * @return \Throwable
 */
function type_check_error(
    $var,
    $type,
    ?\Throwable $throwable = null,
    string $defaultMsg = 'Expected %2$s, %1$s given',
    array $extraArgs = []
): \Throwable
{
    $message = (isset($throwable) ? $throwable->getMessage() : '') ?: $defaultMsg;

    if (strpos($message, '%') === false) {
        return $throwable;
    }

    $args = array_merge(
        [\Improved\type_describe($var, true)],
        $extraArgs,
        [type_join_descriptions(is_scalar($type) ? [$type] : $type, ',', ' or ')]
    );

    $class = isset($throwable) ? get_class($throwable) : \TypeError::class;
    $code = isset($throwable) ? $throwable->getCode() : 0;

    return new $class(sprintf($message ?: $message, ...$args), $code);
}
