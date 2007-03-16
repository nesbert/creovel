<?php
/*

	Class: creovel
	
	The main class where the model, view and controller interact.

 */

class creovel
{
	const VERSION = '0.03';
	const RELEASE_DATE = 'Feb 27 2007 21:32:55';

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
		$route = $_ENV['routing']->which_route($_SERVER['REQUEST_URI']);

		// $GLOBALS['HTTP_RAW_POST_DATA'] used for observer ajax calls
		// Note: HTTP_RAW_POST_DATA must set to on in php.ini
		$route->params['raw_post'] = str_replace('&_=', '', $GLOBALS['HTTP_RAW_POST_DATA']);
		unset($params['_']);

		// set event and params
		$events = $events ? $events : creovel::get_events(); //$route->params;
		$params = $params ? $params : creovel::get_params(); //$route->params;

		$controller = str_replace('/', DIRECTORY_SEPARATOR, $events['controller']);
		self::_include_controller($controller);

		// create controller object and build the framework
		$controller = (preg_match('/\//', $controller) > 0) ? substr(strrchr($controller, DIRECTORY_SEPARATOR), 1) : $controller;
		$controller = str_replace(DS, '_', $controller).'_controller';
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
		
			Array.
			
	*/

	public function get_events($event_to_return = null, $uri = null)
	{
		$route = $_ENV['routing']->which_route((($uri) ? $uri : $_SERVER['REQUEST_URI']));
		return (($event_to_return) ? $route->params[$event_to_return] : $route->params );
	}
	
	/*
	
		Function get_params
		
		Returns the framework params.
		
		Parameters:	
		
			param_to_return - Name of param to return.
			
		Returns:
		
			Array.
	
	*/

	public function get_params($param_to_return = null, $uri = null)
	{
		$route = $_ENV['routing']->which_route((($uri) ? $uri : $_SERVER['REQUEST_URI']));
		return array_merge($_GET, $_POST, $route->params);
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
					require_once($controller_path);
				} else {
					$controller_path = str_replace($class . '.php', '', $controller_path);
					throw new Exception("Controller '{$class}' not found in <strong>".str_replace('_controller'.'.php', '', $controller_path)."</strong>");
				}
			
			} catch ( Exception $e ) {
			
				// add to errors
				$_ENV['error']->add($e->getMessage(), $e);
			
			}
			
			// include helper
			if ( file_exists($helper_path) ) require_once($helper_path);
			
			// append to path if a nested controller
			$path .= str_replace('application'.DS, '', $controller.DS);
		}
	
	}
	
}
?>
