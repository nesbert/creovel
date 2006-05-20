<?php
/*
 * Error class.
 *
 */
class error
{

	/**
	 * Error type
	 *
	 * @author Nesbert Hidalgo
	 * @access private
	 * @var string
	 */
	private $_type;

	/**
	 * Set error type in construct
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $type applicaiton, model
	 */
	public function __construct($type)
	{
		$this->_type = $type;
	}
	
	/**
	 * Add errors to object
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $args[0] message if application error and field name if model error
	 * @param object|string $args[1] exception oject if application error and message if model error
	 */
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
	
	/**
	 * Display application errors to user
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $message required
	 * @param object $exception optional
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
			view::_show_view(CREOVEL_PATH.'views'.DS.'view_source.php', CREOVEL_PATH.'views'.DS.'layouts'.DS.'creovel.php');
		} else {
			view::_show_view(CREOVEL_PATH.'views'.DS.'application_error.php', CREOVEL_PATH.'views'.DS.'layouts'.DS.'creovel.php');
		}
		
		die;
	}
	
	/**
	 * Handle how to display errors to the user. If in dvelopment mode
	 * show debugging information else redirect to error page
	 *
	 * @author Nesbert Hidalgo
	 * @access private
	 * @return bool
	 */
	private function handle_error()
	{
		if ( $_ENV['mode'] !== 'development' ) {
			die('redirect 500 page!');
		} else {
			return true;
		}
	}
	
	/**
	 * Create a property for each error
	 *
	 * @author Nesbert Hidalgo
	 * @access private
	 * @param string $field required
	 * @param object $message required
	 */
	private function add_form_error($field, $message)
	{
		$this->$field = $message;
	}	
	
}
?>