<?php
/*

	Class: error
	
	Error handler for Creovel.

*/

class error
{

	// Section: Public

	/*
	
		Function: __construct
		
		Set error type in construct.
		
		Parameters:
		
			type - Applicaiton (_application) or model error.
	*/

	public function __construct($type)
	{
		$this->_type = $type;
	}
	
	/*
	
		Function: add
		
		Add errors to object
		
		Parameters:
		
			args[0] - Message if application error and field name if model error.
			object|string $args[1] - Exception oject for application error and message if model error.
	
	*/

	public function add()
	{
		$args = func_get_args();
		
		// new error add to count
		if (!isset($_ENV['creovel']['form_errors'][$this->_type . '_' . $args[0]])) {
			$this->_error_count++;
		}
		
		switch ( $this->_type ) {
		
			case '_application':
				$this->_application_error($args[0], $args[1]);
			break;
		
			default:
				$this->_model_error($args[0], $args[1]);
			break;
		
		}
	}
	
	/*
	
		Function: has_errors
		
		Returns bool value for errors.
		
		Returns:
		
			Boolean

	*/

	public function has_errors()
	{
		return $this->_error_count ? true : false;
	}
	
	/*

		Function: count
		
		Accessor for error count.

		Returns:
		
			Integer of the total errors.
	
	*/

	public function count()
	{
		return $this->_error_count;
	}
	
	/*
		Function: email_errors
		
		Email application errors to the emails provided.
		
		Parameters:
		
			emails - Array of email addresses.
	*/

	public function email_errors($emails = null)
	{
		if (!$emails && in_string(',', $_ENV['email_errors'])) {
			$emails = explode(',', $_ENV['email_errors']);
		} else if (isset($_ENV['email_errors'])) {
			$emails = $_ENV['email_errors'];
		}
		if ( !$emails || $emails == 'youremail@yourdomain.com' ) return;
		
		$email = new mailer;
		$email->recipients = $emails;
		$email->subject = 'Application Error: '.BASE_URL.$_SERVER['REQUEST_URI'];
		$email->text = view::_get_view(CREOVEL_PATH.'views'.DS.'command_line_error.php', CREOVEL_PATH.'views'.DS.'layouts'.DS.'command_line.php');
		$email->send();
	}
	
	// Section: Private
	
	/*
		Property: _type
		Error type either application or model error.
	*/
	
	private $_type;
	
	/*
		Property: _error_count
		Number of error in this object.
	*/
	
	private $_error_count = 0;
	
	/*
		Function: _model_error
		
		Create a property for each error.
		
		Paramenters:
		
			field - Required string of field name
			message - Required string of error message for field.
	*/

	private function _model_error($field, $message)
	{
		$this->$field = $message;
		// add to globals
		add_form_error($this->_type . '_' . $field, $message);
	}
	
	/*
		Function:_application_error
		
		Display application errors to user.

		Parameters:
			
			message - Required error string.
			exception - Optional bool. If set to true sets traces for debugger.
	*/

	private function _application_error($message, $exception = null)
	{
		// set header for error pages
		if (preg_match('/^404:/',$message)) {
			header('Status: 404 Not Found', true, 404);
		} else {
			header('Status: 500 Internal Server Error', true, 500);
		}
		
		// set error object for views
		(object) $this->error;
		$this->error->message = $message;
		$this->error->exception = $exception;
		if ( is_object($exception) ) $this->error->traces = $exception->getTrace();
		
		//print_obj($this,1);
		
		// email errors
		if ( $_ENV['mode'] != 'development' && isset($_ENV['email_errors']) ) $this->email_errors();
		
		// custom error
		if ( $_ENV['mode'] != 'development' ) {
			creovel::run($_ENV['routing']->error_events(), array( 'error' => $this->error ));
			die;
		}
		
		// clean output buffer for application errors
		@ob_end_clean();
		
		// command line error
		if ( isset($_ENV['command_line']) ) {
			view::_show_view(CREOVEL_PATH.'views'.DS.'command_line_error.php', CREOVEL_PATH.'views'.DS.'layouts'.DS.'command_line.php');
			die;
		}
		// application error
		if ( isset($_GET['view_source']) ) {
			if ( $_ENV['view_source'] && in_string(BASE_PATH, $_GET['view_source']) ) {
				view::_show_view(CREOVEL_PATH.'views'.DS.'view_source.php', CREOVEL_PATH.'views'.DS.'layouts'.DS.'creovel.php');
			} else {
				// reset debugger error and dont index page
				$this->message = '$_ENV[\'view_source\'] must be set in your config file. For more information visit <a href="http://www.creovel.org">http://www.creovel.org</a>.';
				$this->traces = array();
				view::_show_view(CREOVEL_PATH.'views'.DS.'application_error.php', CREOVEL_PATH.'views'.DS.'layouts'.DS.'creovel.php');
			}
		} else {
			view::_show_view(CREOVEL_PATH.'views'.DS.'application_error.php', CREOVEL_PATH.'views'.DS.'layouts'.DS.'creovel.php');
		}
		die;
	}

}
?>