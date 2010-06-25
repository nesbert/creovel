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
class CArray extends CObject implements Iterator
{
    /**
     * Storage for array.
     * 
     * @var array
     **/
    private $value; 
    
    /**
     * Set value.
     *
     * @return void
     **/
    public function __construct($value = null)
    {
        // set value
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
     * Pop the element off the end of array and returns the
     * last value of array.
     *
     * @return mixed
     **/
    public function pop()
    {
        return array_pop($this->value);
    }
    
    /**
     * Push one or more elements onto the end of array and
     * returns the new number of elements in the array.
     *
     * @return integer
     **/
    public function push()
    {
        foreach (func_get_args() as $var) {
            $this->value[] = $var;
        }
        
        return func_num_args(); 
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
     * @return mixed
     **/
    public function prev()
    {
        return prev($this->value);
    }
    
    /**
     * Set the internal pointer of an array to its first element. Returns
     * the value of the first array element, or FALSE if the array is empty.
     *
     * @return mixed
     **/
    public function rewind()
    {
        return reset($this->value);
    }
        
    /**
     * Return the current element in an array.
     *
     * @return mixed
     **/
    public function current()
    {
        return current($this->value);
    }
    
    /**
     * Return the key of the current element.
     *
     * @return mixed
     **/
    public function key()
    {
        return key($this->value);
    }
    
    /**
     * Checks if current position is valid.
     *
     * @return boolean
     **/
    public function valid()
    {
        return isset($this->value[$this->key()]);
    }
    
    /**
     * Count how many elements are in the array.
     *
     * @return boolean
     **/
    public function count()
    {
        return count($this->value);
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
    public static function clean($array, $length = 0, $allowed_tags = false)
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
    public static function search($i, $val, $array)
    {
        if (is_array($array)) foreach ($array as $row) {
            if (@$row[$i] == $val) return $row;
        } else {
            return false;
        }
    }
} // END CArray extends CObject implements Iterator