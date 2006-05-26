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
	private $type;

	/**
	 * Error count
	 *
	 * @author Nesbert Hidalgo
	 * @access private
	 * @var string
	 */
	private $error_count = 0;

	/**
	 * Set error type in construct
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $type applicaiton, model
	 */
	public function __construct($type)
	{
		$this->type = $type;
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
	
	/**
	 * Returns bool value for errors
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @return bool
	 */
	public function has_errors()
	{
		return $this->error_count ? true : false;
	}
	
	/**
	 * Returns $error_count
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @return int
	 */
	public function count()
	{
		return $this->error_count;
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
	private function model_error($field, $message)
	{
		$this->$field = $message;
		$this->load_to_global_errors($field, $message);
	}
	
	/**
	 * Load model error to $GLOBALS['model_errors']
	 *
	 * @author Nesbert Hidalgo
	 * @access private
	 * @param string $field required
	 * @param object $message required
	 */
	private function load_to_global_errors($field, $message)
	{
		$GLOBALS['model_errors'][$field] = $message;
	}
	
}
?>