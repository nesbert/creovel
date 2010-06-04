<?php
/**
 * WARNING!
 * These functions has been DEPRECATED as of 0.4.5 and have been moved
 * to the CValidate object. Relying on this feature is highly discouraged.
 * 
 * Global validation functions.
 *
 * @package     Creovel
 * @subpackage  Helpers
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
    return CValidate::hostname($var);
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
    return CValidate::email($var);
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
    return CValidate::url($var);
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
    return CValidate::alpha($var);
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
    return CValidate::alpha_numeric($var);
}

/**
 * Checks if $var is a number.
 *
 * @param string $var Value to validate
 * @return boolean
 * @author Nesbert Hidalgo
 **/
if (!function_exists('is_number')) {
    function is_number($var)
    {
        return CValidate::number($var);
    }
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
    return CValidate::positive_number($var);
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
    return CValidate::match($var1, $var2);
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
    return CValidate::between($var, $min, $max);
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
    return CValidate::length($var, $length);
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
    return CValidate::length_between($var, $min, $max);
}

/**
 * Finds whether a $var is a regular expression.
 *
 * @param string $var Value to validate
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function is_regex($var)
{
    return CValidate::regex($var);
}

/**
 * Finds whether a $var is an odd number.
 *
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function is_even($var)
{
    return CValidate::even($var);
}

/**
 * Finds whether a $var is an odd number.
 *
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function is_odd($var)
{
    return CValidate::odd($var);
}

/**
 * Check is request is using AJAX by checking headers.
 *
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function is_ajax()
{
    return CValidate::ajax();
}

/**
 * Check if an array is an associative array.
 *
 * @param array $_array
 * @link http://us3.php.net/manual/en/function.is-array.php#85324
 * @return boolean
 **/
function is_hash($array)
{
    return CValidate::hash($array);
}
