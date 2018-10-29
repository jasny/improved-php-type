<?php declare(strict_types=1);

namespace Improved;

/**
 * Get the type of a variable in a descriptive way.
 *
 * @param mixed $var
 * @return string
 */
function type_describe($var): string
{
    $type = gettype($var);

    switch ($type) {
        case 'double':
            return 'float';
        case 'object':
            return get_class($var) . " object";
        case 'resource':
            return get_resource_type($var) . " resource";
        case 'unknown type':
            return "resource (closed)"; // BC PHP 7.1
        default:
            return $type;
    }
}
