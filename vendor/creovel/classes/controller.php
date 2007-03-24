<?php
/*
	Class: controller
	
	The *controller* class processes and responds to events, typically user actions, and may invoke changes on the model or view.
	
	See Also:
	
		For a better explanation go to <http://en.wikipedia.org/wiki/Model-view-controller>.
*/

class controller
{

	// Section: Public
	
	/*
		Property: render
		
		*String/Bool* name of view to display. Can be set to false to not show a view.
	*/
	
	public $render;
	
	/*
		Property: layout
		
		*String/Bool* name of layout to display. Can be set to false to not show a layout.
	*/
	
	public $layout;
	
	/*
		Property: params
		
		*Array* of all $_GET, $_POST, and $_REQUEST data.
	*/
	
	public $params;
	
	/*
		Function: _set_events
		
		Set the controller's events and layout.
		
		Parameters:
		
			events - Array of url parameters.
	*/

	public function _set_events($events)
	{
		$this->_controller = $events['controller'];
		$this->_action = $events['action'];
		if (!$this->render) $this->render = $events['action'];
		if (!$this->layout) $this->layout = ( $events['layout'] ? $events['layout'] : 'default' );
		if ( count($events['nested_controllers']) ) {
			$this->_nested_controller_path = str_replace($this->_controller, '', implode(DS, $events['nested_controllers']));
		}
	}

	/*
		Function: _set _params
		
		Set the controller's params
		
		Parameters:
		
			params - Array of url parameters.
	*/

	public function _set_params($params)
	{
		$this->params = $params;
	}

	/*
		Function: _execute_action
		
		Executes the controller's action.
	*/

	public function _execute_action()
	{
		// initialize callback
		$this->initialize();
		try {
			
			if ( method_exists($this, $this->_action) ) {
			
				// call before filter
				$this->before_filter();
			
				// controller execute action
				$action = $this->_action;
				$this->$action();
				
				// call before filter
				$this->after_filter();
				
			} else {
				throw new Exception("Call to undefined action '{$method}' not found in <strong>".get_class($this)."</strong>.");
			}
			
		} catch ( Exception $e ) {
		
			$this->_throw_exception($e);
		
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
	
	public function _output($return_as_str)
	{
		// set options for view
		$options['controller'] = $this->_controller;
		$options['action'] = $this->_action;
		$options['layout'] = $this->layout;
		$options['render'] = $this->render;
		$options['text'] = $this->render_text;
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
		if ( $options['partial'] ) {
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
		$view_path = $this->_get_view_path($view, $controller);
		
		switch ( true ) {
		
			// if view equaqls false render nothing
			case ( $view === false ):
				return;
			break;

			// if layout get page content with layout
			case ( $layout ):
				if ( $return_as_str ) {
					return view::_get_view($view_path, $this->_get_layout_path($layout), $options);
				} else {
					return view::_show_view($view_path, $this->_get_layout_path($layout), $options);
				}
			break;
			
			// if same layout include files and set variables
			case ( file_exists($view_path) ):
				// create a variable foreach other option, using its key as the variable name
				if ( count($options) ) foreach ( $options as $key => $values ) $$key = $values;
				
				if ( $return_as_str ) {
					$options['layout'] = false;
					return view::_get_view($view_path, $this->_get_layout_path($layout), $options);
				} else {
					// include partial
					include $view_path;
					return;
				}
			break;
			
			default:
				$_ENV['error']->add("Unable to render '".( $view{0} == '_' ? 'partial' : 'view' )."'. File not found <strong>{$view_path}</strong>.");
			break;
			
		}
	
	}
	
	/*
		Function: render_to_str
		
		Renders view with options to a string.
		
		Paramaters:
		
			options - Array of options.
	
	*/

	public function render_to_str($options)
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
	*/

	public function build_partial($partial, $locals = null, $controller = null)
	{
		if ( is_array($partial) ) {
			$options = $partial;
		} else {
			$options['render'] = $partial;
		}
		if ( $locals ) $options['locals'] = $locals;
		if ( $controller ) $options['controller'] = $controller;
		$this->render($options);
	}
	
	/*
		Function: render_partial
		
		Alias to build_partial and adds an underscore to the view name to signify partials.
		
		Paramaters:
		
			options - Action to render or an array of render $options.
			locals - *Optional* array of variables to pass to the view.
			controller - *Optional* controller name. Use if vew is not in the current controller.
	
	*/

	public function render_partial($partial, $locals = null, $controller = null)
	{
		if ( is_array($partial) ) {
			$options = $partial;
		} else {
			$options['partial'] = $partial;
		}
		if ( $locals ) $options['locals'] = $locals;
		if ( $controller ) $options['controller'] = $controller;
		$this->render($options);
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

	public function build_controller($controller, $action = '', $id = '', $extras = array(), $to_str = false)
	{
		$events = creovel::get_events(null, url_for($controller, $action, $id));
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

	public function build_controller_to_str($controller, $action = '', $id = '', $extras = array())
	{
		return $this->build_controller($controller, $action, $id, $extras, true);
	}

	/*
		Function: run
		
		Excute and render a certain action.
		
		Parameters:
		
			action - *String* Action to run.
	*/

	public function run($action)
	{
		$this->render = $action;
		$this->$action();
	}
	
	/*
		Function: no_view
		
		Don't render layout or view. Usefull for ajax calls.
	*/

	public function no_view()
	{
		$this->layout = false;
		$this->render = false;
	}
	
	/*
		Function: is_posted
		
		Check if current page has posted values.
		
		Returns:
		
			Bool.
	*/

	public function is_posted()
	{
		return ( $_SERVER['REQUEST_METHOD'] == 'POST' );
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
			
			throw new Exception("Call to undefined action '{$method}' not found in <strong>".get_class($this)."</strong>.");
		
		} catch ( Exception $e ) {
		
			$this->_throw_exception($e);
		
		}
	}

	// Section: Protected
	
	/*
		Property: _controller
		
		Name of controller to use.
	*/
	
	protected $_controller;
	
	/*
		Property: _action
		
		Name of action/method to use.
	*/
	
	protected $_action;
	
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
	
	private function _get_view_path($view = null, $controller = null)
	{
		// nested controllers check [NH] might need to find a better way to do this
		if ( $this->_nested_controller_path ) {
			$view_path =  VIEWS_PATH.$this->_nested_controller_path.( $controller ? $controller : $this->_controller ).DS.$view.'.php';
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
	
	private function _get_layout_path($layout = null)
	{
		return VIEWS_PATH.'layouts'.DS.( $layout ? $layout : $this->layout ).'.php';
	}
	
	/*
		Function: _throw_exception
		
		Handle error depending on $_ENV[mode]
		
		Parameters:
		
			$e - Object of error exception.
	*/
	
	private function _throw_exception($e)
	{
		if ( $_ENV['mode'] != 'development' ) {
			$this->_custom_error($e);
		} else {
			// add to errors
			$_ENV['error']->add($e->getMessage(), $e);
		}
	}
	
	/*
		Function: _custom_error
		
		Show custom error page.
		
		Parameters:
		
			$e - Exception object.
	*/
	
	private function _custom_error($e = null)
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
	
	public function before_filter() {}
	
	/*
		Function: after_filter
		
		Called right after the action is executed.
	*/
	
	public function after_filter() {}
	
}
?>
