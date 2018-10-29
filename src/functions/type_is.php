<?php declare(strict_types=1);

namespace Improved;

/**
 * Check the variable has a specific type, otherwise throw an exception.
 *
 * @param mixed           $var
 * @param string|string[] $type
 * @return bool
 */
function type_is($var, $type): bool
{
    $valid = false;
    $types = is_scalar($type) ? [$type] : $type;

    foreach ($types as &$type) {
        if ($valid) {
            break;
        }

        if (substr($type, -9) === ' resource') {
            $valid = is_resource($var) && (get_resource_type($var) === substr($type, 0, -9));
            continue;
        }

        $fn = Internal\type_is_internal_func($type);
        $valid = isset($fn) ? (bool)$fn($var) : is_a($var, $type);
    }

    return $valid;
}
