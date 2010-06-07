<?php
/**
 * Extend functionality of an array data type.
 *
 * @package     Creovel
 * @subpackage  Core
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.0
 * @author      Nesbert Hidalgo
 **/
class CArray extends CObject
{
    /**
     * Set value.
     *
     * @return void
     **/
    public function __construct($value = null)
    {
        $this->value = (array) $value;
    }
    
    /**
     * Clears the array (makes it empty).
     *
     * @return null
     **/
    public function clear()
    {
        return $this->value = array();
    }
    
    /**
     * Returns the first item in the array, or false if the array is empty.
     *
     * @return mixed
     **/
    public function first()
    {
        return reset($this->value);
    }
    
    /**
     * Returns the last item in the array, or false if the array is empty.
     *
     * @return mixed
     **/
    public function last()
    {
        return end($this->value);
    }
    
    /**
     * Returns the next item in the array, or false if the array is empty.
     *
     * @return mixed
     **/
    public function next()
    {
        return next($this->value);
    }
    
    /**
     * Returns the prev item in the array, or false if the array is empty.
     *
     * @return void
     **/
    public function prev()
    {
        return prev($this->value);
    }
    
    /**
     * Sanitize associative array values.
     *
     * @param array $array
     * @param string $length
     * @param string $allowed_tags
     * @return array
     * @see CString::clean()
     * @author Nesbert Hidalgo
     **/
    public function clean($array, $length = 0, $allowed_tags = false)
    {
        if (is_array($array)) {
            foreach ($array as $k => $v) {
                $array[$k] = is_array($v)
                                ? self::clean($v, $length, $allowed_tags)
                                : CString::clean($v, $length, $allowed_tags);
            }
            return $array;
        } else {
            return CString::clean($array, $length, $allowed_tags);
        }
    }

    /**
     * Search a multidimensional array for a certain value and return the
     * array with the match.
     *
     * @param mixed $i
     * @param mixed $val
     * @param array $array
     * @return mixed/false
     * @author Nesbert Hidalgo
     **/
    public function search($i, $val, $array)
    {
        if (is_array($array)) foreach ($array as $row) {
            if (@$row[$i] == $val) return $row;
        } else {
            return false;
        }
    }
} // END CArray extends CObject