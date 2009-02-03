<?php
/**
 * Base Object class.
 *
 * @package     Creovel
 * @subpackage  Core
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.0
 * @author      Nesbert Hidalgo
 **/
class Object
{
    /**
     * Initialize parents.
     *
     * @return void
     **/
    public function __construct()
    {
        // initialize scope fix
        $this->initialize_parents();
    }
    
    /**
     * Waterfall initialize function routine. Override accordingly.
     *
     * @return void
     **/
    public function initialize_parents()
    {
        $parents = get_ancestors($this->to_string());
        foreach (array_reverse($parents) as $parent) {
            $method = 'initialize_' . Inflector::underscore($parent);
            if (method_exists($parent, $method)) {
                $this->{$method}();
            }
        }
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
                case in_string('Controller', $this->to_string()):
                    $error_type = 404;
                    break;
                
                default:
                    $error_type = 500;
                    break;
            }
            
            CREO('error_code', $error_type);
            CREO('application_error', $e);
        }
    }
    
    /**
     * undocumented function
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
     * Get class/object name.
     *
     * @return string
     **/
    public function to_string()
    {
        return get_class($this);
    }
} // END class Object