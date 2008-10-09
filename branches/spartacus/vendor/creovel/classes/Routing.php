<?php
/**
 * Routing class to allow for custom pretty URLs.
 *
 * @package Creovel
 * @subpackage Creovel.Classes
 * @copyright  2008 Creovel, creovel.org
 * @license    http://creovel.googlecode.com/svn/trunk/License   MIT License
 * @version    $Id:$
 * @since      Class available since Release 0.4.0
 */
class Routing
{
	/**
	 * Uniform Resource Identifier string.
	 *
	 * @var string
	 **/
	public static $uri = '';
	
	/**
	 * Route object of matching URI and route.
	 *
	 * @var object
	 **/
	public static $current;
	
	/**
	 * Get URI query string.
	 *
	 * @param string $uri
	 * @return string
	 **/
	public function getQueryString($uri = null)
	{
		if (!$uri) $uri = $_SERVER['REQUEST_URI'];
		$uri = explode('?', $uri);
		return  $uri[0];
	}
	
	/**
	 * Append a route to the routes ($GLOBALS['CREOVEL']['ROUTES']) array.
	 *
	 * @param string $name
	 * @param object $route Route object
	 * @return void
	 **/
	public function add($name, RoutingRoute $route)
	{
		// default last in routes array
		if ($name == 'default') {
			$GLOBALS['CREOVEL']['ROUTES'] = array($name => $route);
		} else {
			$GLOBALS['CREOVEL']['ROUTES'] = array($name => $route) + $GLOBALS['CREOVEL']['ROUTES'];
		}
	}
	
	/**
	 * Get events from route.
	 *
	 * @param string $uri
	 * @param string $route_name Get events for specific route.
	 * @return array Events array.
	 **/
	public function events($uri, $route_name = '')
	{
		if (isset($GLOBALS['CREOVEL']['ROUTES'][$route_name]->events)) {
			return $GLOBALS['CREOVEL']['ROUTES'][$route_name]->events;
		}
		return self::which($uri);
	}
	
	/**
	 * Get custom params from route.
	 *
	 * @param string $uri
	 * @return array Params array.
	 **/
	public function params($uri)
	{
		return self::which($uri, true);
	}
	
	/**
	 * Get the events route depending on URI pattern or params.
	 *
	 * @param string $uri
	 * @param boolean $return_params Flag to return params
	 * @return array Events|Params array.
	 **/
	public function which($uri = null, $return_params = false)
	{
		// get query string
		$uri = self::getQueryString($uri);
		
		// return default route
		if ($uri == '/') {
			// set current
			self::$current = $GLOBALS['CREOVEL']['ROUTES']['default'];
			if ($return_params) {
				return self::defaultParams();
			} else {
				return self::defaultEvents();
			}
		}
		
		// set URI
		$uri = self::trimRegex($uri);
		
		foreach ($GLOBALS['CREOVEL']['ROUTES'] as $name => $route) {
			
			// skip default route
			if ($name == 'default') continue;
			if ($name == 'default_error') continue;
			
			// build pattern from parts
			$pattern = self::partsRegex($route->parts);
			
			// if match return events
			if (preg_match($pattern, '/'.$uri) ) {
				// set current
				self::$current = $route;
				if ($return_params) {
					return $route->params;
				} else {
					return $route->events;
				}
			}
		}
		
		// set current route
		self::$current = $GLOBALS['CREOVEL']['ROUTES']['default'];
		
		if ($return_params) {
			return self::defaultParams();
		} else {
			return self::defaultEvents();
		}
		
	}
	
	/**
	 * Build regex pattern from route parts.
	 *
	 * @param array $array Route parts.
	 * @return string Regular expression pattern.
	 **/
	public function partsRegex($parts)
	{
		$regex = '/^';
		
		foreach ($parts as $segment) {
			
			if (!$segment->value) break;
			
			// dont check nested segments
			if ($segment->name == 'nested_controller_path') continue;
			
			switch (true) {
				case ($segment->type == 'static'):
					$part = $segment->value;
					break;
				
				case ($segment->value && !$segment->constraint):
					$regex .= '(\/[A-Za-z0-9_.-\/]+)*';
					break 2;
					break;
				
				default:
					$part = $this->trimRegex($segment->constraint);
					break;
			}
			
			$regex .= "\/{$part}";
		}
		
		$regex .= '$/';
		
		return $regex;
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function trimRegex($pattern)
	{
		return preg_replace('/^\/(.*?)[\/]?$/', "\\1", $pattern);
	}
	
	/**
	 * Get default events array.
	 *
	 * @return array Events array.
	 **/
	public function defaultEvents()
	{
		return $GLOBALS['CREOVEL']['ROUTES']['default']->events;
	}
	
	/**
	 * Get default params array.
	 *
	 * @return array Params array.
	 **/
	public function defaultParams()
	{
		return $GLOBALS['CREOVEL']['ROUTES']['default']->params;
	}
	
	/**
	 * Get default error events array.
	 *
	 * @return array Params array.
	 **/
	public function error()
	{
		return $GLOBALS['CREOVEL']['ROUTES']['default_error']->events;
	}

} // END class Routing

/**
 * Routing Route storage class.
 *
 * @package Creovel
 * @subpackage Creovel.Classes
 * @copyright  2008 Creovel, creovel.org
 * @license    http://creovel.googlecode.com/svn/trunk/License   MIT License
 * @version    $Id:$
 * @since      Class available since Release 0.4.0
 */
class RoutingRoute
{
	/**
	 * URL pattern.
	 *
	 * @var string
	 **/
	public $path;
	
