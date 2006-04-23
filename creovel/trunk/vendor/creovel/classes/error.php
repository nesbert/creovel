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
				$this->application_error($args[0], $args[1]);
			break;
		
			case 'model':
				$this->add_form_error($args);
			break;
		
		}
	
	}
	
	private function application_error($message, $exception = null)
	{
		$this->mode_check();		
		$this->message = $message;
		if ( is_object($exception) ) $this->traces = $exception->getTrace();
		if ( isset($_GET['view_source']) ) {
			$this->_show_view(CREOVEL_PATH.'views/view_source.php', CREOVEL_PATH.'views/layouts/creovel.php');
		} else {
			$this->_show_view(CREOVEL_PATH.'views/application_error.php', CREOVEL_PATH.'views/layouts/creovel.php');
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
	
	private function add_form_error($message, $error = null)
	{
		//$this->fields->$error['field'] = array();
		//$this->fields->$error['field'] = (object) array('message' => $error['message'], 'value' => $error['value']);
	}	
	
}
?>