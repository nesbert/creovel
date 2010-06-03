<?php
/**
 * Errors controller class.
 *
 * @package     Application
 * @subpackage  Controllers
 **/
class ErrorsController extends ApplicationController
{
    /**
     * Initialize controller.
     *
     * @return void
     **/
    public function initialize_errors_controller()
    {}
    
    /**
     * 500 error page.
     *
     * @return void
     **/
    public function general()
    {}
    
    /**
     * 404 error page.
     *
     * @return void
     **/
    public function not_found()
    {}
    
    /**
     * 401 error page.
     *
     * @return void
     **/
    public function unauthorized()
    {}
} // END class ErrorsController extends ApplicationController