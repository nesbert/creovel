<?php
/**
 * Base CObject class.
 *
 * @package     Creovel
 * @subpackage  Core
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.0
 * @author      Nesbert Hidalgo
 **/
class CObject
{
    /**
     * Initialize parents. Override accordingly.
     *
     * @return void
     **/
    public function __construct()
    {
        // initialize scope fix
        $this->initialize_parents();
    }
    
    /**
     * Magic function call being used to catch controller errors.
     *
     * @param string $method Name of method being called.
     * @param array $arguments Arguments passed to the method being called.
     * @return void
     * @throws Exception Application error.
     **/
    public function __call($method, $arguments)
    {
        try {
            throw new Exception("Call to undefined method ".
                        "<em>{$method}</em> not found in " .
                        "<strong>{$this->to_string()}</strong>.");
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
     * Clone class.
     *
     * @return void
     **/
    public function __clone()
    {
        return $this;
    }
    
    /**
     * Safely return class name as string.
     *
     * @return string
     **/
    public function __toString()
    {
        return $this->to_string();
    }
    
    /**
     * Get class/object name. Child classes can override accordingly to change
     * __toString() behavior.
     *
     * @return string
     **/
    public function to_string()
    {
        return get_class($this);
    }
    
    /**
     * Convert class name to string path.
     *
     * @return string
     **/
    public function to_string_path()
    {
        return CString::patherize($this->to_string());
    }
    
    /**
     * Waterfall initialize function routine.
     *
     * @return void
     **/
    public function initialize_parents()
    {
        $parents = self::ancestors($this->to_string());
        foreach (array_reverse($parents) as $parent) {
            $method = 'initialize_' . Inflector::underscore($parent);
            if (method_exists($parent, $method)) {
                $this->{$method}();
            }
        }
    }
    
    /**
     * Stop the application and display/handle error. Override accordingly.
     *
     * @return void
     **/
    public function throw_error($msg = null)
    {
        if (!$msg) {
            $msg = 'An error occurred while executing a method in ' .
                "<strong>{$this->to_string()}</strong> class.";
        }
        CREO('application_error', $msg);
    }
    
    /**
     * Prints human-readable information about a variable much prettier.
     *
     * @param mixed $obj The value to print out
     * @param boolean $kill Die after print out to screen.
     * @return void
     * @author John Faircloth
     **/
    public function debug($obj, $kill = false)
    {
        echo CTag::create('pre', array('class' => 'debug'), "\n".print_r($obj, 1)). "\n";
        if ($kill) die;
    }
    
    /**
     * Returns an array user defined constants.
     *
     * @return array
     * @author Nesbert Hidalgo
     **/
    public function user_defined_constants()
    {
        $return = get_defined_constants(true);
        return $return['user'];
    }
    
    /**
     * Get an array of all class parents.
     *
     * @link http://us.php.net/manual/en/function.get-parent-class.php#57548
     * @return array
     **/
    public function ancestors($class)
    {
        $classes = array($class);
        while($class = get_parent_class($class)) { $classes[] = $class; }
        return $classes;
    }
} // END class CObject