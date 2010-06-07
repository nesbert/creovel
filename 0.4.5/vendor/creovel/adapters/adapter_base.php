<?php
/**
 * Adapters base class.
 *
 * @package     Creovel
 * @subpackage  Adapters
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.0
 * @author      Nesbert Hidalgo
 **/
 
// include adapter interface.
require_once 'adapter_interface.php';

abstract class AdapterBase extends CObject implements AdapterInterface, Iterator
{
    /**
     * Database resource.
     *
     * @var resource
     **/
    public $db;
    
    /**
     * Database result resource.
     *
     * @var resource
     **/
    public $result;
    
    /**
     * SQL query string.
     *
     * @var string
     **/
    public $query = '';
    
    /**
     * Result row offset. Must be between zero and the total number
     * of rows minus one.
     *
     * @var integer
     **/
    public $offset = 0;
    
    /**
     * Pass an associative array of database settings to connect
     * to database on construction of class.
     *
     * @return void
     **/
    public function  __construct($db_properties = null)
    {
    	$this->offset = 0;
    	
        // if properties passed connect to database
        if (is_array($db_properties)) $this->connect($db_properties);
    }
    
    /**
     * Stop the application and display/handle error.
     *
     * @return void
     **/
    public function throw_error($msg = null)
    {
        if (!$msg) {
            $msg = 'An error occurred while interacting with a database ' .
                "using <strong>{$this->to_string()}</strong> adapter.";
        }
        CREO('application_error_code', 500);
        CREO('application_error', $msg);
    }
    
    /**
     * Iterator methods.
     */
    
    /**
     * Set the result object pointer to its first element.
     *
     * @return void
     **/
    public function rewind()
    {
        $this->offset = 0;
    }
    
    /**
     * Returns an associative array of the current row.
     * 
     * @return array
     * @see function get_row
     **/
    public function current()
    {
        return $this->get_row();
    }
    
    /**
     * Returns the index element of the current result object pointer.
     *
     * @return integer
     **/
    public function key()
    {
        return $this->offset;
    }
    
    /**
     * Advance the result object pointer and return an associative
     * array of the current row.
     * 
     * @return array
     * @see function current
     **/
    public function next()
    {
        ++$this->offset;
        return $this->current();
    }
    
    /**
     * Rewind the result object pointer by one and return an associative
     * array of the current row.
     *
     * @return array
     * @see function current
     **/
    public function prev()
    {
        --$this->offset;
        return $this->current();
    }
    
    /**
     * Resets DB properties and frees result resources.
     *
     * @return void
     **/
    public function reset()
    {
        // reset properties
        $this->query = '';
        $this->rewind();
        
        // release result resource
        if (!empty($this->db) && !empty($this->result) &&
            is_resource($this->db) && is_resource($this->result)) {
            $this->free_result();
        }
    }
    
    /**
     * Transaction methods. Override if necessary to support adapter syntax.
     */
    
    /**
     * START transaction.
     *
     * @return void
     **/
    public function start_tran()
    {
        $this->execute('START TRANSACTION;');
    }
    
    /**
     * ROLLBACK transaction.
     *
     * @return void
     **/
    public function rollback()
    {
        $this->execute('ROLLBACK;');
    }

    /**
     * COMMIT transaction.
     *
     * @return void
     **/
    public function commit()
    {
        $this->execute('COMMIT;');
    }
} // END abstract class AdapterBase extends CObject implements AdapterInterface, Iterator