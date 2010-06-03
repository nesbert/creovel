<?php
/**
 * Extend functionality of an array data type.
 *
 * @package     Creovel
 * @subpackage  Prototype
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.0
 * @author      Nesbert Hidalgo
 **/
class PrototypeArray extends Prototype
{
    /**
     * Array value.
     *
     * @var array
     **/
    public $value;
    
    /**
     * Set value.
     *
     * @return void
     **/
    public function __construct($value)
    {
        $this->value = $value;
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
     * Returns the first item in the array, or undefined if the array is empty.
     *
     * @return void
     **/
    public function first()
    {
        return reset($this->value);
    }
    
    /**
     * Returns the last item in the array, or undefined if the array is empty.
     *
     * @return void
     **/
    public function last()
    {
        return end($this->value);
    }
} // END PrototypeArray extends Prototype