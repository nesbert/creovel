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
} // END CArray extends CObject