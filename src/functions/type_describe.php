<?php declare(strict_types=1);

namespace Improved;

/**
 * Get the type of a variable in a descriptive way.
 *
 * @param mixed $var
 * @param bool  $detailed  Describe both the value and type of the variable
 * @return string
 */
function type_describe($var, bool $detailed = false): string
{
    return $detailed ? Internal\type_describe_value($var) : Internal\type_describe_type($var);
}
