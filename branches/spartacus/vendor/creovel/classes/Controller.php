<?php
/**
 * The Controller class processes and responds to events, typically user
 * actions, and may invoke changes on the model or view.
 *
 * @package Creovel
 * @subpackage Creovel.Classes
 * @copyright  2008 Creovel, creovel.org
 * @license    http://creovel.googlecode.com/svn/trunk/License   MIT License
 * @version    $Id:$
 * @since      Class available since Release 0.1.0
 **/
class Controller
{
	/**
	 * Name of controller to use.
	 *
	 * @var string
	 **/
	public $_controller;
	
	/**
	 * Name of action/method to execute.
	 *
	 * @var string
	 **/
	public $_action;
	
	/**
	 * String/Boolean name of view to display. Can be set to false to
	 * not show a view.
	 *
	 * @var string/false
	 **/
	public $render;
	
	/**
	 * String/Boolean name of view to display. Can be set to false to
	 * not show a layout.
	 *
	 * @var string/false
	 **/
	public $layout;
	
	/**
	 * Array of all $_GET, $_POST, and $_REQUEST data.
	 *
	 * @var array
	 **/
	public $params;
	
	/**
	 * Set the controller's events properties (controller, action,
	 * render layout).
	 *
	 * @param array $events Array of framework events.
	 * @return void
	 **/
	public function __setEvents($events)
	{
		$this->_controller = $events['controller'];
		$this->_action = $events['action'];
		if (!$this->render) {
			$this->render = $events['action'];
		}
		if (!$this->layout) {
			$this->layout = (isset($events['layout']) ? $events['layout'] : CREO('default_layout'));
		}
		if (isset($events['nested_controller_path'])) $this->_nested_controller_path = $events['nested_controller_path'];
	}
	
	/**
	 * Set the controller's params.
	 *
	 * @param array $params Array of url parameters.
	 * @return void
	 **/
	public function __setParams($params)
	{
		$this->params = $params;
	}
	
	/**
	 * Executes the controller's action.
	 *
	 * @return void
	 **/
	public function __executeAction()
	{
		// initialize callback
		$this->initialize();
		
		// initialize scope fix
		$this->__intializeParentControllers();
		
		try {
			
			if (method_exists($this, $this->_action)) {
				
				// call before filter
				$this->beforeFilter();
				
				// controller execute action
				$action = $this->_action;
				$this->$action();
				
				// call before filter
				$this->afterFilter();
				
			} else {
				throw new Exception("404: Call to undefined action <em>{$this->_action}</em> not found in <strong>".get_class($this)."</strong>.");
			}
			
		} catch (Exception $e) {
			$this->__exception($e);
		}
	}
	
	/*
		Function: _output
		
		Output contents to user. Might not need this function anymore since render() does all the
		Leaving in for now [Nes].
		
		Parameters:	
		
			return_as_str - Return as string.
		
		Returns:
		
			Mixed.
	*/
	
	public function __output($return_as_str)
	{
		// set options for view
		$options['controller'] = $this->_controller;
		$options['action'] = $this->_action;
		$options['layout'] = $this->layout;
		$options['render'] = $this->render;
		if (isset($this->render_text)) $options['text'] = $this->render_text;
		$options['to_str'] = $return_as_str;
		return $this->render($options);
	}
	
	/*
		Function: render
		
		Render view from options array.
		
		Parameters:	
		
			options - Array of options.
		
		Returns:
		
			Output to screen or a string.
	*/

	public function render($options)
	{
		// check options
		if ( !is_array($options) ) return false;
		
		// set and unset reserved $options
		if (isset($options['partial'])) {
			$view = '_'.$options['partial'];
			unset($options['partial']);
		}
		
		if ( isset($options['action']) ) {
			$view = $options['action'];
			unset($options['action']);
		}
		
		if ( isset($options['render']) ) {
			$view = $options['render'];
			unset($options['render']);
		}
		
		if ( $options['controller'] ) {
			$controller = $options['controller'];
			unset($options['controller']);
		}
		
		if ( isset($options['layout']) ) {
			$layout = $options['layout'];
			unset($options['layout']);
		}
		
		if ( $options['to_str'] ) {
			$return_as_str = true;
			unset($options['to_str']);
		}

		// set view path
		$view_path = $this->__viewPath($view, $controller);
		
		switch ( true ) {
		
			// if view equaqls false render nothing
			case ( $view === false ):
				return;
			break;

			// if layout get page content with layout
			case ( $layout ):
				if (isset($return_as_str)) {
					return View::create($view_path, $this->__layoutPath($layout), $options);
				} else {
					return View::show($view_path, $this->__layoutPath($layout), $options);
				}
			break;
			
			// if same layout include files and set variables
			case ( file_exists($view_path) ):
				// create a variable foreach other option, using its key as the variable name
				if ( count($options) ) foreach ( $options as $key => $values ) $$key = $values;
				
				if ( $return_as_str ) {
					$options['layout'] = false;
					return View::create($view_path, $this->__layoutPath($layout), $options);
				} else {
					// include partial
					include $view_path;
					return;
				}
			break;
			
			default:
				if (!$options['no_error']) {
					$_ENV['error']->add("404: Unable to render <em>".( $view{0} == '_' ? 'partial' : 'view' )."</em> not found in <strong>{$view_path}</strong>.");
				}
			break;
			
		}
	
	}
	
