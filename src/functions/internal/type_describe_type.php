<?php declare(strict_types=1);

namespace Improved\Internal;

/**
 * Describe the type of a variable only.
 * @internal
 *
 * @param mixed $var
 * @return string
 */
function type_describe_type($var): string
{
    $type = strtolower(gettype($var));

    switch ($type) {
        case 'double':
            return 'float';
        case 'object':
            return "instance of " . get_class($var);
        case 'resource':
            return get_resource_type($var) . " resource";
        case 'unknown type':
            return "resource (closed)"; // BC PHP 7.1
        default:
            return $type;
    }
}
