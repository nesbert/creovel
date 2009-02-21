<?php
/*

	Class: creovel
	
	The main class where the model, view and controller interact.

*/

class creovel
{
	const VERSION = '0.2.0';
	const RELEASE_DATE = 'Feb 02 2007 02:00:00';
	
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
		$events = $events ? $events : self::get_events();
		$params = $params ? $params : self::get_params();
		
		// include controllers and helpers
		if ( is_array($events['nested_controllers']) ) {
			$path = '';
			foreach ( $events['nested_controllers'] as $nested_controller ) {
				self::_include_controller($path.$nested_controller);
				$path .= $nested_controller.DS;
			}
		} else {
			self::_include_controller($events['controller']);
		}
		
		// create controller object and build the framework
		$controller = $events['controller'] . '_controller';
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
		// read URI which was given in order to access this page, remove any trailing forward slashes
		$uri = $uri ? $uri : $_SERVER['REQUEST_URI'];
		$uri = explode('?', ( $uri{strlen($uri) - 1} == '/' ? substr($uri, 0, strlen($uri) - 1) : $uri ));

		if (isset($_ENV['routes'][$uri[0]])) header('Location: '.$_ENV['routes'][$uri[0]]);

		// get event args from uri
		$args = explode('/', substr($uri[0], 1));
		
		// check/set nested controllers
		$path = '';
		if ( count($args) ) foreach ( $args as $arg ) {
			if ( file_exists(CONTROLLERS_PATH.$path.$arg.'_controller.php') ) {
				$events['nested_controllers'][] = $arg;
			}
			$path .= $arg.DS;
		}
		
		// set events for framework with array indexes
		switch ( true ) {
		
			case ( count($events['nested_controllers']) > 1 ):
				$events['controller'] =  $events['nested_controllers'][ count($events['nested_controllers']) - 1 ];
				foreach ( $args as $key => $arg ) {
					if ( $arg == $events['controller'] ) {
						$events['action'] = $args[ $key + 1 ];
						$id = $args[ $key + 2 ];
						break;
					}
				}
			break;
			
			case ( count($args) > 2 ):
				$events['controller'] =  $args[ count($args) - 3 ];
				$events['action'] = $args[ count($args) - 2 ];
				$id = $args[ count($args) - 1 ];
			break;
			
			default:
				$events['controller'] =  $args[0];
				$events['action'] = $args[1];
			break;
			
		}
		
		// return id only
		if ( $event_to_return == 'id' ) {
			return $id;
		}
		
		// set controller & action
		$events['controller'] = $events['controller'] ? $events['controller'] : $_ENV['routes']['default']['controller'];
		$events['action'] = $events['action'] ? $events['action'] : $_ENV['routes']['default']['action'];

		return ( $event_to_return ? $events[$event_to_return] : $events );
	}
	
	/*
	
		Function get_params
		
		Returns the framework params.
		
		Parameters:	
		
			param_to_return - Name of param to return.
			
		Returns:
		
			Array.
	
	*/

	public function get_params($param_to_return = null)
	{
		// intialize params	
		$params = ( $id = self::get_events('id') ) ? array('id'=>$id) : array();
			
		$requests = array($_GET, $_POST);
		
		// add each request add keys & values to $params
		foreach ( $requests as $request ) {
			
			if ( count($request) === 0 ) continue;
			foreach ( $request as $field => $value ) $params[$field] = $value;
		
		}
		
		// $GLOBALS['HTTP_RAW_POST_DATA'] used for observer ajax calls
		// Note: HTTP_RAW_POST_DATA must set to on in php.ini
		if ( $GLOBALS['HTTP_RAW_POST_DATA'] ) {
			$params['raw_post'] = str_replace('&_=', '', $GLOBALS['HTTP_RAW_POST_DATA']);
		}
		
		// unset blank vaiable set by $GLOBALS['HTTP_RAW_POST_DATA']
		unset($params['_']);
		
		return ( $param_to_return ? $params[$param_to_return] : $params );
	
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