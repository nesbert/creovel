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
	 * Add route to framework.
	 *
	 * @return void
	 **/
	public function map($name, $url, $options = null, $requirements = null, $nested_controller = false)
	{
		$uri = explode('?', $_SERVER['REQUEST_URI']);
		
		// create path segments
		$path_segments = self::cleanExplode('/', $url);
		
		// create uri segments
		$uri_segments = self::cleanExplode('/', $uri[0]);
		$uri_segments_count = count($uri_segments);
		
		#print_obj($path_segments);print_obj($uri_segments);
		
		// set default vals
		$default_params = array();
		if (count($options)) foreach ($options as $k => $v) {
			switch (true) {
				case ($k == 'controller'):
				case ($k == 'action'):
					break;
					
				default:
					$default_params[$k] = $v;
					break;
			}
		}
		
		// create events
		$events = array();
		
		// set controller & action
		if ($nested_controller) {
			die('Nested controller support not yet complete!');
		} else {
			if ($uri_segments_count && in_array(':controller', $path_segments)) {
				if (isset($uri_segments[array_search(':controller', $path_segments)])) {
					$events['controller'] = $uri_segments[array_search(':controller', $path_segments)];
				} else {
					$events['controller'] = isset($options['controller']) ? $options['controller'] : CREO('default_controller');
				}
			} else {
				$events['controller'] = isset($options['controller']) ? $options['controller'] : CREO('default_controller');
			}
			if ($uri_segments_count && in_array(':action', $path_segments)) {
				if (isset($uri_segments[array_search(':action', $path_segments)])) {
					$events['action'] = $uri_segments[array_search(':action', $path_segments)];
				} else {
					$events['action'] = isset($options['action']) ? $options['action'] : CREO('default_action');
				}
			} else {
				$events['action'] = isset($options['action']) ? $options['action'] : CREO('default_action');
			}
		}
		
		// set params
		$params = $options;
		unset($params['controller']);
		unset($params['action']);
		
		if ($uri_segments_count) {
			foreach ($path_segments as $k => $v) {
				switch (true) {
					case ($v == ':controller'):
					case ($v == ':action'):
						break;
						
					case ($v == '*'):
						foreach (range($k, $uri_segments_count) as $uri_key) {
							if (isset($uri_segments[$uri_key + 1]) && ($uri_segments[$uri_key] < 1)) {
								$params[$uri_segments[$uri_key]] = $uri_segments[$uri_key + 1];
								continue;
							}
						}
						break;
					
					case ($v{0} == ':'):
						$params[self::cleanLabel($v)] = isset($uri_segments[$k]) ? $uri_segments[$k] : '';
					    break;
				}
			}
		}
		
		$pattern = '';
		if (count($requirements)) {
			// set regex pattern
			$pattern = '/^';
			
			#print_obj($path_segments);print_obj($params);print_obj($requirements);
			
			foreach ($path_segments as $part) {
				
				if ($part == '*') {
					break;
				}
				
				if ($part{0} == ':') {
					$label = self::cleanLabel($part);
					if (isset($requirements[$part])) {
						$pattern .= '\/' . self::trimSlashes($requirements[$part]);
					} elseif (isset($params[$label]) && $params[$label]) {
						$pattern .= '([A-Za-z0-9_\-\+.\/]+)';
					}
				} else {
					$pattern .= '\/' . $part;
				}
			}
			
			$pattern .= '$/';
		}
		
		
		if (count($params)) foreach ($params as $k => $v) {
			$label = self::cleanLabel($k);
			if (isset($params[$label]) && !$v) {
				$params[$label] = isset($default_params[$label]) ? $default_params[$label] : '';
			}
		}
		
		self::add($name, $url, $events, $params, $pattern);
	}
	
	/**
	 * Append a route to the routes ($GLOBALS['CREOVEL']['ROUTES']) array.
	 *
	 * @param string $name
	 * @param object $route Route object
	 * @return void
	 **/
	public function add($name, $url, $events, $params = array(), $regex = '')
	{
		// default last in routes array
		$data = array(
			'name'			=> $name,
			'url'			=> $url,
			'events'		=> $events,
			'params'		=> $params
			);
		if ($regex) {
			$data['regex'] = $regex;
		}
		if ($name == 'default') {
			$GLOBALS['CREOVEL']['ROUTING']['ROUTES']['default'] = $data;
		} else {
			$GLOBALS['CREOVEL']['ROUTING']['ROUTES'] = array($name => $data) + $GLOBALS['CREOVEL']['ROUTING']['ROUTES'];
		}
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
		
		if (count($GLOBALS['CREOVEL']['ROUTING']['CURRENT'])) {
			if ($return_params) {
				return $GLOBALS['CREOVEL']['ROUTING']['CURRENT']['params'];
			} else {
				return $GLOBALS['CREOVEL']['ROUTING']['CURRENT']['events'];
			}
		}
		
		// return default route
		if ($uri == '/') {
			// set current
			self::setCurrentDefault();
			
			if ($return_params) {
				return self::defaultParams();
			} else {
				return self::defaultEvents();
			}
		}
		
		// create pattern to match against URI
		foreach ($GLOBALS['CREOVEL']['ROUTING']['ROUTES'] as $name => $route) {
			// skip default route
			if (!isset($route['regex'])) continue;
			
			// if match return events
			if (preg_match($route['regex'], '/'. self::trimSlashes($uri)) ) {
				// set current
				$GLOBALS['CREOVEL']['ROUTING']['CURRENT'] = $route;
				if ($return_params) {
					return $route['params'];
				} else {
					return $route['events'];
				}
			}
		}
		
		// set current route
		self::setCurrentDefault();
		
		if ($return_params) {
			return self::defaultParams();
		} else {
			return self::defaultEvents();
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
	 * undocumented function
	 *
	 * @return void
	 * @author Nesbert Hidalgo
	 **/
	public function setCurrentDefault()
	{
		$GLOBALS['CREOVEL']['ROUTING']['CURRENT'] = $GLOBALS['CREOVEL']['ROUTING']['ROUTES']['default'];
	}
	
	/**
	 * Get default events array.
	 *
	 * @return array Events array.
	 **/
	public function defaultEvents()
	{
		return $GLOBALS['CREOVEL']['ROUTING']['ROUTES']['default']['events'];
	}
	
	/**
	 * Get default params array.
	 *
	 * @return array Params array.
	 **/
	public function defaultParams()
	{
		return $GLOBALS['CREOVEL']['ROUTING']['ROUTES']['default']['params'];
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function trimSlashes($pattern)
	{
		return preg_replace('/^\/(.*?)[\/]?$/', "\\1", $pattern);
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
		if (isset($GLOBALS['CREOVEL']['ROUTING']['ROUTES'][$route_name]['events'])) {
			return $GLOBALS['CREOVEL']['ROUTING']['ROUTES'][$route_name]['events'];
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
	 * Get default error events array.
	 *
	 * @return array Params array.
	 **/
	public function error()
	{
		return $GLOBALS['CREOVEL']['ROUTES']['default_error']['events'];
	}
} // END class Routing