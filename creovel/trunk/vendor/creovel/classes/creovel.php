<?php
/*
 * Framework base class.
 *
 * @todo test nested controllers
 */
class creovel
{
	const VERSION = '0.01';
	const RELEASE_DATE = 'Nov 24 2005 16:55:55';
	
	/**
	 * Run framework.
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 * @param array $events optional assoc. array of CONTORLLER, ACTION & ID
	 * @param array $events optional assoc. array of params
	 * @param bool $return_as_str optional returns controller as string
	 * @return object
	 */
	public function run($events = null, $params = null, $return_as_str = false)
	{
		// set error object
		$_ENV['error'] = new error('application');
		
		// set-up environment
		require_once( CONFIG_PATH . 'environments' . DS . ( $_ENV['mode'] = ( isset($_ENV['mode']) ? $_ENV['mode'] : 'development' ) ) . '.php' );
		
		// set event and params
		$events = $events ? $events : self::get_events();
		$params = $params ? $params : self::get_params();
		
		// set controller & action
		$events['controller'] = $events['controller'] ? $events['controller'] : $_ENV['routes']['default']['controller'];
		$events['action'] = $events['action'] ? $events['action'] : $_ENV['routes']['default']['action'];
		
		// include controllers and helpers		
		self::include_controller($events['nested_controller_path'] . $events['controller']);
		
		// create controller object and build the framework
		$controller = $events['controller'] . '_controller';
		$controller = new $controller();
		
		// set controller properties
		$controller->_set_events($events);
		$controller->_set_params($params);
		
		// execute action
		$controller->_execute_action();
		
		if ( $return_as_str ) {
			return $controller->_get_view();
		} else {
			return $controller->_show_view();
			return $controller;
		}
	}
	
	/**
	 * Returns the framework events (CONTORLLER, ACTION & ID).
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $event_to_return optional name of event to return
	 * @return array
	 */
	public function get_events($event_to_return = null)
	{
		// read URI which was given in order to access this page
		$uri = explode('?', $_SERVER['REQUEST_URI']);
		
		// get event args from uri
		$args = explode(DS, substr($uri[0], 1));
		
		// set events for framework with array indexes
		if ( count($args) > 2 ) {
			$events['controller'] =  $args[0];
			$events['action'] = $args[1];
			$events['id'] = $args[2];
		} else {
			$events['controller'] =  $args[0];
			$events['action'] = $args[1];
		}
		
		// check for nested controller
		if ( count($args) > 3 ) {
			$events['nested_controller_path'] = implode(DS, array_slice($args, 0, count($args) - 3) ) . DS;
		}
		
		return ( $event_to_return ? $events[$event_to_return] : $events );		
	}
	
	/**
	 * Returns the framework params.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $param_to_return optional name of param to return
	 * @return array
	 */
	public function get_params($param_to_return = null)
	{
		// get id from events	
		$id = self::get_events('id');
		
		// intialize params	
		$params = $id ? array('id'=>$id) : array();
			
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
	
	/**
	 * Includes the required files for a controller and the controller helpers.
	 *
	 * @author Nesbert Hidalgo
	 * @access private
	 * @param string $controller_path required
	 */
	private function include_controller($controller_path)
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