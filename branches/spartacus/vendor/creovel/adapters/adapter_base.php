<?php
/**
 * Adapters base class.
 *
 * @package     Creovel
 * @subpackage  Creovel.Adapters
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.0
 * @author      Nesbert Hidalgo
 **/
abstract class AdapterBase
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
                "using <strong> " . get_class($this) . ' adapter.</strong> .';
        }
        CREO('application_error', $msg);
    }
} // END abstract class AdapterBase