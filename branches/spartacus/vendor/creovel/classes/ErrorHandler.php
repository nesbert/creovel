<?php
/**
 * Creovel application error handler and debugger. Controls framework catchable
 * errors and allows for graceful recovery.
 *
 * @package Creovel
 * @subpackage Creovel.Classes
 **/
class ErrorHandler
{
	/**
	 * Add/process application error.
	 *
	 * @param string/object $error Error message or Exception object.
	 * @return void
	 **/
	public function add(&$error)
	{
		$this->__process($error);
	}
	
	/**
	 * Email application error or a customer error message to emails set
	 * in $GLOBALS['CREOVEL']['SERVER_ADMIN'].
	 *
	 * @param string $error_message Optional error message.
	 * @return false Returns false if email not set
	 **/
	public function email($error_message = null)
	{
		// get email set in ENV
		$emails = CREO('server_admin');
		
		if (!$emails || $emails == 'youremail@yourdomain.com') return false;
		
		$email = new Mailer;
		$email->recipients = $emails;
		$email->subject = 'Application Error: '.BASE_URL.$_SERVER['REQUEST_URI'];
		$email->text = $error_message ? error_message : View::toString(CREOVEL_PATH.'views'.DS.'debugger'.DS.'error_text.php');
		$email->send();
	}
	
	/**
	 * Process and/or display application errors to user.
	 *
	 * @param string/object $error Error message or Exception object.
	 * @return void
	 **/
	private function __process(&$error)
	{
		if (is_object($error)) {
			$this->exception = $error;
			$this->message = $this->exception->getMessage();
		} else {
			$this->exception = '';
			$this->message = $error;
		}
		
		// set header for error pages
		if (preg_match('/^404:/', $this->message)) {
			$this->message = trim(str_replace('404:', '', $this->message));
			header('Status: 404 Not Found', true, 404);
		} else {
			header('Status: 500 Internal Server Error', true, 500);
		}
		
		// grace fully handle errors in none devlopment mode
		if (CREO('mode') != 'development') {
			// email errors
			if (CREO('email_on_error')) $this->email(&$var);
			die('#155 error');
			Creovel::Run($_ENV['routing']->error_events(), array('error' => $this->error ));
			die;
		}
		
		// show debugger
		self::__debug();
	}
	
	/**
	 * Show Creovel debugger and application settings and files.
	 *
	 * @return void
	 **/
	private function __debug()
	{
		// clean output buffer for application errors
		@ob_end_clean();
		
		if (CREO('show_source')
			&& isset($_GET['view_source'])
			&& in_string(BASE_PATH, $_GET['view_source'])) {
			// show source view on application error
			View::show(CREOVEL_PATH.'views'.DS.'debugger'.DS.'view_source.php',
					CREOVEL_PATH.'views'.DS.'layouts'.DS.'debugger.php');
		} else {
			// else show source view on application error
			View::show(CREOVEL_PATH.'views'.DS.'debugger'.DS.'error.php',
					CREOVEL_PATH.'views'.DS.'layouts'.DS.'debugger.php');
		}
		die;
	}
} // END class CreovelError