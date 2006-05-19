<?php
/*
 * Error class.
 *
 */
class error extends view
{

	/**
	 * Error type
	 *
	 * @author Nesbert Hidalgo
	 * @access private
	 * @var string
	 */
	private $_type;

	public function __construct($type)
	{
		$this->_type = $type;
	}
	
	public function add()
	{
		$args = func_get_args();
		
		$this->has_errors = true;
		
		switch ( $this->_type ) {
		
			case 'application':
				$this->application_error($args[0], $args[1]);
			break;
		
			case 'model':
				$this->add_form_error($args[0], $args[1]);
			break;
		
		}
	
	}
	
	private function application_error($message, $exception = null)
	{
		$this->mode_check();
		@ob_end_clean(); // clean diaplay buffer
		$this->message = $message;
		if ( is_object($exception) ) $this->traces = $exception->getTrace();
		if ( isset($_GET['view_source']) ) {
			$this->_show_view(CREOVEL_PATH.'views'.DS.'view_source.php', CREOVEL_PATH.'views'.DS.'layouts'.DS.'creovel.php');
		} else {
			$this->_show_view(CREOVEL_PATH.'views'.DS.'application_error.php', CREOVEL_PATH.'views'.DS.'layouts'.DS.'creovel.php');
		}
		die;
	}
	
	private function mode_check()
	{
		if ( $_ENV['mode'] !== 'development' ) {
			die('redirect 500 page!');
		} else {
			return true;
		}
	}
	
	private function add_form_error($field, $message)
	{
		$this->$field = $message;
	}	
	
}
?>