<?php
/**
 * Global validation functions.
 *
 * @package     Creovel
 * @subpackage  Creovel.Helpers
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.2.0
 **/

/**
 * Check if $var is a valid host name. Hostnames must use a-z,0-9,
 * and '-'. A hostname cannot have any spaces nor can it start
 * with a '-'.
 *
 * @param string $var Value to validate
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function is_hostname($var)
{
    return eregi('^[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$', $var)
            && $var{0} != '-' ? true : false;
}

/**
 * Checks if $var a variable is a valid email address.
 *
 * @param string $var Value to validate
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function is_email($var)
{
    $var = @explode('@', $var);
    return count($var) == 2
            && eregi('^[_a-z0-9-]+(\.[_a-z0-9-]+)*$', $var[0])
            && is_hostname($var[1]) ? true : false;
}

/**
 * Checks if $var a variable is a valid URL.
 *
 * @param string $var Value to validate
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function is_url($var)
{
    $var = @parse_url($var);
    return eregi('^(http|https|ftp)$', $var[scheme])
            && is_hostname($var[host]) ? true : false;
}

/**
 * Checks if $var a variable only contains characters A-Z or a-z
 *
 * @param string $var Value to validate
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function is_alpha($var)
{
    return preg_match('/^[a-z]+$/i', $var) ? true : false;
}

/**
 * Checks if $var only contains characters A-Z or a-z or 0-9.
 *
 * @param string $var Value to validate
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function is_alpha_numeric($var)
{
    return preg_match('/^[a-zA-Z0-9]+$/', $var) ? true : false;
}

/**
 * Checks if $var is a number.
 *
 * @param string $var Value to validate
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function is_number($var)
{
    return preg_match('/^[0-9]+?[.]?[0-9]*$/', $var) ? true : false;
}

/**
 * Checks if $var is a positive number.
 *
 * @param string $var Value to validate
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function is_positive_number($var)
{
    return is_number($var) && $var > 0 ? true : false;
}

/**
 * Checks if $var1 is equal to $var2.
 *
 * @param string $var1 Value to validate
 * @param string $var2 Value to validate against
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function is_match($var1, $var2)
{
    return $var1 == $var2;
}

/**
 * Checks if $var is between $min and $max.
 *
 * @param string $var Value to validate
 * @param integer $min Minimum number
 * @param integer $max Maximum number
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function is_between($var, $min, $max)
{
    return (is_numeric($min) && is_numeric($max))
        && ($var >= $min && $var <= $max);
}

/**
 * Checks if $var length equals $length.
 *
 * @param string $var Value to validate
 * @param integer $length
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function is_length($var, $length)
{
    return count(str_split($var)) == $length;
}

/**
 * Checks if $var length is between $min and $max.
 *
 * @param string $var Value to validate
 * @param integer $min Minimum length
 * @param integer $max Maximum length
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function is_length_between($var, $min, $max)
{
	$length = strlen($var);
	return ( $length >= $min ) && ( $length <= $max );
}