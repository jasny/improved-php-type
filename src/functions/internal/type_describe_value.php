<?php declare(strict_types=1);

namespace Improved\Internal;

/**
 * Describe the type and value of the variable.
 * @internal
 *
 * @param mixed $var
 * @return string
 */
function type_describe_value($var)
{
    $type = strtolower(gettype($var));

    switch ($type) {
        case 'boolean':
            return 'bool(' . ($var === true ? 'true' : 'false') . ')';
        case 'integer':
            return "int($var)";
        case 'double':
            return "float($var)";
        case 'string':
            return 'string(' . strlen($var) . ') '
                 . (strlen($var) > 32 ? '"' . substr($var, 0, 29) . '"...' : '"' . $var . '"');
        case 'array':
            return 'array(' . count($var) . ')';
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
