<?php
/**
 * Created by TuyenNV.
 * Date: 6/3/2016
 * Time: 1:36 PM
 */

/**
 * Move an item of array to new position.
 * @param array $array
 * @param int $sign key or position of item
 * @param int $newPosition new position was moved.
 * @param bool $positionFlag Set true if wanth move by old position to new postion.
 * @return array
 */
function array_move(array $array, $sign, $newPosition, $positionFlag = false)
{
    // Default position is 0.
    $possition = 0;

    // Default result is empty array.
    $resultArray = [];

    // With $sign is position.
    if ($positionFlag) {
        // Has key by postion.
        $movedKey = array_keys($array)[$sign];

        // Add data to result array.
        foreach ($array as $key => $item) {
            if ($possition == $newPosition) {
                $resultArray[$movedKey] = $array[$movedKey];
            }

            if ($possition != $sign) {
                $resultArray[$key] = $item;
            }

            $possition++;
        }
    // With $sign is key.
    } else {
        // Add data to result array.
        foreach ($array as $key => $item) {
            if ($possition == $newPosition) {
                $resultArray[$sign] = $array[$sign];
            }

            if ($key != $sign) {
                $resultArray[$key] = $item;
            }

            $possition++;
        }
    }

    return $resultArray;
}

/**
 * Rename keys of array.
 * @param array $array
 * @param array $mapName EX: ['old_key' => 'new_key', ...]
 * @return array
 */
function array_rename_keys(array $array, $mapName)
{
    // Default result is empty array.
    $newArray = [];

    // Add data to result array.
    foreach ($array as $key => $value)
    {
        if (array_key_exists($key, $mapName)) {
            $newArray[$mapName[$key]] = $value;
        } else {
            $newArray[$key] = $value;
        }
    }

    return $newArray;
}

/**
 * Merge array2 to array1 if same key in array1
 * @param array $array1
 * @param array $array2
 * @return array
 */
function array_left_merge(array $array1, array $array2)
{
    foreach ($array2 as $key => $value) {
        if (array_key_exists($key, $array1)) {
            $array1[$key] = $value;
        }
    }

    return $array1;
}

/**
 * Vertical rotate a array.
 * @param array $flatten
 * @return array Vertical rotate a array.
 */
function array_rotate(array $flatten)
{
    return call_user_func_array('array_map', array_merge([null], $flatten));
}

/**
 * Parse to full array.
 * @param mixed $list
 * @return array converted
 */
function arrayval($list)
{
    if (is_array($list)) {
        // Parse item of list to array.
        array_walk_recursive(
            $list,
            function (&$item) {
                if (is_object($item)) {
                    $item = (array) $item;
                }
            }
        );
    } else {
        // Pasre list to array.
        $list = (array) $list;
    }

    return $list;
}

/**
 * Filter flatten array by condition.
 * @param array $flatten multi-dimensional array (record set) from which to pull a column of values.
 * @param array $condition EX: ['id' => 1, 'name' => 'John']
 * @return array filtered
 */
function flatten_filter ($flatten, $condition)
{
    // Default result is empty array.
    $result = [];

    // Add data to result array.
    foreach ($flatten as $key => $item) {
        $checker = true;

        foreach ($condition as $field => $value) {
            $checker &= $item[$field] == $value;
        }

        if ($checker) {
            $result[$key] = $item;
        }
    }

    return $result;
}

/**
 * Soft flatten array.
 *  Example: $arr2 = array_msort($data, ['collumn1'=>SORT_DESC, 'collumn2'=>SORT_ASC]);
 * @param array $array
 * @param array $cols
 * @return array
 */
function array_msort(array $array, array $cols)
{
    $colarr = [];

    foreach ($cols as $col => $order) {
        $colarr[$col] = [];

        foreach ($array as $k => $row) {
            $colarr[$col]['_'.$k] = strtolower($row[$col]);
        }
    }

    $eval = 'array_multisort(';

    foreach ($cols as $col => $order) {
        $eval .= '$colarr[\''.$col.'\'],'.$order.',';
    }

    $eval = substr($eval,0,-1).');';

    eval($eval);

    $ret = [];

    foreach ($colarr as $col => $arr) {
    
        foreach ($arr as $k => $v) {
            $k = substr($k,1);
        
            if (!isset($ret[$k])) $ret[$k] = $array[$k];
        
            $ret[$k][$col] = $array[$k][$col];
        }
    }

    return $ret;
}

/**
 * Soft flatten array by one collumn
 * @param array $flatten
 * @param string $collumn_key
 * @param int $sort SORT_ASC or SORT_DESC
 * @return mixed
 */
function flatten_sort($flatten, $collumn_key, $sort = SORT_ASC)
{
    usort($flatten, function ($item1, $item2) use ($collumn_key, $sort) {
        if ($sort == SORT_ASC) {
            return $item1[$collumn_key] - $item2[$collumn_key];

        } else {
            return $item2[$collumn_key] - $item1[$collumn_key];
        }
    });

    return $flatten;
}

/**
 * Convert array to object recursive.
 * @param array $array
 * @return object
 */
function array_to_object (array $array)
{
    return json_decode(json_encode($array), false);
}

/**
 * Check target is flatten.
 * @param array $array
 * @return bool
 */
function is_flatten ($array)
{
    return (is_array($array) and is_array(reset($array)));
}

/**
 * Check value is flatten.
 * @param array $flatten
 * @param bool $relatively.
 * @return bool
 */
function is_array_result ($flatten, $relatively = true)
{
    // Target is not array.
    if (! is_array($flatten)) {
        return false;
    }

    // Flatten 1 item.
    if (count($flatten) === 1 and is_array(reset($flatten))) {
        return true;
    }

    // Check relatively.
    if ($relatively) {
        $current = current($flatten);
        $next = next($flatten);
        $end = end($flatten);

        if (
            ! is_array($current)
            or ! is_array($next)
            or ! is_array($end)
            or ($keys = array_keys($current)) != array_keys($next)
            or $keys != array_keys($end)
        ) {
            return false;
        }

    } else {
        // Check 100%.
        while($current = current($flatten) and $next = next($flatten)) {
            if (! is_array($current) or ! is_array($next) or array_keys($current) != array_keys($next)) {
                return false;
            }
        }
    }

    return true;
}

/**
 * Get try get value by list keys.
 * @param array $array
 * @param array $keys
 * @return mixed|null
 */
function array_try (array $array, array $keys)
{
    foreach ($keys as $key) {
        if (isset($array[$key])) {
            return $array[$key];
        }
    }

    return null;
}