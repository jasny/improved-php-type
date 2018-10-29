<?php declare(strict_types=1);

namespace Improved\Internal;

/**
 * Describe the type and value of the variable.
 *
 * @param mixed $var
 * @return string
 */
function type_describe_value($var)
{
    $type = gettype($var);

    switch ($type) {
        case 'bool':
            return 'bool (' . ($var ? 'true' : 'false') . ')';
        case 'int':
            return "int ($var)";
        case 'double':
            return "float ($var)";
        case 'string':
            return "string (" . ((strlen($var) > 32) ? substr($var, 0, 29) . '...' : $var) . ')';
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
