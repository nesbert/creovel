<?

/*

Class: controller
	The main class.

*/

class controller
{

	public $render;
	public $layout;
	public $params;

	protected $_controller;
	protected $_action;

	// Section: Public	
	
	/*
	
	Function: _set_events
		Set the controller's events and layout

	Parameters:
		events - array of url parameters	
 
	*/

	public function _set_events($events)
	{
		$this->_controller = $events['controller'];
		$this->_action = $events['action'];
		if (!$this->render) $this->render = $events['action'];
		if (!$this->layout) $this->layout = $_ENV['routes']['default']['layout'];
		if ( count($events['nested_controllers']) ) {
			$this->_nested_controller_path = str_replace($this->_controller, '', implode(DS, $events['nested_controllers']));
		}
	}

	/*
	
	Function: _set _params
		Set the controller's params

	Parameters:
		params - array of url parameters

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
				throw new Exception("Action '{$this->_action}' not found in <strong>{$this->_controller}_controller</strong>");
			}
			
		} catch ( Exception $e ) {
		
			// add to errors
			$_ENV['error']->add($e->getMessage(), $e);
		
		}		
	}
	
	/*
	
	Function: _output
		Output contents to user

	Parameters:	
		return_as_str - Return as string

	Returns:
		mixed

	*/

	public function _output($return_as_str)
	{
		// set options for view
		$options['controller'] = $this->_controller;
		$options['action'] = $this->_action;
		$options['layout'] = $this->layout;
		$options['render'] = $this->render;
		$options['text'] = $this->render_text;
		return $this->render($options);
	}
	
	/*
	
	Function: render
		Render views with options

	Parameters:	
		options - array of options

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
		Render views with options to a string

	Paramaters:
		options - array of options

	*/

	public function render_to_str($options)
	{
		$options['to_str'] = true;
		return $this->render($options);
	}

	/*
	
	Function: build_partial
		Include a view into the current page

	Parameters:	
		options - action to render or an array of render $options
		locals - optional
		controller - optional controller name

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
		Alias to build_partial and adds an underscore to the view

	Paramaters:
		options - partial to render or an array of render $options
		locals - optional
		controller - optional controller name

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
		Allows the ability build a controller within a controller

	Parameters:	
		controller - controller to build
		action - action to build
		id - optional
		extras - optional
		to_str optional return controller as string

	Returns:
		controller object

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
		Alias to build_controller return controller as a string

	Parameters:	
		controller - controller to build
		action - action to build
		id - optional
		extras - optional
	
	Returns:
		controller object

	*/

	public function build_controller_to_str($controller, $action = '', $id = '', $extras = array())
	{
		return $this->build_controller($controller, $action, $id, $extras, true);
	}
	
	/**

	Function: __call
		Magic functions

	Parameters:
		method - name of method
		arguments - arguments passed

	*/

	public function __call($method, $arguments)
	{
		try {
			
			throw new Exception("Call to undefined method '{$method}' not found in <strong>".get_class($this)."</strong>.");
		
		} catch ( Exception $e ) {
		
			// add to errors
			$_ENV['error']->add($e->getMessage(), $e);
		
		}
	}

	/*

	Function: run	
		Excute and render pasted action

	Parameters:	
		action - action to run

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
		Is Posted

	Returns:
		bool

	*/

	public function is_posted()
	{
		return ( $_SERVER['REQUEST_METHOD'] == 'POST' );
	}

	// Section: Private
	
	/*
	
	Function: _get_view_path
		Get the path of the view file

	Parameters:	
		view - view name
		controller - controller name

	Returns:
		string

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
	
	Function: _get_laytou_path
		Get the path of the layout file

	Parameters:	
		layout - layout name

	Returns:
		string

	*/

	private function _get_layout_path($layout = null)
	{
		return VIEWS_PATH.'layouts'.DS.( $layout ? $layout : $this->layout ).'.php';
	}


	/*

	Section: Callbacks	
		
		* initialize
		* before_filter
		* after_filter

	*/

	public function initialize() {}
	public function before_filter() {}
	public function after_filter() {}
}

?>
