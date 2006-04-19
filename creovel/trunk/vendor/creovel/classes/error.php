<?php
/*
 * Errors class.
 *
 */
class error extends view
{

	private $errors;
	
	
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
		$this->message = $message;
		$this->exception = $exception;
		$this->set_content_path(CREOVEL_PATH.'views/fatal_errors.php');
		$this->set_template_path(CREOVEL_PATH.'views/layouts/creovel.php');
		$this->create_view();
		$this->show_view();
		die;
	}

}
?>