	/**
	 * Events array for route.
	 *
	 * @var array
	 **/
	public $events;
	
	/**
	 * Custom params array for route.
	 *
	 * @var array
	 **/
	public $params;
	
	/**
	 * Part segments for the route.
	 *
	 * @var array
	 **/
	public $parts;
	
	/**
	 * Set object properties.
	 *
	 * @param string $path
	 * @param array $events
	 * @param array $params
	 * @param array $parts Array of RoutingSegments
	 * @return void
	 **/
	public function __construct($path, $events, $params, $parts)
	{
		$this->path = $path;
		$this->events = $this->filterEvents($events);
		$this->params = $this->filterParams($params);
		$this->parts = $parts;
	}
	
	/**
	 * Filter/reformat events array.
	 *
	 * @param array $events
	 * @return array Events array.
	 **/
	public function filterEvents($events)
	{
		$return = array();
		foreach ($events as $label => $event) {
			if ($label == 'nested_controllers') {
				$return[$label] = $event;
				continue;
			}
			$return[$label] = $event->value;
		}
		if (isset($return['nested_controllers'])) {
			$return['nested_controller_path'] = implode(DS, $return['nested_controllers']);
		}
		return $return;
	}
	
	/**
	 * Filter/remove empty $params from array.
	 *
	 * @param array $params
	 * @return array Params array.
	 **/
	public function filterParams($params)
	{
		$return = array();
		foreach ($params as $param) {
			if ($param->value) $return[$param->name] = $param->value;
		}
		return $return;
	}
} // END class RoutingRoute

/**
 * Routing Segment storage class.
 *
 * @package Creovel
 * @subpackage Creovel.Classes
 * @copyright  2008 Creovel, creovel.org
 * @license    http://creovel.googlecode.com/svn/trunk/License   MIT License
 * @version    $Id:$
 * @since      Class available since Release 0.4.0
 */
class RoutingSegment
{
	
	/**
	 * Segment name.
	 *
	 * @var string
	 **/
	public $name;
	
	/**
	 * Segment type 'dynamic' or 'static'.
	 *
	 * @var string
	 **/
	public $type;
	
	/**
	 * Segment value.
	 *
	 * @var mixed
	 **/
	public $value;
	
	/**
	 * Segment constraint is regular expression that value is check against.
	 *
	 * @var string
	 **/
	public $constraint;
	
