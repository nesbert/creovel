<?php
/*
 * Errors class.
 *
 */
class error extends view
{

	public function add($type, $message, $exception = null)
	{
	
		switch ( $type ) {
		
			case 'fatal':
				$this->display_fatal_error($message, $exception);
			break;
		
			case 'form':
				$this->add_form_error($message, $exception);
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