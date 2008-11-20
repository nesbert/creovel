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
abstract class ActionController
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
	public function __set_events($events)
	{
		$events['action'] = underscore($events['action']);
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
	public function __set_params($params)
	{
		$this->params = $params;
	}
	
	/**
	 * Execute controller's action and calls back.
	 *
	 * @return void
	 **/
	public function __execute_action()
	{
		try {
			// initialize callback
			$this->initialize();
			
			// initialize scope fix
			$this->__intialize_parent_controllers();
			
			if (method_exists($this, $this->_action)) {
				// call before filter
				$this->before_filter();
				
				// controller execute action
				$this->{$this->_action}();
				
				// call before filter
				$this->after_filter();
			} else {
				throw new Exception('Call to undefined method ' .
					"<em>{$this->_action}</em> not found in " .
					'<strong>' . get_class($this) . '</strong>.');
			}
		} catch (Exception $e) {
			CREO('error_code', 404);
			CREO('application_error', $e);
		}
	}
	
	/**
	 * Output contents to user.
	 *
	 * @param boolean $return_as_str
	 * @return string
	 **/
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
	
	/**
	 * Render view from options array.
	 *
	 * @param array Options array.
	 * @return string Output to screen or a string.
	 **/
	public function render($options)
	{
		try {
			// check options
			if ( !is_array($options) ) return false;
			
			// set and unset reserved $options
			if (@$options['partial']) {
				$view = '_'.$options['partial'];
				unset($options['partial']);
			}
			
			if (@$options['action']) {
				$view = $options['action'];
				unset($options['action']);
			}
			
			if (@$options['render']) {
				$view = $options['render'];
				unset($options['render']);
			}
			
			if (@$options['controller']) {
				$controller = $options['controller'];
				unset($options['controller']);
			}
			
			if (@$options['layout']) {
				$layout = $options['layout'];
			} else {
				$layout = false;
			}
			unset($options['layout']);
			
			if (@$options['to_str']) {
				$return_as_str = true;
			} else {
				$return_as_str = false;
			}
			unset($options['to_str']);
			
			// set view path
			$view_path = @$this->__view_path($view, $controller);
			
			switch (true) {
				
				// if view equaqls false render nothing
				case ($view === false):
					return;
					break;
				
				// if layout get page content with layout
				case ($layout):
					if ($return_as_str) {
						return ActionView::to_str($view_path, $this->__layout_path($layout), $options);
					} else {
						return ActionView::show($view_path, $this->__layout_path($layout), $options);
					}
					break;
				
				// if same layout include files and set variables
				case (file_exists($view_path)):
					// create a variable foreach other option, using its key as the variable name
					if (count($options)) foreach ( $options as $key => $values ) $$key = $values;
					
					if ($return_as_str) {
						$options['layout'] = false;
						return ActionView::to_str($view_path, $this->__layout_path($layout), $options);
					} else {
						// include partial
						include $view_path;
						return;
					}
					break;
				
				default:
					if (@!$options['no_error']) {
						throw new Exception("Unable to render <em>" .
						($view{0} == '_' ? 'partial' : 'view') .
						"</em> not found in <strong>{$view_path}</strong>.");
					}
					break;
			}
		} catch (Exception $e) {
			CREO('error_code', 404);
			CREO('application_error', $e);
		}
	}
	
	/**
	 * Renders view with options to a string.
	 *
	 * @param array Options array.
	 * @return string Output to screen or a string.
	 **/
	public function render_to_str($options)
	{
		$options['to_str'] = true;
		return $this->render($options);
	}
	
	/**
	 * Include a view into the current page.
	 *
	 * @param string $view View to render or an array of render $options.
	 * @param array $locals *Optional* array of variables to pass to the view.
	 * @param string $controller *Optional* controller name. Use if view is not in the current controller.
	 * @param boolean $no_error *Optional* no application error if partial not found.
	 * @return string Output to screen or a string.
	 **/
	public function build_partial($view, $locals = null, $controller = null, $no_error = false)
	{
		if ( is_array($view) ) {
			$options = $view;
		} else {
			$options['render'] = $view;
		}
		if ($locals) $options['locals'] = $locals;
		if ($controller) $options['controller'] = $controller;
		if ($no_error) $options['no_error'] = $no_error;
		$this->render($options);
	}
	
	/**
	 * Alias to build_partial and adds an underscore to the view name to
	 * signify partials.
	 *
	 * @param string $partial View to render or an array of render $options.
	 * @param array $locals *Optional* array of variables to pass to the view.
	 * @param string $controller *Optional* controller name. Use if view is not in the current controller.
	 * @param boolean $no_error *Optional* no application error if partial not found.
	 * @return string Output to screen or a string.
	 **/
	public function render_partial($partial, $locals = null, $controller = null, $no_error = false)
	{
		if (is_array($partial)) {
			$options = $partial;
		} else {
			$options['partial'] = $partial;
		}
		$this->build_partial($options, $locals, $controller, $no_error);
	}
	
	/**
	 * Alias to build_partial and adds an underscore to the view name to
	 * signify partials.
	 *
	 * @param string $partial View to render or an array of render $options.
	 * @param array $locals *Optional* array of variables to pass to the view.
	 * @param string $controller *Optional* controller name. Use if view is not in the current controller.
	 * @return string Output string.
	 **/
	public function render_partial_to_str($partial, $locals = null, $controller = null)
	{
		if ( is_array($partial) ) {
			$options = $partial;
		} else {
			$options['partial'] = $partial;
		}
		if ($locals) $options['locals'] = $locals;
		if ($controller) $options['controller'] = $controller;
		$options['to_str'] = true;
		return $this->render($options);
	}
	
	/**
	 * Allows the ability build a controller within a controller.
	 *
	 * @param string $controller Controller name to build.
	 * @param string $action Action to execute and view.
	 * @param mixed $id
	 * @param array $extras
	 * @param boolean $to_str - Boolean to return controller as a string.
	 * @return string Print output or return string.
	 **/
	public function build_controller($controller, $action = '', $id = '', $extras = array(), $to_str = false)
	{
		$route_name = 'build_controller_' . uniqid();
		$route_path = "/{$controller}/{$action}";
		Routing::map($route_name, $route_path, array('controller' => $controller, 'action' => $action));
		$events = Dispatcher::events(null, url_for($controller, $action, $id), $route_name);
		$params = array();
		if ($id) $params['id'] = $id;
		return Dispatcher::run($events, array_merge($params, $extras), $to_str);
	}
	
	/**
	 * Alias to build_controller return controller as a string.
	 *
	 * @param string $controller Controller name to build.
	 * @param string $action Action to execute and view.
	 * @param mixed $id
	 * @param array $extras
	 * @return string Output string.
	 **/
	public function build_controller_to_str($controller, $action = '', $id = '', $extras = array())
	{
		return $this->build_controller($controller, $action, $id, $extras, true);
	}
	
	/**
	 * Execute and render a certain action.
	 *
	 * @param string $action Action to run/view.
	 * @param boolean $kill_after Script stops after action is executed.
	 * @return void
	 **/
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
	public function no_view()
	{
		$this->layout = false;
		$this->render = false;
	}
	
	/**
	 * Check if current page has posted values.
	 *
	 * @return boolean
	 **/
	public function is_posted()
	{
		return $_SERVER['REQUEST_METHOD'] == 'POST';
	}
	
	/**
	 * Stop the application at the controller level during an error.
	 *
	 * @return void
	 **/
	public function throw_error($msg = null)
	{
		if (!$msg) {
			$msg = 'An error occurred while executing the action ' .
			"<em>{$this->_action}</em> in the <strong> " . get_class($this) .
			'</strong>.';
		}
		CREO('error_code', 404);
		CREO('application_error', $msg);
	}
	
	/**
	 * Magic function call being used to catch controller errors.
	 *
	 * @param string $method Name of method being called.
	 * @param array $arguments Arguments passed to the method being called.
	 * @return void
	 * @throws Exception Application error.
	 **/
	public function __call($method, $arguments)
	{
		try {
			throw new Exception("Call to undefined method <em>{$method}</em>" .
				' not found in <strong>' . get_class($this) . '</strong>.');
		} catch ( Exception $e ) {
			CREO('error_code', 404);
			CREO('application_error', $e);
		}
	}
	
	/**
	 * Gets the path of the view file.
	 *
	 * @param string $view String of the view name.
	 * @param string $controller String of the controller name.
	 * @return string Server path of view.
	 **/
	private function __view_path($view = null, $controller = null)
	{
		// nested controllers check [NH] might need to find a better way to do this
		if (isset($this->_nested_controller_path)) {
			return VIEWS_PATH . $this->_nested_controller_path .
				DS . ($controller ? $controller : $this->_controller) .
				DS . $view . '.php';
		} else {
			return VIEWS_PATH . ($controller ? $controller : $this->_controller) .
			DS . $view . '.php';
		}
	}
	
	/**
	 * Gets the path of the layout file.
	 *
	 * @param string $layout String of the layout name.
	 * @return string Server path of layout.
	 **/
	private function __layout_path($layout = null)
	{
		return VIEWS_PATH . 'layouts' . DS . ($layout ? $layout : $this->layout) . '.php';
	}
	
	/**
	 * Waterfall initialize function routine.
	 *
	 * @return void
	 **/
	private function __intialize_parent_controllers()
	{
		$parent_controllers = get_ancestors(get_class($this));
		foreach (array_reverse($parent_controllers) as $controller) {
			$method = 'initialize_' . Inflector::underscore($controller);
			if (method_exists($controller, $method)) {
				$this->$method();
			}
		}
	}
	
	/**
	 * First thing called during action execution.
	 *
	 * @return void
	 **/
	public function initialize()
	{}
	
	/**
	 * Called right before the action is executed.
	 *
	 * @return void
	 **/
	public function before_filter()
	{}
	
	/**
	 * Called right after the action is executed.
	 *
	 * @return void
	 **/
	public function after_filter()
	{}
} // END abstract class Controller