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
class Object implements Iterator
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
     * Waterfall initialize function routine.
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
     * General iterator methods using $_items_.
     */
    
    /**
     * Resets DB properties and frees result resources.
     *
     * @return void
     **/
    public function reset()
    {
        $this->rewind();
    }
    
    /**
     * Set the index to its first element in $_items_.
     *
     * @return void
     **/
    public function rewind()
    {
        $this->_index_ = 0;
    }
    
    /**
     * Return the current item in $_items_.
     *
     * @return mixed
     **/
    public function current()
    {
        $class = $this->to_string();
        $o = new $class;
        $o->load_item((object) $this->_items_[$this->_index_]);
        return $o;
    }
    
    /**
     * Returns the index element of $_items_.
     *
     * @return integer
     **/
    public function key()
    {
        return $this->_index_;
    }
    
    /**
     * Advance the $_index_ pointer by one.
     *
     * @return void
     **/
    public function next()
    {
        ++$this->_index_;
        return $this->current();
    }
    
    /**
     * Rewind the $_index_ pointer by one.
     *
     * @return object
     **/
    public function prev()
    {
        --$this->_index_;
        return $this->current();
    }
    
    /**
     * Check if current $_index_ is set in $_items_
     *
     * @return boolean
     **/
    public function valid()
    {
        return isset($this->_items_[$this->_index_]);
    }
    
    /**
     * Initialize iterator properties.
     *
     * @return void
     **/
    final public function initialize_iterator()
    {
        $this->_index_ = 0;
        $this->_items_ = array();
    }
    
    /**
     * Check if this object has $_items_.
     *
     * @return boolean
     **/
    final public function has_items()
    {
        return isset($this->_items_) && is_array($this->_items_);
    }
    
    /**
     * Get object $_items_.
     *
     * @return array
     **/
    final public function get_items()
    {
        return $this->has_items() ? $this->_items_ : array();
    }
    
    /**
     * Add an item to $_items_.
     *
     * @return void
     **/
    final public function load_item($item)
    {
        if (!isset($this->_index_)) $this->initialize_iterator();
        $this->_items_[] = $item;
    }
    
    /**
     * Add an array items to $_items_.
     *
     * @return void
     **/
    final public function load_items($items)
    {
        if (!is_array($items)) return;
        if (!isset($this->_index_)) $this->initialize_iterator();
        $this->_items_ += $items;
    }
    
    /**
     * General magic methods.
     */
    
    /**
     * General __get routine.
     *
     * @return void
     **/
    public function __get($property)
    {
        // make sure items is set
        if (isset($this->_items_)) {
            // check for property
            if (isset($this->_items_[$this->_index_]->{$property})) {
                if (is_object($this->_items_[$this->_index_])) {
                    return $this->_items_[$this->_index_]->{$property};
                } else {
                    return $this->_items_[$this->_index_][$property];
                }
            }
        }
    }
    
} // END class Object implements Iterator