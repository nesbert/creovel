<?php
/**
 * Extends basic functionality of class by extending functionality based on
 * data type of value (prototype). Inspired by Prototype.js a javascript
 * framework created by Sam Stephenson.
 *
 * @package     Creovel
 * @subpackage  Core
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.0
 * @author      Nesbert Hidalgo
 **/
class CData extends CObject
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
            $DataType = "C{$this->type}";
            switch (true) {
                case method_exists($DataType, $method):
                    $value = call_user_func_array(
                                array(new $DataType($this->value), $method),
                                $arguments);
                    switch (true) {
                        case is_bool($value):
                            return $value;
                            break;
                        
                        case ($DataType == 'CArray'
                                && $method == 'clear'):
                            $this->value = $value;
                            return new CData(array());
                            break;
                        
                        case $DataType == 'CArray';
                        case $DataType == 'CString';
                            return new CData($value);
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
                case CValidate::in_string('Controller', $this->to_string()):
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
            case $attribute == 'val':
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
        return is_array($this->_attribites_->value)
                    ? count($this->value) : strlen($this->value);
    }
    
    /**
     * Checks if value is blank/empty.
     *
     * @return boolean
     **/
    public function blank()
    {
        return empty($this->_attribites_->value);
    }
    
    /**
     * Get object value.
     *
     * @return mixed
     **/
    public function val()
    {
        return $this->_attribites_->value;
    }
    
    /**
     * Get the data type of a variable.
     *
     * @param $var
     * @link http://us3.php.net/manual/en/function.gettype.php#78381
     * @return string
     **/
    function type($var = null)
    {
        if (isset($this) && empty($var)) {
            $var = $this->_attribites_->value;
        }
        
        if (empty($var)) return false;
        
        return
            (is_array($var) ? 'array' :
            (is_bool($var) ? 'boolean' :
            (is_float($var) ? 'float' :
            (is_int($var) ? 'integer' :
            (is_null($var) ? 'null' :
            (is_numeric($var) ? 'numeric' :
            (is_object($var) ? 'object' :
            (is_resource($var) ? 'resource' :
            (is_string($var) ? 'string' :
            'unknown' )))))))));
    }
} // END class CData extends CObject