	/**
	 * Set object properties.
	 *
	 * @param string $name
	 * @param string $type
	 * @param mixed $value
	 * @param string $constraint
	 * @return void
	 **/
	public function __construct($name, $type, $value, $constraint = null)
	{
		$this->name = $name;
		$this->type = $type;
		$this->value = $value;
		$this->constraint = $constraint;
	}
} // END class RoutingSegment

/**
 * Mapper class creates routing rules and saves them
 * to $GLOBALS['CREOVEL']['ROUTES'].
 *
 * @package Creovel
 * @subpackage Creovel.Classes
 * @copyright  2008 Creovel, creovel.org
 * @license    http://creovel.googlecode.com/svn/trunk/License   MIT License
 * @version    $Id:$
 * @since      Class available since Release 0.4.0
 */
class Mapper
{
	/**
	 * Add route to framework.
	 *
	 * @return void
	 **/
	public function connect($route_path = ':controller/:action/:id', $options = null)
	{
		if (count($options)) {
			foreach ( $options as $k => $v ) {
				$temp[self::cleanLabel($k)] = $v;
			}
			$options = $temp;
		}
		
		// set default options
		$options['controller'] = isset($options['controller']) ? $options['controller'] : CREO('default_controller');
		$options['action'] = isset($options['action']) ? $options['action'] : CREO('default_action');
		$options['name'] = ($route_path == ':controller/:action/:id' ? 'default' : ($options['name'] ? $options['name'] : $route_path));
		
		// create path segments
		$path_segments = self::cleanExplode('/', $route_path);
		
		// create uri segments
		$uri_segments = self::cleanExplode('/', Routing::getQueryString());
		//print_obj($uri_segments);
		
		// create segments array
		$segments = array();
		
		// group from path and uri segements and create segment objects
		foreach ($uri_segments as $k => $v) {
			if (isset($path_segments[$k])) {
				$segment = self::createSegment($path_segments[$k], $v);
				$segments[$segment->name] = $segment;
			}
		}
		
		// check $segments has each $path_segment
		foreach ($path_segments as $k => $v) {
			$label = self::cleanLabel($v);
			if (!array_key_exists($label, $segments) && isset($options["{$label}"])) {
				$segment = self::createSegment($v, $options["{$label}"]);
				$segments[$segment->name] = $segment;
			}
		}
		
		// set events
		$events = array();
		if (array_key_exists('controller', $segments) && $segments['controller']->value != 'index') {
			$events['controller'] = $segments['controller'];
		} else {
			$events['controller'] = new RoutingSegment('controller', 'static', $options['controller']);
		}
		if (array_key_exists('action', $segments)  && $segments['action']->value != 'index') {
			$events['action'] = $segments['action'];
		} else {
			$events['action'] = new RoutingSegment('action', 'static', $options['action']);
		}
		
		// set custom options defaults
		foreach ($options as $k => $v) {
			switch (true) {
				case ($k == 'controller'):
				case ($k == 'action'):
				case ($k == 'name'):
				case ($k == 'requirements'):
				// if segment had value skip
				case ($segments[self::cleanLabel($k)]->value):
				break;
				
				default:
					if (preg_match('/:' . self::cleanLabel($k) . '/', $route_path)) {
						$segments[self::cleanLabel($k)] = new RoutingSegment(self::cleanLabel($k), 'dynamic', $v);
					} else {
						$segments[self::cleanLabel($k)] = new RoutingSegment(self::cleanLabel($k), 'static', $v);
					}
				break;
			}
		}
		
		// check for requirements
		if ( isset($options['requirements']) ) foreach ($options['requirements'] as $label => $constraint) {
			$segments[self::cleanLabel($label)]->constraint = $constraint;
		}
		
		// set params, clean segments and get params
		$params = array();
		foreach ($segments as $part) {
			if ($part->type == 'static') continue;
			$params[$part->name] = $part;
		}
		
		/*
		// if default route_path check/set nested controllers
		if ($route_path == ':controller/:action/:id') {
			$path = '';
			if (count($uri_segments) >= 2) {
				foreach ($uri_segments as $arg) {
					if (file_exists(CONTROLLERS_PATH . $path . $arg . '_controller.php')) {
						echo CONTROLLERS_PATH . $path . $arg . '_controller.php';
						echo '<br/>';
						$events['nested_controllers'][] = $arg;
					}
					$path .= $arg . DS;
				}
				
				print_obj($events, 1);
				
				if (isset($events['nested_controllers']) && ($events['nested_controllers'] >= 2)) {
					
					$events['controller'] = new RoutingSegment('controller', 'static', $events['nested_controllers'][ count($events['nested_controllers']) - 1 ]);
					
					foreach ($uri_segments as $key => $arg) {
						if ($arg == $events['controller']->value) {
							$events['action'] = new RoutingSegment('action', 'dynamic', $uri_segments[ $key + 1 ]);
							$params['id'] = new RoutingSegment('id', 'dynamic', $uri_segments[ $key + 2 ]);
						}
					
					}
					
					// pop currentcontroller
					array_pop($events['nested_controllers']);
					
					// clear segments not vaild for nested controllers
					$segments = array();
				}
				
			}
			
			// set default action
			if ( !$events['action']->value ) $events['action']->value = $options['action'];
		
		}
		*/
		
		if (isset($options['nested_controller_path'])) {
		
			$dirs = explode('/', $options['nested_controller_path']);
			
			foreach ($dirs as $dir) {
				if (!trim($dir)) continue;
				$events['nested_controllers'][] = $dir;
			}
		
		}
		
		// clean params
		unset($params[$events['controller']->name]);
		unset($params['controller']);
		unset($params['action']);
		
		// create route object
		$route = new RoutingRoute($route_path, $events, $params, $segments);
		
		// add to routing class
		Routing::add($options['name'], $route);
	}
	
	/**
	 * Create array and remove last value if blank.
	 *
	 * @param string $separator
	 * @param string $string
	 * @return array
	 **/
	public function cleanExplode($separator, $string)
	{
		$temp = explode($separator, $string);
		if (!$temp[0]) array_shift($temp);
		if ((count($temp) > 0) && (!$temp[count($temp) - 1])) array_pop($temp);
		return $temp;
	}
	
	/**
	 * Clean label string by removing ":" from string if the first character.
	 *
	 * @param string $label
	 * @return string
	 **/
	public function cleanLabel($label)
	{
		return $label{0} == ':' ? substr($label, 1) : $label;
	}
	
	/**
	 * Create segments if name begins with ":" it is 'dynamic' else it is
	 * 'static'. Dynamic names means their values can change.
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return object RoutingSegment object.
	 **/
	public function createSegment($name, $value)
	{
		switch (true) {
			// dynamic segement
			case ($name{0} == ':'):
				$name = substr($name, 1);
				return new RoutingSegment($name, 'dynamic', $value);
				break;
			
			// static segement
			default:
				return new RoutingSegment($name, 'static', $name);
				break;
		}
	}
} // END class Mapper