<?php
/**
 * Class to handle all intreaction between models, views, and controllers
 * 
 */
class controller extends view
{
	
	protected $controller;
	protected $action;
	
	public $layout;
	public $params;
	
	/**
	 * Set the controller's events and layout
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 */
	public function set_events($events)
	{
		$this->controller = $events['controller'];
		$this->action = $events['action'];
		$this->layout = $_ENV['routes']['default']['layout'];		
	}

	/**
	 * Set the controller's params
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 */
	public function set_params($params)
	{
		$this->params = $params;
	}

	/**
	 * Executes the controller's action.
	 *
	 * @author John Faircloth
	 * @access public 
	 */
	public function execute_action()
	{
		$action = $this->action;
		$this->$action();
	}
	
	/**
	 * Builds the html page and display it.
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 * @return string
	 */
	public function build_view()
	{
		// set view properties
		if ( isset($this->render_text) ) $this->set_content($this->render_text);
		if ( $this->render !== false ) $this->set_content_path(VIEWS_PATH.$this->controller.DS.( $this->render ? $this->render : $this->action ).'.php');
		if ( $this->layout !== false ) $this->set_template_path(VIEWS_PATH.'layouts'.DS.$this->layout.'.php');
		
		// create view
		$this->create_view();
		
		return $this->get_view();
	}
	
	/**
	 * Outputs the generated html to screen
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 */
	public function display_view()
	{
		echo $this->build_view();
	}
	

	public function build_controller($controller, $action = '', $id = '', $extras = array())
	{
		$events = array('controller'=>$controller, 'action'=>$action);
		
		if ( $id ) $params['id'] = $id;

		creovel::run($events, array_merge($params, $extras));
	}
	
	public function build_partial($view, $controller = null)
	{
		include ( VIEWS_PATH.( $controller ? $controller : $this->controller ).DS.$view.'.php' );
	}

	public function render_partial($view, $controller = null)
	{
		$this->build_partial('_'.$view, $locals, $controller);
	}
}
?>