<?php
/*
 * Errors class.
 *
 */
class error extends view
{

	private $type;

	public function __construct($type)
	{
		$this->type = $type;
	}
	
	public function add()
	{
		$args = func_get_args();
		
		switch ( $this->type ) {
		
			case 'application':
				$this->display_fatal_error($args[0], $args[1]);
			break;
		
			case 'model':
				$this->add_form_error($args);
			break;
		
		}
	
	}
	
	private function display_fatal_error($message, $exception = null)
	{
		$this->mode_check();
		
		$this->message = $message;
		$this->traces = $exception->getTrace();
		$this->_show_view(CREOVEL_PATH.'views/fatal_errors.php', CREOVEL_PATH.'views/layouts/creovel.php');
		die;
	}
	
	private function add_form_error($message, $error = null)
	{
		//$this->fields->$error['field'] = array();
		$this->fields->$error['field'] = (object) array('message' => $error['message'], 'value' => $error['value']);
	}
	
	private function mode_check()
	{
		if ( $_ENV['mode'] !== 'development' ) {
			die('redirect 500 page!');
		} else {
			return true;
		}
	}
	
}
?>