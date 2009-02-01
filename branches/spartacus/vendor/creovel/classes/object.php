<?php
/**
 * Extends basic functionality of class by extending functionality based on
 * data type of value (prototype). Inspired by Prototype.js the very propular
 * javascript framework created by name (http://prototypejs.com).
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
    public function __construct($id = null, $value = null)
    {
        // initialize scope fix
        $this->initialize_parents();
        
        // if id passed prototype object
        if (!is_null($id)) {
            $this->init($id, $value);
        }
    }
    
    /**
     * Waterfall initialize function routine.
     *
     * @return void
     **/
    public function initialize_parents()
    {
        $parents = get_ancestors(get_class($this));
        foreach (array_reverse($parents) as $parent) {
            $method = 'initialize_' . Inflector::underscore($parent);
            if (method_exists($parent, $method)) {
                $this->$method();
            }
        }
    }
    
    /**
     * First thing called during action execution.
     *
     * @return void
     **/
    public function initialize()
    {}
    
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
            throw new Exception("Call to undefined method <em>{$method}</em>" .
                " not found in <strong>{$this->object_name()}</strong>.");
        } catch (Exception $e) {
            
            switch (true) {
                case in_string('Controller', $this->object_name()):
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
     * Get class/object name.
     *
     * @return string
     **/
    public function object_name()
    {
        return get_class($this);
    }
} // END class Object