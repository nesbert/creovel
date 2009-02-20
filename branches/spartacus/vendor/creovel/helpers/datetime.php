<?php
/**
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
    switch (true) {
        case !$datetime:
        default:
            return date('Y-m-d H:i:s');
            break;
        
        case is_array($datetime):
            $datetime['hour'] = isset($datetime['hour'])
                                ? $datetime['hour']
                                : 0;
            $datetime['minute'] = isset($datetime['minute'])
                                ? $datetime['minute']
                                : 0;
            $datetime['second'] = isset($datetime['second'])
                                ? $datetime['second']
                                : 0;
            if (!empty($datetime['ampm'])
                && strtoupper($datetime['ampm']) == 'PM') {
                $datetime['hour'] += 12;
            }
            return date('Y-m-d H:i:s', mktime(
                                            $datetime['hour'],
                                            $datetime['minute'],
                                            $datetime['second'],
                                            $datetime['month'],
                                            $datetime['day'],
                                            $datetime['year']));
            break;
        
        case is_number($datetime):
            return date('Y-m-d H:i:s', $datetime);
            break;
        
        case is_string($datetime) && $datetime != '0000-00-00 00:00:00':
            return date('Y-m-d H:i:s', strtotime($datetime));
            break;
    }
}

/**
 * MySQL Timestamp of from current time in GMT.
 *
 * @param mixed $datetime Accepts either an array, unix timestamp or string.
 * @see datetime
 * @return string Date and time stamp.
 * @author Nesbert Hidalgo
 **/
function gmtime($datetime = null)
{
    return gmdate('Y-m-d H:i:s', ($datetime
                                    ? strtotime(datetime($datetime))
                                    : time()
                                    ));
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
    $time = time() - (is_string($time) ? strtotime($time) : $time);
    
    switch (true) {
        case ($time < MINUTE):
            $time = round(((($time % WEEK) % DAY) % HOUR) % MINUTE);
            $return = "{$time} second";
            break;
        
        case ($time < HOUR):
            $time = round(((($time % WEEK) % DAY) % HOUR) / MINUTE);
            $return = "{$time} minute";
            break;
        
        case ($time < DAY):
            $time = round((($time % WEEK) % DAY) / HOUR);
            $return = "{$time} hour";
            break;
        
        case ($time < WEEK):
            $time = round(($time % WEEK) / DAY);
            $return = "{$time} day";
            break;
        
        case ($time < WEEK * 4):
            $time = round($time / WEEK);
            $return = "{$time} week";
            break;
        
        default:
            return false;
            break;
    }
    
    return pluralize($return, $time);
}