	/*
		Function: render_to_str
		
		Renders view with options to a string.
		
		Paramaters:
		
			options - Array of options.
	
	*/

	public function renderToStr($options)
	{
		$options['to_str'] = true;
		return $this->render($options);
	}

	/*
		Function: build_partial
		
		Include a view into the current page.
		
		Parameters:
		
			options - Action to render or an array of render $options.
			locals - *Optional* array of variables to pass to the view.
			controller - *Optional* controller name. Use if vew is not in the current controller.
			no_error - *Optional* no application error if partial not found.
	*/
	
	public function buildPartial($partial, $locals = null, $controller = null, $no_error = false)
	{
		if ( is_array($partial) ) {
			$options = $partial;
		} else {
			$options['render'] = $partial;
		}
		if ( $locals ) $options['locals'] = $locals;
		if ( $controller ) $options['controller'] = $controller;
		if ( $no_error ) $options['no_error'] = $no_error;
		$this->render($options);
	}
	
	/*
		Function: render_partial
		
		Alias to build_partial and adds an underscore to the view name to signify partials.
		
		Paramaters:
		
			options - Action to render or an array of render $options.
			locals - *Optional* array of variables to pass to the view.
			controller - *Optional* controller name. Use if vew is not in the current controller.
			no_error - *Optional* no application error if partial not found.
	*/
	
	public function renderPartial($partial, $locals = null, $controller = null, $no_error = false)
	{
		if ( is_array($partial) ) {
			$options = $partial;
		} else {
			$options['partial'] = $partial;
		}
		$this->buildPartial($options, $locals, $controller, $no_error);
	}
	
	/*
		Function: render_partial_to_str
		
		Alias to build_partial and adds an underscore to the view name to signify partials.
		
		Paramaters:
		
			options - Action to render or an array of render $options.
			locals - *Optional* array of variables to pass to the view.
			controller - *Optional* controller name. Use if vew is not in the current controller.
		
		Returns:
			string
	
	*/

	public function renderPartialToStr($partial, $locals = null, $controller = null)
	{
		if ( is_array($partial) ) {
			$options = $partial;
		} else {
			$options['partial'] = $partial;
		}
		if ( $locals ) $options['locals'] = $locals;
		if ( $controller ) $options['controller'] = $controller;
		$options['to_str'] = true;
		return $this->render($options);
	}
	
	/*
		Function: build_controller
		
		Allows the ability build a controller within a controller.
		
		
		Parameters:
		
			controller - Controller to build.
			action - Action to view.
			id - *Optional*
			extras - *Optional*
			to_str - *Optional* bool to return controller as a string.
		
		Returns:
		
			Controller object.
	*/

	public function buildController($controller, $action = '', $id = '', $extras = array(), $to_str = false)
	{
		$route_name = 'build_controller_'.uniqid();
		$route_path = "{$controller}/{$action}";
		mapper::connect( $route_path, array( 'name' => $route_name, 'controller' => $controller, 'action' => $action ));
		$events = creovel::get_events(null, url_for($controller, $action, $id), $route_name);
		
		$params = array();
		if ( $id ) $params['id'] = $id;
		return creovel::run($events, array_merge($params, $extras), $to_str);
	}
	
	/*
		Function: build_controller_to_str
		
		Alias to build_controller return controller as a string.

		Parameters:	
		
			controller - Controller to build.
			action - Action to view.
			id - *Optional*
			extras - *Optional*
			to_str - *Optional* Return controller as a string.
		
		Returns:
		
			Controller view as a string.
	*/

