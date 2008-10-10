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
	 * Append a route to the routes ($GLOBALS['CREOVEL']['ROUTES']) array.
	 *
	 * @param string $name
	 * @param object $route Route object
	 * @return void
	 **/
	public function add($name, $url, $options = null)
	{
		// default last in routes array
		$data = array(
			'name'			=> $name,
			'url'			=> $url,
			'controller'	=> $options['controller'],
			'action'		=> $options['action'],
			'params'		=> $options['params'],
			'rule'			=> $options['rule']
			);
		if ($name == 'default') {
			$GLOBALS['CREOVEL']['ROUTING']['ROUTES']['default'] = $data;
		} else {
			$GLOBALS['CREOVEL']['ROUTING']['ROUTES'] = array($name => $data) + $GLOBALS['CREOVEL']['ROUTES'];
		}
	}
	
	/**
	 * Add route to framework.
	 *
	 * @return void
	 **/
	public function map($name, $url, $options = null)
	{
		// set defaults
		$options['controller'] = isset($options['controller']) ? $options['controller'] : CREO('default_controller');
		$options['action'] = isset($options['action']) ? $options['action'] : CREO('default_action');
		$options['params'] = '';
		$options['rule'] = '';
		
		
		// create path segments
		$path_segments = self::cleanExplode('/', $url);
		print_obj($path_segments);
		
		// create uri segments
		$uri_segments = self::cleanExplode('/', Routing::getQueryString());
		print_obj($uri_segments);
		
		self::add($name, $url, $options);
		print_obj($GLOBALS['CREOVEL']['ROUTING'], 1);
		
		// create segments array
		$segments = array();
		$used_keys = array();
		
		// no controllers in segment include default segment
		#$segments['controllers'][$options['controller']] = new RoutingSegment('controller', 'default', $options['controller'], null, CONTROLLERS_PATH . $options['controller'] . '_controller.php');
		
		// no action in segment include default action
		#$segments['action'] = new RoutingSegment('action', 'default', $options['action'], null);;
		
		foreach ($path_segments as $key => $path) {
			switch (true) {
				case $path == ':controller':
					$controller_path = '';
					if (count($uri_segments)) foreach (range($key, (count($uri_segments) - 1)) as $count => $num) {
						if (file_exists($location = CONTROLLERS_PATH . $controller_path . $uri_segments[$num] . '_controller.php')) {
							//if (!$count) unset($segments['controllers'][$options['controller']]);
							$segment = new RoutingSegment('controller', 'dynamic', $uri_segments[$num], null, $location);
							$segments['controllers'][$uri_segments[$num]] = $segment;
							$controller_path .= $uri_segments[$num] . DS;
							$used_keys[] = $num;
						}
					}
					break;
				
				case $path == ':action':
					if (!isset($uri_segments[$key]) || in_array($key, $used_keys)) {
						$action = $options['action'];
					} else {
						$action = $uri_segments[$key];
					}
					$segment = new RoutingSegment('action', 'dynamic', $action);
					$segments[$segment->name] = $segment;
					break;
				
				default:
					$segments['parts'][$path] = self::createSegment($path, (isset($uri_segments[$key]) ? $uri_segments[$key] : ''));
					break;
			}
		}
		
		// no controllers in segment include default segment
		if (!isset($segments['controllers'][$options['controller']])) {
			$segments['controllers'][$options['controller']] = new RoutingSegment('controller', 'default', $options['controller'], null, CONTROLLERS_PATH . $options['controller'] . '_controller.php');
		}
		
		// no action in segment include default action
		if (!isset($segments['action'])) {
			$segments['action'] = new RoutingSegment('action', 'default', $options['action'], null);;
		}
		
		// check for requirements
		if (isset($options['requirements'])) foreach ($options['requirements'] as $label => $constraint) {
			if (isset($segments['parts'][$label])) {
				$segments['parts'][$label]->constraint = $constraint;
			}
		}
		
		// create route object
		$route = new RoutingRoute($route_path, $segments);
		
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
			
			// set regex pattern
			$pattern = '/^';
			
			// build pattern from parts
			foreach ($route->segments as $segment_type => $segment) {
				if ($segment_type != 'action') {
					foreach ($segment as $seg) {
						$pattern .= self::regexSegment($seg);
					}
				}  else {
					$pattern .= self::regexSegment($segment);
				}
			}
			
			$pattern .= '$/';
			
			
			echo $pattern . ' :: ' . $uri . '<br />';
			
			// if match return events
			if (preg_match($pattern, '/'. $uri) ) {
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
	public function regexSegment($segment)
	{
		$regex = '';
		
		if (!$segment->value) return;
		
		switch (true) {
			case ($segment->type == 'default'):
				break;
				case ($segment->type == 'static'):
					$regex = $segment->value;
					break;
				
			case ($segment->value && !$segment->constraint):
				if (($segment->name == 'controller') ||
					($segment->name == 'action')) {
					$regex = '(\/[A-Za-z0-9_]+)*';
				} else {
					$regex = '(\/[A-Za-z0-9_.-\/]+)*';
				}
				break;
				
			default:
				$regex = self::trimRegex($segment->constraint);
				break;
		}
		
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
	public $segments;
	
	/**
	 * Set object properties.
	 *
	 * @param string $path
	 * @param array $events
	 * @param array $params
	 * @param array $parts Array of RoutingSegments
	 * @return void
	 **/
	public function __construct($path, &$segments)
	{
		$this->path = $path;
		$this->segments = $segments;
		
		end($this->segments['controllers']);
		
		// set events
		$this->events = array(
			'controller'	=> $this->segments['controllers'][key($this->segments['controllers'])]->value,
			'action' 		=> $this->segments['action']->value
			);
		
		// set params, clean segments and get params
		$this->params = array();
		if (isset($this->segments['parts'])) foreach ($this->segments['parts'] as $part) {
			if ($part->type == 'static') continue;
			$this->params[$part->name] = $part;
		}
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
	 * Segment path used by controller segment types.
	 *
	 * @var string
	 **/
	public $path;
	
	/**
	 * Set object properties.
	 *
	 * @param string $name
	 * @param string $type
	 * @param mixed $value
	 * @param string $constraint
	 * @return void
	 **/
	public function __construct($name, $type, $value, $constraint = null, $path = '')
	{
		$this->name = $name;
		$this->type = $type;
		$this->value = $value;
		$this->constraint = $constraint;
		$this->path = $path;
	}
} // END class RoutingSegment