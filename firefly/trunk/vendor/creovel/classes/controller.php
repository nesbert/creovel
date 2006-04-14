<?php
/**
 * Class to handle all intreaction between models, views, and controllers
 * 
 * @todo
 *  Todo's [NH] 10/22/2005
 *	- create data scrubber(clean, dirty)
 *	-
 */

class controller
{

	public $controller 	= 'index';
	public $action 		= 'index';
	public $render 		= 'index';
	public $render_text = false;
	public $layout 		= 'default';
	public $params;
	
	public function set_properties($params)
	{
		// set framework events
		$this->controller = ( $params['controller'] ? $params['controller'] : 'index' );
		$this->action = ( $params['action'] ? $params['action'] : 'index' );
		$this->render = ( $params['action'] ? $params['action'] : 'index' );
		
		// don't include contoller & action & set params
		unset($params['controller']);
		unset($params['action']);
		$this->params = $params;
	}

	public function build_page()
	{
	
		$html = '';
	
		// include view
		if ( $this->render !== false ) $view = $this->get_include_contents(VIEWS_PATH.$this->controller.DS.$this->render.'.php');
		
		// render text
		if ( $this->render_text !== false ) $view = $this->render_text . $view;
		
		// include template
		if ( $this->layout !== false ) {
			
			// get layout contents
			$html = $this->get_include_contents(VIEWS_PATH.'layouts'.DS.$this->layout.'.php');
			
			if ( $html ) {
				$html = str_replace('@@page_contents@@', $view, $html);
			} else {
				$html = $view;
			}
			
		} else {
		
			$html = $view;
			
		}
		
		// display page
		echo $html;
		
	}

	public function execute_action()
	{
		$action = $this->action;
		$this->$action();
	}

	public function build_partial($partial, $locals = array(), $controller = null)
	{
		include ( VIEWS_PATH.( $controller ? $controller : $this->controller ).DS.$partial.'.php' );
	}

	public function render_partial($partial, $locals = array(), $controller = null)
	{
		$this->build_partial('_'.$partial, $locals, $controller);
	}

	public function build_controller($controller, $action = '', $id = '', $extras = array())
	{

		$params = array(
			'controller' => $controller,
			'action' => $action,
			'id' => $id,
			);

		$params = array_merge($params, $extras);

		creovel($params);

	}

	public function redirect_to($controller = '', $action = '', $id = '', $extras = array())
	{
		header('location: ' . url_for($controller, $action, $id, $extras));
		die;
	}
	
	/*
	 * http://us3.php.net/manual/en/function.include.php
	 * Example 16-11. Using output buffering to include a PHP file into a string
	 */

	public function get_include_contents($filename)
	{
	   if ( is_file($filename) ) {
		   ob_start();
		   include $filename;
		   $contents = ob_get_contents();
		   ob_end_clean();
		   return $contents;
	   }
	   return false;
	}

	
}
?>