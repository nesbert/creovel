<?php
/*

Class: error
	Error handler for Creovel.

*/

class error
{

	private $type;
	private $error_count = 0;

	// Section: Public

	/*
	
	Function: __construct
		Set error type in construct.

	Parameters:
		type - applicaiton, model

	*/

	public function __construct($type)
	{
		$this->type = $type;
	}
	
	/*

	Function: add			
		Add errors to object

	Parameters:	
		args[0] - message if application error and field name if model error
		object|string $args[1] - exception oject if application error and message if model error

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
	
	/*

	Function:	
		Returns bool value for errors

	Returns:	
		bool

	*/

	public function has_errors()
	{
		return $this->error_count ? true : false;
	}
	
	/*

	Function:		
		Accessor for Error Count

	Return:	
		Int
	*/

	public function count()
	{
		return $this->error_count;
	}
	
	/*
	
	Function:	
		Display application errors to user

	Parameters:
		message - required
		exception - optional

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
			if ( $_ENV['view_source'] && strstr($_GET['view_source'], BASE_PATH) ) {		
				view::_show_view(CREOVEL_PATH.'views'.DS.'view_source.php', CREOVEL_PATH.'views'.DS.'layouts'.DS.'creovel.php');
			} else {
				die('Looking for something? <a href="creovel.org">creovel.org</a>');
			}
		} elseif (isset($_SERVER['argv'][0])) {
			view::_show_view(CREOVEL_PATH.'views'.DS.'command_line_error.php', CREOVEL_PATH.'views'.DS.'layouts'.DS.'command_line.php');
		} else {
			view::_show_view(CREOVEL_PATH.'views'.DS.'application_error.php', CREOVEL_PATH.'views'.DS.'layouts'.DS.'creovel.php');
		}
		
		die;
	}

	// Section: Private

	/*
			
	Function: handle_error
		Handle how to display errors to the user. If in dvelopment mode show debugging information else redirect to error page.

	Returns:
		bool

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
	
	/*

	Function: model_error	
		Create a property for each error

	Paramenters:	
		field - required
		message - required

	*/

	private function model_error($field, $message)
	{
		$this->$field = $message;
		// add to globals
		$_ENV['model_error'][$field] = $message;
	}	
	
}
?>
