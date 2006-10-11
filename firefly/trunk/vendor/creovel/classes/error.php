<?php
/**
 * Copyright (c) 2005-2006, creovel.org
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated 
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software,
 * and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions
 * of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED
 * TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * Licensed under The MIT License. Redistributions of files must retain the above copyright notice.
 */

/*
 * Error class.
 *
 * @copyright	Copyright (c) 2005-2006, creovel.org
 * @package		creovel
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class error
{

	/**
	 * Error type
	 *
	 * @author Nesbert Hidalgo
	 * @access private
	 * @var string
	 */
	private $type;

	/**
	 * Error count
	 *
	 * @author Nesbert Hidalgo
	 * @access private
	 * @var string
	 */
	private $error_count = 0;

	/**
	 * Set error type in construct
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $type applicaiton, model
	 */
	public function __construct($type)
	{
		$this->type = $type;
	}
	
	/**
	 * Add errors to object
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $args[0] message if application error and field name if model error
	 * @param object|string $args[1] exception oject if application error and message if model error
	 */
	public function add()
	{
		$this->error_count++;
		$args = func_get_args();
		
		switch ( $this->type ) {
		
			case 'application':
				$this->application_error($args[0], $args[1]);
			break;
		
			default:
				$this->model_error($args[0], $args[1]);
			break;
		
		}
	
	}
	
	/**
	 * Returns bool value for errors
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @return bool
	 */
	public function has_errors()
	{
		return $this->error_count ? true : false;
	}
	
	/**
	 * Returns $error_count
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @return int
	 */
	public function count()
	{
		return $this->error_count;
	}
	
	/**
	 * Display application errors to user
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $message required
	 * @param object $exception optional
	 */
	private function application_error($message, $exception = null)
	{
		// check whether or not to show debugging errors
		$this->handle_error();
		
		// clean output buffer for application errors
		@ob_end_clean();
		
		$this->message = $message;
		
		if ( is_object($exception) ) $this->traces = $exception->getTrace();
		
		if ( isset($_GET['view_source']) ) {
			view::_show_view(CREOVEL_PATH.'views'.DS.'view_source.php', CREOVEL_PATH.'views'.DS.'layouts'.DS.'creovel.php');
		} else {
			view::_show_view(CREOVEL_PATH.'views'.DS.'application_error.php', CREOVEL_PATH.'views'.DS.'layouts'.DS.'creovel.php');
		}
		
		die;
	}
	
	/**
	 * Handle how to display errors to the user. If in dvelopment mode
	 * show debugging information else redirect to error page
	 *
	 * @author Nesbert Hidalgo
	 * @access private
	 * @return bool
	 */
	private function handle_error()
	{
		if ( $_ENV['mode'] != 'development' ) {
			switch ( true )
			{
				// if routes error set show this error page else show creovel error
				case ( is_array($_ENV['routes']['error']) ):
					view::_show_view(VIEWS_PATH.( $_ENV['routes']['error']['controller'] ? $_ENV['routes']['error']['controller'] : ( $_ENV['routes']['default']['controller'] ? $_ENV['routes']['default']['controller'] : 'index' ) ).DS.( $_ENV['routes']['error']['action'] ? $_ENV['routes']['error']['action'] : 'error' ).'.php', VIEWS_PATH.'layouts'.DS.( $_ENV['routes']['error']['layout'] ? $_ENV['routes']['error']['layout'] : ( $_ENV['routes']['default']['layout'] ? $_ENV['routes']['default']['layout'] : 'default' ) ).'.php');
					die;
				break;
				
				default:
					return true;
				break;
			}
		} else {
			return true;
		}
	}
	
	/**
	 * Create a property for each error
	 *
	 * @author Nesbert Hidalgo
	 * @access private
	 * @param string $field required
	 * @param object $message required
	 */
	private function model_error($field, $message)
	{
		$this->$field = $message;
		// add to globals
		$_ENV['model_error'][$field] = $message;
	}	
	
}
?>