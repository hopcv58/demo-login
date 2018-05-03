<?php

/**
 * Convert H:i count to minutes number counter.
 * @param string $time format H:i
 * @return int
 */
function count_minute ($time)
{
    $time = explode(':', $time);

    return ($time[0]*60) + ($time[1]);
}

/**
 * Convert minutes count to H:i format.
 * @param int $minuteCount
 * @return string|false
 */
function minute_to_time ($minuteCount, $format = 'H:i')
{
    return date($format, mktime(0, $minuteCount));
}
