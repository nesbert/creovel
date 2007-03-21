<?php

class routing
{
	
	public static $routes;
	public static $uri = '';
	
	function __construct()
	{
		$this->set_uri();
	}

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

			route_path - Route path string.
	*/

	public function add_route($route_path, $segments, $events, $name = null)
	{
		if ( $name == 'default' || ( !count($this->routes) && !$name ) ) {
			$label = 'default';
		} else if ( !$name ) {
			$label = $route_path;
		} else {
			$label = $name;
		}
		
		// clean segments and get params
		$params = array();
		foreach ( $segments as $part ) {
			if ( $part->type == 'static' ) continue;
			$params[$part->name] = $part;
		}

		unset($params[$events['controller']->name]);
		unset($params['controller']);
		unset($params['action']);
		
		$obj = (object) array( 'route_path' => $route_path, 'events' => $events, 'params' => $params, 'parts' => $segments );
		
		// default last in routes array
		if ( $label == 'default' ) {
			$this->routes[$label] = $obj;
		} else {
			$this->routes = array_merge(array( $label =>  $obj ), $this->routes);
		}
	}
	
	public function events($uri)
	{
		return $this->which_route($uri);
	}
	
	public function params($uri)
	{
		return $this->which_route($uri, true);
	}
	
	public function filter_events($events)
	{
		return array( 'controller' => $events['controller']->value, 'action' => $events['action']->value );
	}
	
	public function filter_params($params)
	{
		$return = array();
		foreach ($params as $param ) {
			if ($param->value) $return[$param->name] = $param->value;
		}
		return $return;
	}
	
	public function which_route($uri = null, $return_params = false)
	{
		$uri = $uri ? $uri : $this->uri;
		#print_obj($this, 1);
		// return default route
		if ( $uri == '/' ) return $this->route_default($return_params);
		
		// set uri
		$uri = $this->trim_regex($uri);
		
		$pieces = explode('/', $uri);
		
		foreach ( $this->routes as $name => $route ) {
			// skip default route
			if ( $name == 'default' ) continue;
			// build pattern form parts
			$pattern = $this->parts_regex($route->parts, count($pieces));
			// if match return events
			#print_obj("{$name} -> preg_match($pattern, $uri)");
			if ( preg_match($pattern, $uri) ) {
				#print_obj("{$name} -> preg_match($pattern, $uri)");
				if ($return_params) {
					return $this->filter_params($route->params);
				} else {
					return $this->filter_events($route->events);
				}
			}
		}
		
		return $this->route_default($return_params);
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
	
	public function route_default($return_params = false)
	{
		if ($return_params) {
			return $this->filter_params($this->routes['default']->params);
		} else {
			return $this->filter_events($this->routes['default']->events);
		}
	}
	
	public function route_error()
	{
		return $this->filter_events($this->routes['error']->events);
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
		
		// add to routing class
		$_ENV['routing']->add_route($route_path, $segments, $events, $options['name']);
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
	
	public function is_match($value)
	{
		if ( $this->value == $value ) return true;
		if ( !$this->constraint ) return false;
		if ( (bool) preg_match($this->constraint, $value) ) return true;
		return false;
	}

}
?>