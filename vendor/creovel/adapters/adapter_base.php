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

abstract class AdapterBase extends Object
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
} // END abstract class AdapterBase extends Object