	public function buildControllerToStr($controller, $action = '', $id = '', $extras = array())
	{
		return $this->build($controller, $action, $id, $extras, true);
	}

	/*
		Function: run
		
		Excute and render a certain action.
		
		Parameters:
		
			action - *String* Action to run.
			kill_after - *Boolean* script stops after action is executed.
	*/

	public function run($action, $kill_after = false)
	{
		$this->render = $action;
		if ($kill_after) {
			$this->_action = $action;
		} else {
			$this->$action();
		}
	}
	
	/**
	 * Don't render layout or view. Useful for AJAX calls.
	 *
	 * @return void
	 **/
	public function noView()
	{
		$this->layout = false;
		$this->render = false;
	}
	
	/**
	 * Check if current page has posted values.
	 *
	 * @return boolean
	 **/
	public function isPosted()
	{
		return $_SERVER['REQUEST_METHOD'] == 'POST';
	}
	
	/*
		Function: application_error
		
		Stop the application at the controller level during an error.
	*/

	public function application_error($msg = null)
	{
		try {
			
			$msg = $msg ? $msg : "An error occurred while executing the action <em>".$this->_action."</em> in the <strong>".get_class($this)."</strong>.";
			throw new Exception($msg);
		
		} catch ( Exception $e ) {
		
			$this->__exception($e);
		
		}
	}
	
	/*
		Function: __call
		
		Magic function call being used to catch controller errors.
		
		Parameters:
		
			method - Name of method.
			arguments - Arguments passed.
	*/

	public function __call($method, $arguments)
	{
		try {
			
			throw new Exception("404: Call to undefined action <em>{$method}</em> not found in <strong>".get_class($this)."</strong>.");
		
		} catch ( Exception $e ) {
		
			$this->__exception($e);
		
		}
	}
	
	// Section: Private
	
	/*
		Function: _get_view_path
		
		Gets the path of the view file.
		
		
		Parameters:	
		
			view - String of the view name.
			controller - String of the controller name.
			
		Returns:
		
			String.
	*/
	
	private function __viewPath($view = null, $controller = null)
	{
		// nested controllers check [NH] might need to find a better way to do this
		if (isset($this->_nested_controller_path)) {
			$view_path =  VIEWS_PATH.$this->_nested_controller_path.DS.( $controller ? $controller : $this->_controller ).DS.$view.'.php';
			if ( file_exists($view_path) ) {
				return $view_path;
			} else {
				return VIEWS_PATH.( $controller ? $controller : $this->_controller ).DS.$view.'.php';
			}
		} else {
			return VIEWS_PATH.( $controller ? $controller : $this->_controller ).DS.$view.'.php';
		}
	}
	
	/*
		Function: _get_layout_path
		
		Gets the path of the layout file.
		
		Parameters:
		
			layout - String of the layout name.
			
		Returns:
		
			String.
	*/
	
	private function __layoutPath($layout = null)
	{
		return VIEWS_PATH.'layouts'.DS.( $layout ? $layout : $this->layout ).'.php';
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Nesbert Hidalgo
	 **/
	private function __intializeParentControllers()
	{
		$parent_controllers = get_ancestors(get_class($this));
		foreach (array_reverse($parent_controllers) as $controller) {
			$method = 'initialize' . $controller;
			if (method_exists($controller, $method)) {
				$this->$method();
			}
		}
	}
	
	/**
	 * Handle error depending on $GLOBALS['CREOVEL']['MODE'].
	 *
	 * @param object $e Object of error exception.
	 * @return void
	 **/
	private function __exception($e)
	{
		if (CREO('mode') != 'development') {
			$this->__customError($e);
		} else {
			// add to errors
			CREO('application_error', $e);
		}
	}
	
	/*
		Function: _custom_error
		
		Show custom error page.
		
		Parameters:
		
			$e - Exception object.
	*/
	
	private function __customError($e = null)
	{
		error::email_errors($e);
		$this->params['error'] = $e;
		creovel::run($_ENV['routing']->error_events(), $this->params);
		die;
	}
	
	// Section: Callbacks
	
	/*
		Function: initialize
		
		Called when controller is intialized. *Note:* need to the scoping of this function child overrides all [Nes].
	*/
	
	public function initialize() {}
	
	/*
		Function: before_filter
		
		Called right before the action is executed.
	*/
	
	public function beforeFilter()
	{}
	
	/*
		Function: after_filter
		
		Called right after the action is executed.
	*/
	
	public function afterFilter()
	{}
	
	
} // END class Controller