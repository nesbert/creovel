<?php

/*
	Class: routing
	
	Routing class to allow for customer URLs.
*/

class routing
{
	
	// Section: Public

	/*
		Property: uri
		
		Uniform Resource Identifier.
	*/
	
	public $uri = '';
	
	/*
		Property: current
		
		Route object of matching URI and route.
	*/
	
	public $current;
	
	/*
		Property: routes
		
		Array of all routes created.
	*/
	
	public $routes;
	
	/*
		Function: set_uri
		
		Set $uri for class and clean it from params.
		
		Parameters:
		
			uri - URI string.
	*/
	
	
	function __construct()
	{
		$this->set_uri();
	}
	
	/*
		Function: set_uri
		
		Set $uri for class and clean it from params.
		
		Parameters:
		
			uri - URI string.
	*/
	
	public function set_uri($uri = null)
	{
		if ( !$uri ) {
			$uri = explode('?', $_SERVER['REQUEST_URI']);
			$uri = $uri[0];
		}
		$this->uri = $uri;
	}
	
	/*
		Function: add_route
		
		Append a route to the routes array
		
		Parameters:
		
			name - Route name.
			route - Route object.
	*/
	
	public function add_route($name, $route)
	{
		// set route name
		//if ( !count($this->routes) && !$name ) $name = 'default';
		
		// default last in routes array
		if ( $name == 'default' ) {
			$this->routes[$name] = $route;
		} else {
			$this->routes = array_merge(array( $name =>  $route ), $this->routes);
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
	
	public function events($uri)
	{
		return $this->which_route($uri);
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
		return $this->which_route($uri, true);
	}
	
	public function which_route($uri = null, $return_params = false)
	{
		$uri = $uri ? $uri : $this->uri;

		// return default route
		if ( $uri == '/' ) {
			// set current
			$this->current = $this->routes['default'];
			if ($return_params) {
				return $this->default_params();
			} else {
				return $this->default_events();
			}
		}
		
		// set uri
		$uri = $this->trim_regex($uri);
		
		$pieces = explode('/', $uri);
		
		foreach ( $this->routes as $name => $route ) {
			// skip default route
			if ( $name == 'default' ) continue;
			// build pattern form parts
			$pattern = $this->parts_regex($route->parts, count($pieces));
			// if match return events
			if ( preg_match($pattern, $uri) ) {
				// set current
				$this->current = $route;
				if ($return_params) {
					return $route->params;
				} else {
					return $route->events;
				}
			}
		}
		
		// set current
		$this->current = $this->routes['default'];
		
		if ($return_params) {
			return $this->default_params();
		} else {
			return $this->default_events();
		}
		
	}
	
	public function parts_regex($parts, $limit)
	{
		$regex = '/^';
		$count = 0;
		foreach ( $parts as $segment ) {
			$count++;
			
			switch ( true ) {
				
				case ( $segment->type == 'static' ):
					$regex .= $segment->value;
				break;
				
				case ( $segment->value && !$segment->constraint ):
					$regex .= '\w*';
				break;
				
				default:
					$regex .= $this->trim_regex($segment->constraint);
				break;
			}
			
			if ( $count == $limit ) break;
			
			if ( count($parts) != $count ) {
				$regex .= '\/';
			}
		}
		$regex .= '/';
		
		//echo $regex;
		return $regex;
	}
	
	function trim_regex($pattern)
	{
		$pattern = preg_replace('/^\//', '', $pattern);
		$pattern = preg_replace('/\/$/', '', $pattern);
		return $pattern;
	}
	
	public function default_events()
	{
		return $this->routes['default']->events;
	}
	
	public function default_params()
	{
		return $this->routes['default']->params;
	}
	
	public function error_events()
	{
		return $this->routes['error']->events;
	}

}

class route
{
	public $path;
	public $events;
	public $params;
	public $parts;
	
	public function __construct($path, $events, $params, $parts)
	{
		$this->path = $path;
		$this->events = $this->filter_events($events);
		$this->params = $this->filter_params($params);
		$this->parts = $parts;
	}
	
	public function filter_events($events)
	{
		$return = array();
		foreach ( $events as $label => $event ) {
			if ( $label == 'nested_controllers' ) {
				$return[$label] = $event;
				continue;
			}
			$return[$label] = $event->value;
		}
		if ( $return['nested_controllers'] ) {
			$return['nested_controller_path'] = implode(DS, $return['nested_controllers']);
		}
		return $return;
	}
	
	public function filter_params($params)
	{
		$return = array();
		foreach ( $params as $param ) {
			if ($param->value) $return[$param->name] = $param->value;
		}
		return $return;
	}	
}

class mapper
{

	public function connect($route_path = ':controller/:action/:id', $options = null)
	{
		if ( count($options) ) {
			foreach ( $options as $k => $v ) {
				$temp[self::clean_label($k)] = $v;
			}
			$options = $temp;
		}

		// set default options
		$options['controller'] = isset($options['controller']) ? $options['controller'] : 'index';
		$options['action'] = isset($options['action']) ? $options['action'] : 'index';
		$options['name'] = ( $route_path == ':controller/:action/:id' ? 'default' : ( $options['name'] ? $options['name'] : $route_path ) );
		
		// create path segments
		$path_segments = self::clean_explode('/', $route_path);
		//print_obj($path_segments);
		
		// create uri segments
		$uri_segments = self::clean_explode('/', $_ENV['routing']->uri);
		//print_obj($uri_segments);
		
		// create segments array
		$segments = array();
		
		// group from path and uri segements and create segment objects
		foreach ( $uri_segments as $k => $v ) {
			if ( isset($path_segments[$k]) ) {
				$segment = self::create_segment($path_segments[$k], $v);
				$segments[$segment->name] = $segment;
			}
		}
		
		// check $segments has each $path_segment
		foreach ( $path_segments as $k => $v ) {
			$label = self::clean_label($v);
			if ( !array_key_exists($label, $segments) ) {
				$segment = self::create_segment($v, $options[$label]);
				$segments[$segment->name] = $segment;
			}
		}
		
		// set events
		$events = array();
		if ( array_key_exists('controller', $segments) && $segments['controller']->value != 'index' ) {
			$events['controller'] = $segments['controller'];
		} else {
			$events['controller'] = new segment('controller', 'static', $options['controller']);
		}
		if ( array_key_exists('action', $segments)  && $segments['action']->value != 'index' ) {
			$events['action'] = $segments['action'];
		} else {
			$events['action'] = new segment('action', 'static', $options['action']);
		}
		
		// set custom options defaults
		foreach ( $options as $k => $v ) {
			switch ( true )
			{
				case ( $k == 'controller' ):
				case ( $k == 'action' ):
				case ( $k == 'name' ):
				case ( $k == 'requirements' ):
				// if segment had value skip
				case ( $segments[self::clean_label($k)]->value ):
				break;
				
				default:
					if ( preg_match('/:'.self::clean_label($k).'/', $route_path) ) {
						$segments[self::clean_label($k)] = new segment(self::clean_label($k), 'dynamic', $v);
					} else {
						$segments[self::clean_label($k)] = new segment(self::clean_label($k), 'static', $v);
					}
				break;
			}
		}
		
		// check for requirements
		if ( isset($options[requirements]) ) foreach ( $options['requirements'] as $label => $constraint ) {
			$segments[self::clean_label($label)]->constraint = $constraint;
		}
		
		// set params, clean segments and get params
		$params = array();
		foreach ( $segments as $part ) {
			if ( $part->type == 'static' ) continue;
			$params[$part->name] = $part;
		}
		
		// if default route_path check/set nested controllers
		if ( $route_path == ':controller/:action/:id' ) {
			$path = '';
			if ( count($uri_segments) >= 2 ) {
				foreach ( $uri_segments as $arg ) {
					if ( file_exists(CONTROLLERS_PATH.$path.$arg.'_controller.php') ) {
						$events['nested_controllers'][] = $arg;
					}
					$path .= $arg.DS;
				}
				
				if ( $events['nested_controllers'] >= 2 ) {
					
					$events['controller'] = new segment('controller', 'static', $events['nested_controllers'][ count($events['nested_controllers']) - 1 ]);
				
					foreach ( $uri_segments as $key => $arg ) {
						if ( $arg == $events['controller']->value ) {
							$events['action'] = new segment('action', 'dynamic', $uri_segments[ $key + 1 ]);
							$params['id'] = new segment('id', 'dynamic', $uri_segments[ $key + 2 ]);
						}
					
					}
					
					// pop currentcontroller
					array_pop($events['nested_controllers']);
					
					// clear segments not vaild for nested controllers
					$segments = array();
				}
				
			}
			
		}
		
		// clean params
		unset($params[$events['controller']->name]);
		unset($params['controller']);
		unset($params['action']);
		
		// create route object
		$route = new route($route_path, $events, $params, $segments);
		
		// add to routing class
		$_ENV['routing']->add_route($options['name'], $route);
	}
	
	public function clean_explode($seperator, $string)
	{
		$temp = explode($seperator, $string);
		if ( !$temp[0] ) array_shift($temp);
		if ( !$temp[count($temp) - 1] ) array_pop($temp);
		return $temp;
	}
	
	public function clean_label($label)
	{
		 return ( $label{0} == ':' ? substr($label, 1) : $label );
	}
	
	private function create_segment($name, $value)
	{
		switch ( true )
		{
			// dynamic segement
			case ( $name{0} == ':' ):
				$name = substr($name, 1);
				$segment = new segment($name, 'dynamic', $value);
			break;
			
			// static segement
			default:
				$segment = new segment($name, 'static', $name);
			break;
		}
		return $segment;
	}
	
}

class segment
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

?>