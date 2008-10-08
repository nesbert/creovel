<?php

/*
	Class: routing
	
	Routing class to allow for customer URLs.
*/

class Routing
{
	
	// Section: Public

	/*
		Property: uri
		
		Uniform Resource Identifier.
	*/
	
	public static $uri = '';
	
	/*
		Property: current
		
		Route object of matching URI and route.
	*/
	
	public static $current;
	

	public function getURI($uri = null)
	{
		if (!$uri) $uri = $_SERVER['REQUEST_URI'];
		$uri = explode('?', $uri);
		return  $uri[0];
	}
	
	/*
		Function: add
		
		Append a route to the routes array.
		
		Parameters:
		
			name - Route name.
			route - Route object.
	*/
	
	public function add($name, $route)
	{
		// default last in routes array
		if ($name == 'default') {
			$GLOBALS['CREOVEL']['ROUTES'] = array($name => $route);
		} else {
			$GLOBALS['CREOVEL']['ROUTES'] = array($name => $route) + $GLOBALS['CREOVEL']['ROUTES'];
		}
	}
	
	/*
		Function: events
		
		Get events from route.
		
		Parameters:
		
			uri - URI.
			
		Returns:
		
			An array of events.
	*/
	
	public function events($uri, $route_name = '')
	{
		if (isset($this->routes[$route_name]->events)) {
			return $this->routes[$route_name]->events;
		}
		return self::which($uri);
	}
	
	/*
		Function: params
		
		Get params from route.
		
		Parameters:
		
			uri - URI.
			
		Returns:
		
			An array of params.
	*/
	
	public function params($uri)
	{
		return self::which($uri, true);
	}
	
	/*
		Function: which_route
		
		Get the events route depending on URI pattern or params.
		
		Parameters:
		
			uri - URI.
			return_params - Boolean.
			
		Returns:
		
			An array of events/params.
	*/
	
	public function which($uri = null, $return_params = false)
	{
		$uri = self::getURI($uri);
		
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
		
		// set uri
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
		
		// set current
		self::$current = $GLOBALS['CREOVEL']['ROUTES']['default'];
		
		if ($return_params) {
			return self::defaultParams();
		} else {
			return self::defaultEvents();
		}
		
	}
	
	public function partsRegex($parts)
	{
		$regex = '/^';
		
		//print_obj($parts);
		
		foreach ($parts as $segment)
		{
			if (!$segment->value) break;
			// dont check nested segments
			if ($segment->name == 'nested_controller_path') continue;
			switch (true) {
				case ( $segment->type == 'static' ):
					$part = $segment->value;
				break;
				
				case ( $segment->value && !$segment->constraint ):
					$regex .= '(\/[A-Za-z0-9_.-\/]+)*';
					break 2;
				break;

				default:
					$part = $this->trim_regex($segment->constraint);
				break;
			}
			
			$regex .= "\/{$part}";
		}

		$regex .= '$/';

		return $regex;
	}
	
	public function trimRegex($pattern)
	{
		return preg_replace('/^\/(.*?)[\/]?$/', "\\1", $pattern);
	}
	
	public function defaultEvents()
	{
		return $GLOBALS['CREOVEL']['ROUTES']['default']->events;
	}
	
	public function defaultParams()
	{
		return $GLOBALS['CREOVEL']['ROUTES']['default']->params;
	}
	
	public function error_events()
	{
		return $GLOBALS['CREOVEL']['ROUTES']['error']->events;
	}

}

class Route
{
	public $path;
	public $events;
	public $params;
	public $parts;
	
	public function __construct($path, $events, $params, $parts)
	{
		$this->path = $path;
		$this->events = $this->filterEvents($events);
		$this->params = $this->filterParams($params);
		$this->parts = $parts;
	}
	
	public function filterEvents($events)
	{
		$return = array();
		foreach ( $events as $label => $event ) {
			if ( $label == 'nested_controllers' ) {
				$return[$label] = $event;
				continue;
			}
			$return[$label] = $event->value;
		}
		if ( isset($return['nested_controllers']) ) {
			$return['nested_controller_path'] = implode(DS, $return['nested_controllers']);
		}
		return $return;
	}
	
