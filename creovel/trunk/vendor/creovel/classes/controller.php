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
			
				// controller execute action
				$action = $this->_action;
				$this->$action();
				
			} else {
				throw new Exception("Action '{$this->_action}' not found in <strong>{$this->_controller}_controller</strong>");
			}
			
		} catch ( Exception $e ) {
		
			// add to errors
			$_ENV['error']->add($e->getMessage(), $e);
		
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
	 * Include a view into the current page
	 *
	 * @author John Faircloth
	 * @access public
	 * @param string $view
	 * @param string $controller optional controller name
	 */
	public function build_partial($view, $controller = null)
	{
		include ( VIEWS_PATH.( $controller ? $controller : $this->controller ).DS.$view.'.php' );
	}
	
	/**
	 * Alias to build_partial and adds an underscore to the view
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 * @param string $view
	 * @param string $controller optional controller name
	 */
	public function render_partial($view, $controller = null)
	{
		$this->build_partial('_'.$view, $locals, $controller);
	}
	
}
?>