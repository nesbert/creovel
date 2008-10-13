<?php
/**
 * The main class where the model, view and controller interact.
 *
 * @package Creovel
 * @subpackage Creovel.Classes
 * @copyright  2008 Creovel, creovel.org
 * @license    http://creovel.googlecode.com/svn/trunk/License   MIT License
 * @version    $Id:$
 * @since      Class available since Release 0.1.0
 */
class Dispatcher
{
	/**
	 * Runs the framework.
	 *
	 * @return void
	 * @throws Exception [description]
	 **/
	public static function run($events = null, $params = null, $return_as_str = false)
	{
		// set event and params
		$events = $events ? $events : self::events();
		$params = $params ? $params : self::params();
		
		// include controller
		self::includeController((isset($events['nested_controller_path']) && $events['nested_controller_path'] ? $events['nested_controller_path'] . DS : '' ) . $events['controller']);
		
		// create controller object and build the framework
		$controller = humanize($events['controller']) . 'Controller';
		$controller = new $controller();
		
		// set controller properties
		$controller->__setEvents($events);
		$controller->__setParams($params);
		
		// execute action
		$controller->__executeAction();
		
		// output to user
		return $controller->__output($return_as_str);
	}
	
	/**
	 * Return the an associative array of events or a particular event value.
	 *
	 * @param string $event_to_return Name of event to return.
	 * @param string $uri
	 * @return mixed 
	 **/
	public function events($event_to_return = null, $uri = null)
	{
		$events = Routing::which($uri);
		return $event_to_return ? $events[$event_to_return] : $events;
	}
	
	/**
	 * Return the an associative array of params or a particular param value.
	 *
	 * @param string $param_to_return Name of param to return.
	 * @param string $uri
	 * @return mixed 
	 **/
	public function params($param_to_return = null, $uri = null)
	{
		$params = array_merge($_GET, $_POST, Routing::which($uri, true));
		return $param_to_return ? $params[$param_to_return] : $params;
	}
	
	/**
	 * Includes the required files for a controller and the controller helpers.
	 *
	 * @param string $controller_path Server path of controller to include.
	 * @return void 
	 **/
	public function includeController($controller_path)
	{
		try {
			// include application controller
			$controllers = array_merge(array('application'), explode(DS, $controller_path));
			
			$path = '';
			
			foreach ($controllers as $controller) {
				$class = $controller . '_controller';
				$controller_path = CONTROLLERS_PATH . $path . $class . '.php';
				$helper_path = HELPERS_PATH . $path . $controller . '_helper.php';
				
				if (file_exists($controller_path)) {
					require_once $controller_path;
				} else {
					$controller_path = str_replace($class . '.php', '', $controller_path);
					throw new Exception(str_replace(' ', '', humanize($class)) . " not found in <strong>" . str_replace('_controller' . '.php', '', $controller_path) . "</strong>");
				}
				
				// include helper
				if (file_exists($helper_path)) require_once $helper_path;
				
				// append to path if a nested controller
				$path .= str_replace('application' . DS, '', $controller . DS);
			}
		} catch (Exception $e) {
			CREO('error_code', 404);
			CREO('application_error', $e);
		}
	}
} // END class Dispatcher