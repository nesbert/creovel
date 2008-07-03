<?php

/*
	Class: creovel
	
	The main class where the model, view and controller interact.
*/

class creovel
{

	const VERSION = '0.04';
	const RELEASE_DATE = 'Jul 02 2008 19:01:55';
	
	// Section: Public
	
	/*
		Function: run
		
		Runs the framework.
		
		Parameters:
		
			events - Assoc. array of controller, action & id.
			params - Assoc. array of params.
			return_as_str - *Optional* Returns controller as string.
		
		Returns:
		
			<controller> object or string.
	*/
	
	public function run($events = null, $params = null, $return_as_str = false)
	{
		// set event and params
		$events = $events ? $events : creovel::get_events();
		$params = $params ? $params : creovel::get_params();
		
		// include controller
		self::_include_controller( ( isset($events['nested_controller_path']) && $events['nested_controller_path'] ? $events['nested_controller_path'].DS : '' ) . $events['controller'] );
		
		// create controller object and build the framework
		$controller = $events['controller'].'_controller';
		$controller = new $controller();
		
		// set controller properties
		$controller->_set_events($events);
		$controller->_set_params($params);
		
		// execute action
		$controller->_execute_action();

		// output to user
		return $controller->_output($return_as_str);
	}
	
	/*
		Function: get_events
		
		Returns the framework events (controller, action & id).
		
		Parameters:
		
			event_to_return - Name of event to return.
			uri - *Optional* url routing path.
		
		Returns:
		
			Events array.
	*/
	
	public function get_events($event_to_return = null, $uri = null, $route_name = '')
	{
		$events = $_ENV['routing']->events($uri, $route_name);
		return $event_to_return ? $events[$event_to_return] : $events;
	}
	
	/*
		Function get_params
		
		Returns the framework params.
		
		Parameters:
		
			param_to_return - Name of param to return.
			
		Returns:
		
			Params array.
	*/
	
	public function get_params($param_to_return = null, $uri = null)
	{
		$params = $_ENV['routing']->params($uri);
		
		// clean controller (eg. admin/users)
		if ( isset($params['controller']) && ( count( $c = explode('/', $params['controller'] ) ) > 1 ) ) {
			$params['controller'] = $c[ count($c) - 1 ];
			array_pop($c);
			$params['nested_controller'] = implode('/', $c);
		}
		
		$params = array_merge($params, $_GET, $_POST);
		// $GLOBALS['HTTP_RAW_POST_DATA'] used for observer ajax calls
		// Note: HTTP_RAW_POST_DATA must set to on in php.ini
		if ( isset($GLOBALS['HTTP_RAW_POST_DATA']) ) {
			$params =  array_merge( $params, array( 'raw_post' => str_replace('&_=', '', $GLOBALS['HTTP_RAW_POST_DATA']) ) );
		}
		unset($params['_']);
		return $param_to_return ? $params[$param_to_return] : $params;
	}
	
	// Section: Private
	
	/*
		Function: _include_controller
		
		Includes the required files for a controller and the controller helpers.
		
		Parameters:	
		
			controller_path - Server path of controller to include.
	*/
	
	private function _include_controller($controller_path)
	{
		// include application controller
		$controllers = array_merge(array('application'), explode(DS, $controller_path));
		
		$path = '';
		
		foreach ( $controllers as $controller ) {
		
			$class = $controller . '_controller';
			$controller_path = CONTROLLERS_PATH . $path . $class . '.php';
			$helper_path = HELPERS_PATH . $path . $controller . '_helper.php';
			
			try {
				
				if ( $class == '_controller' ) {
					$_ENV['error']->add("Looking for an 'Unknown Controller' in <strong>".str_replace('_controller'.'.php', '', $controller_path)."</strong>");
				}
				
				if ( file_exists($controller_path) ) {
					require_once $controller_path;
				} else {
					$controller_path = str_replace($class . '.php', '', $controller_path);
					throw new Exception("404: Controller '{$class}' not found in <strong>".str_replace('_controller'.'.php', '', $controller_path)."</strong>");
				}
			
			} catch ( Exception $e ) {
			
				// add to errors
				$_ENV['error']->add($e->getMessage(), $e);
			
			}
			
			// include helper
			if ( file_exists($helper_path) ) require_once $helper_path;
			
			// append to path if a nested controller
			$path .= str_replace('application'.DS, '', $controller.DS);
		}
	
	}

}
?>