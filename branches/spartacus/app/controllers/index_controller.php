<?php
/**
 * IndexController class.
 *
 * @package Application
 * @subpackage Application.Controllers
 * @author Nesbert Hidalgo
 **/
class IndexController extends ApplicationController
{
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function initializeIndexController()
	{
		//echo '<h5>IndexController::initializeIndexController</h5>';
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function index()
	{
		print_obj($this);
		print_obj(CREO());
	}
} // END class IndexController