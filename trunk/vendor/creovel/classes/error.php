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
		
			type - Applicaiton or model error.
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
		$this->_error_count++;
		$args = func_get_args();
		
		switch ( $this->_type ) {
		
			case 'application':
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
		
		Bool.

	*/

	public function has_errors()
	{
		return $this->_error_count ? true : false;
	}
	
	/*

		Function: count
		
		Accessor for error count.

		Return:
		
		Integer of the total errors.
	
	*/

	public function count()
	{
		return $this->_error_count;
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
	
		Function: _handle_error
		
		Handle how to display errors to the user. If in dvelopment mode show debugging information else redirect to error page.
		
		Returns:
		
		Bool or shows error page.

	*/

	private function _handle_error()
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
		$_ENV['model_error'][$field] = $message;
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
		if ( is_object($exception) ) $this->traces = $exception->getTrace();

		if ($_ENV['mode'] != 'development' && isset($_ENV['email_errors'])) $this->email_errors(explode(',', $_ENV['email_errors']), $exception);

		// check whether or not to show debugging errors
		$this->_handle_error();
		
		// clean output buffer for application errors
		@ob_end_clean();
		
		$this->message = $message;
		
		if ( isset($_GET['view_source']) ) {
			if ( $_ENV['view_source'] && strstr($_GET['view_source'], BASE_PATH) ) {		
				view::_show_view(CREOVEL_PATH.'views'.DS.'view_source.php', CREOVEL_PATH.'views'.DS.'layouts'.DS.'creovel.php');
			} else {
				// reset debugger error and dont index page
				$this->message = '$_ENV[\'view_source\'] must be set in your config file. For more information visit <a href="http://www.creovel.org">http://www.creovel.org</a>.';
				$this->traces = array();
				view::_show_view(CREOVEL_PATH.'views'.DS.'application_error.php', CREOVEL_PATH.'views'.DS.'layouts'.DS.'creovel.php');
			}
		} else if ( $_ENV['command_line'] ) {
			view::_show_view(CREOVEL_PATH.'views'.DS.'command_line_error.php', CREOVEL_PATH.'views'.DS.'layouts'.DS.'command_line.php');
		} else {
			view::_show_view(CREOVEL_PATH.'views'.DS.'application_error.php', CREOVEL_PATH.'views'.DS.'layouts'.DS.'creovel.php');
		}
		
		die;
	}

	/*
	
	Function: email_errors
		Email application errors to the emails provided.

	Parameters:
		emails - array of email addresses
		exception - exception object
	
	*/

	private function email_errors($emails, $exception)
	{
		foreach ($emails as $email) mail($email, 'Application Error', $exception->getMessage());
	}
}

?>
