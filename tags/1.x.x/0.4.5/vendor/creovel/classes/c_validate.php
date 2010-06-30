<?php
/**
 * Base Validate class.
 *
 * @package     Creovel
 * @subpackage  Core
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.5
 * @author      Nesbert Hidalgo
 **/
class CValidate extends CObject
{
    /**
     * Check if $var is a valid host name. Hostnames must use a-z,0-9,
     * and '-'. A hostname cannot have any spaces nor can it start
     * with a '-'.
     *
     * @param string $var Value to validate
     * @return boolean
     * @author Nesbert Hidalgo
     **/
    public static function hostname($var)
    {
        return preg_match('/^[A-Za-z0-9-]+([\.A-Za-z0-9-]+)$/', $var)
                && $var{0} != '-' ? true : false;
    }
    
    /**
     * Check if $var is a valid domain name.
     *
     * @param string $var Value to validate
     * @return boolean
     * @author Nesbert Hidalgo
     **/
    public static function domain($var)
    {
        return preg_match('/^[A-Za-z0-9-]+(\.[A-Za-z0-9-]+)*(\.[A-Za-z]{2,4})$/', $var)
                && $var{0} != '-' ? true : false;
    }
    
    /**
     * Checks if $var a variable is a valid email address.
     *
     * @param string $var Value to validate
     * @return boolean
     * @author Nesbert Hidalgo
     **/
    public static function email($var)
    {
        $var = @explode('@', $var);
        return count($var) == 2
                && preg_match('/^[_A-Za-z0-9-]+(\.[_A-Za-z0-9-]+)*$/', $var[0])
                && self::domain($var[1]) ? true : false;
    }
    
    /**
     * Checks if $var a variable is a valid URL.
     *
     * @param string $var Value to validate
     * @return boolean
     * @author Nesbert Hidalgo
     **/
    public static function url($var)
    {
        $var = @parse_url($var);
        
        return (isset($var['scheme']) && isset($var['host']))
                && preg_match('/^(http|https|ftp)$/', $var['scheme'])
                && self::hostname($var['host']) ? true : false;
    }
    
    /**
     * Checks if $var a variable only contains characters A-Z or a-z
     *
     * @param string $var Value to validate
     * @return boolean
     * @author Nesbert Hidalgo
     **/
    public static function alpha($var)
    {
        if (!is_string($var)) return false;
        return preg_match('/^[a-z]+$/i', $var) ? true : false;
    }
    
    /**
     * Checks if $var only contains characters A-Z or a-z or 0-9.
     *
     * @param string $var Value to validate
     * @return boolean
     * @author Nesbert Hidalgo
     **/
    public static function alpha_numeric($var)
    {
        if (!is_string($var) && !is_numeric($var)) return false;
        return preg_match('/^[a-zA-Z0-9]+$/', $var) ? true : false;
    }
    
    /**
     * Checks if $var is a number.
     *
     * @param string $var Value to validate
     * @return boolean
     * @author Nesbert Hidalgo
     **/
    public static function number($var)
    {
        if (!is_string($var) && !is_numeric($var)) return false;
        return preg_match('/^[0-9]+?[.]?[0-9]*$/', $var) ? true : false;
    }
    
    /**
     * Checks if $var is a positive number.
     *
     * @param string $var Value to validate
     * @return boolean
     * @author Nesbert Hidalgo
     **/
    public static function positive_number($var)
    {
        return self::number($var) && $var > 0;
    }
    
    /**
     * Checks if $var1 is equal to $var2.
     *
     * @param string $var1 Value to validate
     * @param string $var2 Value to validate against
     * @return boolean
     * @author Nesbert Hidalgo
     **/
    public static function match($var1, $var2)
    {
        return $var1 === $var2;
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
    public static function between($var, $min, $max)
    {
        return (is_numeric($min) && is_numeric($max))
            && ($var >= $min && $var <= $max);
    }
    
    /**
     * Checks if $var length equals $length.
     *
     * @param string/array $var Value to validate
     * @param integer $length
     * @return boolean
     * @author Nesbert Hidalgo
     **/
    public static function length($var, $length)
    {
        if (is_string($var)) {
           return count(str_split($var)) == $length;
        } elseif (is_array($var)) {
          return count($var) == $length;
        }
        return false;
    }
    
    /**
     * Checks if $var length is between $min and $max.
     *
     * @param string/array $var Value to validate
     * @param integer $min Minimum length
     * @param integer $max Maximum length
     * @return boolean
     * @author Nesbert Hidalgo
     **/
    public static function length_between($var, $min, $max)
    {
        if (is_string($var)) {
            $length = strlen($var);
        } elseif (is_array($var)) {
            $length = count($var);
        } else {
            return false;
        }
        return ( $length >= $min ) && ( $length <= $max );
    }
    
    /**
     * Finds whether a $var is a regular expression.
     *
     * @param string $var Value to validate
     * @return boolean
     * @author Nesbert Hidalgo
     **/
    public static function regex($var)
    {
        @preg_match($var, '', $test);
        return is_array($test);
    }
    
    /**
     * Finds whether a $var is an odd number.
     *
     * @param integer $var Value to validate
     * @return boolean
     * @author Nesbert Hidalgo
     **/
    public static function even($var)
    {
        if (!self::number($var)) return false;
        return !self::odd($var);
    }
    
    /**
     * Finds whether a $var is an odd number.
     *
     * @param integer $var Value to validate
     * @return boolean
     * @author Nesbert Hidalgo
     **/
    public static function odd($var)
    {
        if (!self::number($var)) return false;
        return ($var % 2) == 1;
    }
    
    /**
     * Check is request is using AJAX by checking headers.
     *
     * @return boolean
     * @author Nesbert Hidalgo
     **/
    public static function ajax()
    {
        return ((!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
                && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) ==
                    'xmlhttprequest')
                || @$_REQUEST['_AJAX_']);
    }
    
    /**
     * Check if an array is an associative array.
     *
     * @param array $_array
     * @link http://us3.php.net/manual/en/function.is-array.php#85324
     * @return boolean
     **/
    public static function hash($array)
    {
        if (is_array($array) == false) {
            return false;
        }
        
        foreach (array_keys($array) as $k => $v) {
            if ($k !== $v) return true;
        }
        
        return false;
    }
    
    /**
     * A faster/less memory substitute for strstr() or preg_match
     * used to check the occurrence of a subject in a string.
     *
     * @param string $needle
     * @param array $haystack
     * @return boolean
     * @author Nesbert Hidalgo
     **/
    public static function in_string($needle, $haystack)
    {
        return CString::contains($needle, $haystack);
    }
} // END class CValidate extends CObject