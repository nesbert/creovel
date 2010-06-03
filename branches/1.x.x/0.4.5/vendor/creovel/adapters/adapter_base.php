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

abstract class AdapterBase extends Object implements AdapterInterface, Iterator
{
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
        CREO('error_code', 500);
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
        return (int) $this->offset;
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
        $this->offset++;
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
        $this->offset--;
        return $this->current();
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
} // END abstract class AdapterBase extends Object implements AdapterInterface, Iterator