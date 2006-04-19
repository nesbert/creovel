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
		
		}
	
	}
	
	private function display_fatal_error($message, $exception = null)
	{
		$this->mode_check();
		
		$this->message = $message;
		$this->exception = $exception;
		$this->_set_view_content_path(CREOVEL_PATH.'views/fatal_errors.php');
		$this->_set_view_layout_path(CREOVEL_PATH.'views/layouts/creovel.php');
		$this->_show_view();
		die;
	}
	
	private function mode_check()
	{
		if ( $_ENV['mode'] !== 'development' ) {
			die('redirect 404 page!');
		} else {
			return true;
		}
	}
	
}
?>