<?php
/**
 * Modules base class.
 *
 * @package     Creovel
 * @subpackage  Modules
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.0
 * @author      Nesbert Hidalgo
 **/
abstract class ModuleBase
{
    /**
     * Stop the application and display/handle error.
     *
     * @return void
     **/
    public function throw_error($msg = null)
    {
        if (!$msg) {
            $msg = 'An error occurred while processing the ' .
                "<strong>{$this->to_string()}</strong> module.";
        }
        CREO('application_error_code', 500);
        CREO('application_error', $msg);
    }
} // END abstract class ModuleBase