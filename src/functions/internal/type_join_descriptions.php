<?php declare(strict_types=1);

namespace Improved\Internal;

/**
 * Join types to be displayed in an (error) message.
 * @internal
 *
 * @param string[] $types
 * @param string   $glue
 * @param string   $lastGlue
 * @return string
 */
function type_join_descriptions(array $types, string $glue, string $lastGlue)
{
    $orNull = false;

    $descs = array_map(function (string $rawType) use (&$orNull) {
        $orNull = $orNull || $rawType[0] === '?';
        $type = ltrim($rawType, '?');

        $internal = (TYPE_INTERNAL[$type] ?? false) !== false || substr($type, -9) === ' resource';

        return ($internal ? '' : 'instance of ') . ltrim($type, '?');
    }, $types);

    if ($orNull) {
        $descs[] = 'null';
    }

    $set = array_unique($descs);
    $last = (string)array_pop($set);

    return (count($set) === 0 ? "" : join($glue, $set) . $lastGlue) . $last;
}
