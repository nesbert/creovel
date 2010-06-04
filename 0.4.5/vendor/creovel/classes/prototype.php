<?php
/**
 * Extends basic functionality of class by extending functionality based on
 * data type of value (prototype). Inspired by Prototype.js a javascript
 * framework created by Sam Stephenson.
 *
 * @package     Creovel
 * @subpackage  Prototype
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.0
 * @author      Nesbert Hidalgo
 **/
class Prototype extends CObject
{
    /**
     * Storage for object values.
     *
     * @var object
     **/
    public $_attribites_;
    
    /**
     * Initialize parents.
     *
     * @return void
     **/
    public function __construct($value = null, $name = null)
    {
        // initialize scope fix
        $this->initialize_parents();
        
        // if id passed prototype object
        if (!is_null($value)) {
            $this->initialize($value, $name);
        }
    }
    
    /**
     * Return Prototype object base on data type. Returning an object allows
     * for chaining.
     *
     * @param string $method Name of method being called.
     * @param array $arguments Arguments passed to the method being called.
     * @return object
     * @throws Exception Application error.
     **/
    public function __call($method, $arguments)
    {
        try {
            $DataType = "Prototype{$this->type}";
            switch (true) {
                case method_exists($DataType, $method):
                    $value = call_user_func_array(
                                array(new $DataType($this->value), $method),
                                $arguments);
                    switch (true) {
                        case is_bool($value):
                            return $value;
                            break;
                        
                        case ($DataType == 'PrototypeArray'
                                && $method == 'clear'):
                            $this->value = $value;
                            return new Prototype(array());
                            break;
                        
                        case $DataType == 'PrototypeArray';
                        case $DataType == 'PrototypeString';
                            return new Prototype($value);
                            break;
                    }
                    break;
                
                default:
                    throw new Exception("Call to undefined method ".
                        "<em>{$method}</em> not found in " .
                        "<strong>{$this->to_string()}</strong>.");
                    break;
            }
        } catch (Exception $e) {
            
            switch (true) {
                case in_string('Controller', $this->to_string()):
                    $error_type = 404;
                    break;
                
                default:
                    $error_type = 500;
                    break;
            }
            
            CREO('application_error_code', $error_type);
            CREO('application_error', $e);
        }
    }
    
    /**
     * Magic set function to handle special property calls.
     *
     * @access private
     * @return void
     **/
    public function __set($attribute, $value)
    {
        #echo "$attribute, $value<br>";
        switch (true) {
            case $attribute == 'value':
                $this->_attribites_->type =
                    ucwords(get_type($value));
                $this->_attribites_->value = $value;
                break;
                
            case $attribute == 'name':
                $this->_attribites_->name =
                    $value;
                break;
            
            default:
                $this->{$attribute} = $value;
                break;
        }
    }
    
    /**
     * Magic get function to handle special property calls.
     *
     * @access private
     * @return void
     **/
    public function __get($attribute)
    {
        switch (true) {
            case $attribute == 'name':
            case $attribute == 'type':
            case $attribute == 'value':
                if (isset($this->_attribites_->{$attribute}))
                    return $this->_attribites_->{$attribute};
                break;
            
            case $attribute == 'length':
                return $this->length();
                break;
                
            case $attribute == 'val':
                return $this->value;
                break;
                
            default:
                if (isset($this->{$attribute}))
                    return $this->{$attribute};
                break;
        }
    }
    
    /**
     * Get value.
     *
     * @return string
     **/
    public function to_string()
    {
        return isset($this->_attribites_->value)
                ? (string) $this->_attribites_->value
                : '';
    }
    
    /**
     * Initialize prototype.
     *
     * @param mixed $value Data to prototype.
     * @param string $name Name of object for reference only.
     * @return void
     **/
    public function initialize($value, $name = '')
    {
        if ($name) $this->name = $name;
        $this->value = $value;
    }
    
    /**
     * Get the length or an object's value.
     *
     * @return integer
     **/
    public function length()
    {
        return is_array($this->value) ? count($this->value) : strlen($this->value);
    }
    
    /**
     * Checks if value is blank/empty.
     *
     * @return boolean
     **/
    public function blank()
    {
        return is_array($this->value) ? count($this->value) > 0 : trim($this->value);
    }
} // END class Prototype extends CObject