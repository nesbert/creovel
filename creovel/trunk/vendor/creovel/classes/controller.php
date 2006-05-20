<?php
/**
* Class to handle all intreaction between models, views, and controllers
* 
* @todo filiters
*/
class controller extends view
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
		$this->layout = $_ENV['routes']['default']['layout'];		
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
	 * Out put contents to user
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param bool $return_as_str
	 * @return mixed
	 */
	public function _output($return_as_str)
	{
		if ( $return_as_str ) {
			return $this->_get_view();
		} else {
			$this->_show_view();
			return $this;
		}
	}
	
	/**
	 * Allows the ability build a controller with a controller
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
		
		if ( $options['partial'] ) {
			$view = '_'.$options['partial'];
			unset($options['partial']);
		}
		
		// set and unset reserved $options
		if ( $options['action'] ) {
			$view = $options['action'];
			unset($options['action']);
		}
		
		if ( $options['controller'] ) {
			$controller = $options['controller'];
			unset($options['controller']);
		} else {
			$controller = $this->_controller;
		}
		
		if ( $options['layout'] ) { 
			$layout = $options['layout'];
			unset($options['layout']);
		} else {
			$layout = $this->layout;
		}
		
		// create a variable foreach other option, using its key as the vairable name
		if ( count($options) ) foreach ( $options as $key => $values ) $$key = $values;
		
		switch ( true ) {
		
			case ( $layout ):
				$view_path = VIEWS_PATH.$controller.DS.$view.'.php';
				$layout_path = VIEWS_PATH.'layouts'.DS.$layout.'.php';
				die($layout);
			break;
		
			case ( include ( VIEWS_PATH.$controller.DS.$view.'.php' ) ):
				return;
			break;

			default:
				$_ENV['error']->add("Failed to render '{$view}'. Not found <strong>".VIEWS_PATH.( $controller ? $controller : $this->_controller ).DS."</strong>.");
			break;
			
		}
					
	}

	/**
	 * Include a view into the current page
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param mixed $view action to render or an array of render $options
	 * @param array $locals optional
	 * @param string $controller optional controller name
	 */
	public function build_partial($view, $locals = null, $controller = null)
	{
		$options = array('action' => $view, 'locals' => $locals, 'controller' => $controller);
		if ( is_array($view) ) $options = array_merge($options, $view);
		$this->render($options);
	}
	
	/**
	 * Alias to build_partial and adds an underscore to the view
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 * @param mixed $view partial to render or an array of render $options
	 * @param array $locals optional
	 * @param string $controller optional controller name
	 */
	public function render_partial($view, $locals = null, $controller = null)
	{
		$options = array('partial' => $view, 'locals' => $locals, 'controller' => $controller);
		if ( is_array($view) ) $options = array_merge($options, $view);
		$this->render($options);
	}

	public function __call($method, $arguments)
	{
		$_ENV['error']->add("Call to undefined method '{$method}' not found in <strong>".get_class($this)."</strong>.");
	}
	
	/**
	 * Callback functions
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 */
	public function before_filter() {}
	public function after_filter() {}
	
	
}
?>