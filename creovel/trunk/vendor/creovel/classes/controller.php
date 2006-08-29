<?php
/**
* Class to handle all intreaction between models, views, and controllers
* 
* @todo filiters
*/
class controller
{

	/**
	 * Controller name
	 *
	 * @author Nesbert Hidalgo
	 * @access protected
	 * @var string
	 */
	protected $_controller;
	
	/**
	 * Action name
	 *
	 * @author Nesbert Hidalgo
	 * @access protected
	 * @var string
	 */
	protected $_action;
	
	/**
	 * Page to be outputted
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @var string
	 */
	public $render;
	
	/**
	 * Layout name
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @var string
	 */	
	public $layout;
	
	/**
	 * Array of all server-side requests
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @var array
	 */	
	public $params;
	
	/**
	 * Set the controller's events and layout
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 */
	public function _set_events($events)
	{
		$this->_controller = $events['controller'];
		$this->_action = $events['action'];
		if (!$this->render) $this->render = $events['action'];
		if (!$this->layout) $this->layout = $_ENV['routes']['default']['layout'];
		$this->_nested_controller_path = $events['nested_controller_path'];
	}

	/**
	 * Set the controller's params
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 */
	public function _set_params($params)
	{
		$this->params = $params;
	}

	/**
	 * Executes the controller's action.
	 *
	 * @author John Faircloth, Nesbert Hidalgo
	 * @access public 
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
	
	/**
	 * Output contents to user
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param bool $return_as_str
	 * @return mixed
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
	
	/**
	 * Get the path of the view file
	 *
	 * @author Nesbert Hidalgo
	 * @access private
	 * @param string $view
	 * @param string $controller
	 * @return string
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
	
	/**
	 * Get the path of the layout file
	 *
	 * @author Nesbert Hidalgo
	 * @access private
	 * @param string $layout
	 * @return string
	 */
	private function _get_layout_path($layout = null)
	{
		return VIEWS_PATH.'layouts'.DS.( $layout ? $layout : $this->layout ).'.php';
	}

	/**
	 * Render views with options
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param array $options
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
				// include partial
				include ( $view_path );				
				return;
			break;
			
			default:
				//print_obj($options, 1);
				$_ENV['error']->add("Unable to render 'view'. File not found <strong>{$view_path}</strong>.");
			break;
			
		}
	
	}
	
	/**
	 * Render views with options to a string
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param array $options
	 */
	public function render_to_str($options)
	{
		$options['to_str'] = true;
		return $this->render($options);
	}

	/**
	 * Include a view into the current page
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param mixed $options action to render or an array of render $options
	 * @param array $locals optional
	 * @param string $controller optional controller name
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
	
	/**
	 * Alias to build_partial and adds an underscore to the view
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 * @param mixed $options partial to render or an array of render $options
	 * @param array $locals optional
	 * @param string $controller optional controller name
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
	
	/**
	 * Allows the ability build a controller within a controller
	 *
	 * @author John Faircloth, Nesber Hidalgo
	 * @access public
	 * @param string $controller
	 * @param string $action optional
	 * @param string $id optional
	 * @param string $extras optional
	 * @param bool $to_str optional return controller as string
	 * @return object controller object
	 */
	public function build_controller($controller, $action = '', $id = '', $extras = array(), $to_str = false)
	{
		$events = array('controller'=>$controller, 'action'=>$action);
		$params = array();		
		if ( $id ) $params['id'] = $id;
		return creovel::run($events, array_merge($params, $extras), $to_str);
	}
	
	/**
	 * Alias to build_controller return controller as a string
	 *
	 * @author Nesber Hidalgo
	 * @access public
	 * @param string $controller
	 * @param string $action optional
	 * @param string $id optional
	 * @param string $extras optional
	 * @return string controller object
	 */
	public function build_controller_to_str($controller, $action = '', $id = '', $extras = array())
	{
		return $this->build_controller($controller, $action, $id, $extras, true);
	}
	
	/**
	 * Magic functions
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
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
	
	/**
	 * Callback functions
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 */
	public function initialize() {}
	public function before_filter() {}
	public function after_filter() {}
	
	/**
	 * Excute and render pasted action
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 * @param string $action
	 */
	public function run($action)
	{
		$this->$action();
		$this->render = $action;
	}
	
}
?>