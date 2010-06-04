<?php
/**
 * WARNING
 * These functions has been DEPRECATED as of 0.4.5 and have been moved
 * to the CDate object. Relying on this feature is highly discouraged.
 *
 * Global date and time functions.
 *
 * @package     Creovel
 * @subpackage  Helpers
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.3.0
**/

/**
 * Creates/converters time into a MySQL Timestamp. <i>Note: If nothing
 * is passed use current server time</i>.
 *
 * @param mixed $datetime Accepts either an array, unix timestamp or string.
 * @return string Date and time stamp "1979-03-06 05:55:55".
 * @author Nesbert Hidalgo
 **/
function datetime($datetime = null)
{
    return CDate::datetime($datetime);
}

/**
 * Returns the current time measured in the number of seconds since the Unix Epoch
 * (January 1 1970 00:00:00 GMT) in GMT
 *
 * @return integer
 * @author John Faircloth
 **/
function gmtime()
{
    return CDate::gmtime();
}

/**
 * MySQL Timestamp of from current time in GMT.
 *
 * @param mixed $datetime Accepts either an array, unix timestamp or string.
 * @see datetime
 * @return string Date and time stamp.
 * @author Nesbert Hidalgo
 **/
function gmdatetime($datetime = null)
{
    return CDate::gmdatetime($datetime);
}

/**
 * Returns time passed. Latest activity about "8 hours" ago.
 *
 * @param mixed $time Accepts unix timestamp or datetime string.
 * @return string
 * @author Nesbert Hidalgo 
 **/
function time_ago($time)
{ 
    return CDate::timeAgo($time);
}

/**
 * Get an array of dates with key as date (Y-m-d) and value as day (D).
 *
 * @param mixed $start
 * @param mixed $end
 * @param string $key_date_format
 * @param string $value_date_format
 * @param mixed $end
 * @return Array
 * @author Nesbert Hidalgo
 **/
function date_range($start, $end = '', $key_date_format = 'Y-m-d', $value_date_format = 'D')
{
    return CDate::dateRange($start, $end, $key_date_format, $value_date_format);
}