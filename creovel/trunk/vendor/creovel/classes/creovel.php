<?php
/*
 * Framework base class.
 *
 */
class creovel
{
	
	/**
	 * Run framework.
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 * @return object
	 */
	public function run($events = null, $params = null)
	{
		// set-up environment
		require_once( CONFIG_PATH . 'environments' . DS . ( $_ENV['mode'] = ( isset($_ENV['mode']) ? $_ENV['mode'] : 'development' ) ) . '.php' );
		
		// set event and params
		$events = $events ? $events : self::get_events();
		$params = $params ? $params : self::get_params();
		
		// set controller & action
		$events['controller'] = $events['controller'] ? $events['controller'] : $_ENV['routes']['default']['controller'];
		$events['action'] = $events['action'] ? $events['action'] : $_ENV['routes']['default']['action'];
		
		#print_obj($events);
		#print_obj($params);
		
		// include controllers and helpers		
		self::include_controllers($events['nested_controller_path'] . $events['controller']);		
		
		// create controller object and build the framework
		$controller = $events['controller'] . '_controller';
		$controller = new $controller();
		
		// set controller properties
		$controller->set_events($events);
		$controller->set_params($params);
		
		// execute action
		$controller->execute_action();
	
		// build page
		$controller->display_view();
		
		//print_obj($controller);
		return $controller;
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
		$args = explode('/', substr($uri[0], 1));
		
		// set events for framework with array indexes
		if ( count($args) > 2 ) {
			$events['controller'] =  $args[count($args) - 3];
			$events['action'] = $args[count($args) - 2];
			$events['id'] = $args[count($args) - 1];
		} else {
			$events['controller'] =  $args[count($args) - 2];
			$events['action'] = $args[count($args) - 1];
			$events['id'] = $args[count($args)];
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
	private function include_controllers($controller_path)
	{
		// include application controller
		$controllers = array_merge(array('application'), explode('/', $controller_path));
		
		$path = '';
		
		foreach ( $controllers as $controller ) {
		
			$class = $controller . '_controller';
			$controller_path = CONTROLLERS_PATH . $path . $class . '.php';
			$helper_path = HELPERS_PATH . $path . $controller . '_helper.php';
			
			#echo $controller_path. '<br />';
			
			try {
				
				if ( file_exists($controller_path) ) {				
					require_once($controller_path);
				} else {
					throw new Exception("Controller '{$class}' not found in <strong>{$controller_path}</strong>");
				}
				
			} catch ( Exception $e ) {
			
				// add to errors
				$error	= new error();
				$error->add('fatal', $e->getMessage(), $e);
			
			}
			
			// include helper
			if ( file_exists($helper_path) ) require_once($helper_path);

			$path .= str_replace('application/', '', $controller . DS);
			#echo $path.'<br /><br />';
		}
	
	}
	
}
?>