	public function filterParams($params)
	{
		$return = array();
		foreach ( $params as $param ) {
			if ($param->value) $return[$param->name] = $param->value;
		}
		return $return;
	}	
}

class Mapper
{

	public function connect($route_path = ':controller/:action/:id', $options = null)
	{
		if ( count($options) ) {
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
		//print_obj($path_segments);
		
		// create uri segments
		$uri_segments = self::cleanExplode('/', Routing::getURI());
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
			$events['controller'] = new Segment('controller', 'static', $options['controller']);
		}
		if (array_key_exists('action', $segments)  && $segments['action']->value != 'index') {
			$events['action'] = $segments['action'];
		} else {
			$events['action'] = new Segment('action', 'static', $options['action']);
		}
		
		// set custom options defaults
		foreach ($options as $k => $v) {
			switch ( true )
			{
				case ( $k == 'controller' ):
				case ( $k == 'action' ):
				case ( $k == 'name' ):
				case ( $k == 'requirements' ):
				// if segment had value skip
				case ( $segments[self::cleanLabel($k)]->value ):
				break;
				
				default:
					if ( preg_match('/:'.self::cleanLabel($k).'/', $route_path) ) {
						$segments[self::cleanLabel($k)] = new Segment(self::cleanLabel($k), 'dynamic', $v);
					} else {
						$segments[self::cleanLabel($k)] = new Segment(self::cleanLabel($k), 'static', $v);
					}
				break;
			}
		}
		
		// check for requirements
		if ( isset($options['requirements']) ) foreach ( $options['requirements'] as $label => $constraint ) {
			$segments[self::cleanLabel($label)]->constraint = $constraint;
		}
		
		// set params, clean segments and get params
		$params = array();
		foreach ($segments as $part) {
			if ($part->type == 'static') continue;
			$params[$part->name] = $part;
		}
		
		// if default route_path check/set nested controllers
		if ($route_path == ':controller/:action/:id') {
			$path = '';
			if ( count($uri_segments) >= 2 ) {
				foreach ( $uri_segments as $arg ) {
					if ( file_exists(CONTROLLERS_PATH.$path.$arg.'_controller.php') ) {
						$events['nested_controllers'][] = $arg;
					}
					$path .= $arg.DS;
				}
				
				if ( $events['nested_controllers'] >= 2 ) {
					
					$events['controller'] = new Segment('controller', 'static', $events['nested_controllers'][ count($events['nested_controllers']) - 1 ]);
				
					foreach ( $uri_segments as $key => $arg ) {
						if ( $arg == $events['controller']->value ) {
							$events['action'] = new Segment('action', 'dynamic', $uri_segments[ $key + 1 ]);
							$params['id'] = new Segment('id', 'dynamic', $uri_segments[ $key + 2 ]);
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
		$route = new Route($route_path, $events, $params, $segments);
		
		// add to routing class
		Routing::add($options['name'], $route);
	}
	
	public function cleanExplode($seperator, $string)
	{
		$temp = explode($seperator, $string);
		if (!$temp[0]) array_shift($temp);
		if ((count($temp) > 0) && (!$temp[count($temp) - 1])) array_pop($temp);
		return $temp;
	}
	
	public function cleanLabel($label)
	{
		 return ( $label{0} == ':' ? substr($label, 1) : $label );
	}
	
	private function createSegment($name, $value)
	{
		switch ( true )
		{
			// dynamic segement
			case ( $name{0} == ':' ):
				$name = substr($name, 1);
				$segment = new Segment($name, 'dynamic', $value);
			break;
			
			// static segement
			default:
				$segment = new Segment($name, 'static', $name);
			break;
		}
		return $segment;
	}
	
}

class Segment
{
	
	public $name;
	public $type;
	public $value;
	public $constraint;
	
	public function __construct($name, $type, $value, $constraint = null)
	{
		$this->name = $name;
		$this->type = $type;
		$this->value = $value;
		$this->constraint = $constraint;
	}

}