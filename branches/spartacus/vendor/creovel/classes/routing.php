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
		$events = array();
		$params = array();
		@$options['controller'] = $options['controller'] ? $options['controller'] : CREO('default_controller');
		@$options['action'] = $options['action'] ? $options['action'] : CREO('default_action');
		
		// set params
		$params = $options;
		unset($params['controller']);
		unset($params['action']);
		if (count($params)) foreach ($params as $k => $v) {
			$label = self::clean_label($k);
			$params[$label] = $v;
		}
		
		$url_path = explode('/', $url);
		
		// remove first and last if empty
		if (@!$url_path[0]) array_shift($url_path);
		if (@!$url_path[count($url_path) - 1]) array_pop($url_path);
		
		// segment path
		$segments = array();
		foreach ($url_path as $key => $segment) {
			if (!$segment) continue;
			if ($segment == '*') {
				$astrik = $key;
				continue;
			}
			$label = self::clean_label($segment);
			$type = $segment{0} == ':' ? 'dynamic' : 'static';
			$segments[] = @(object) array(
				'name' => $label,
				'type' => $type,
				'value' => $type == 'static' ? '' : $options[$label],
				'constraint' => $requirements[$segment]
				);
		}
		
		$uri_path = explode('/', $GLOBALS['CREOVEL']['ROUTING']['path']);
		
		// remove first and last if empty
		if (@!$uri_path[0]) array_shift($uri_path);
		if (@!$uri_path[count($uri_path) - 1]) array_pop($uri_path);
		
		// update segment values from uri
		foreach ($uri_path as $k => $v) {
			if ($k > count($segments) - 1) break;
			if ($segments[$k]->type == 'dynamic') $segments[$k]->value = $v;
		}
		
		// copy non staic vals to params
		foreach ($segments as $segment) {
			if ($segment->name == 'controller' || $segment->name == 'action') {
				$events[$segment->name] = $segment->value;
				continue;
			}
			if ($segment->type != 'static') {
				$params[$segment->name] = $segment->value;
			}
		}
		
		// add * to params
		if ($astrik && ($astrik < ($max = count($uri_path)))) {
			foreach (range($astrik, $max - 1, 2) as $k) {
				if (@$uri_path[$k]) @$params[$uri_path[$k]] = $uri_path[$k + 1];
			}
		}
		
		// if no events set events
		if (@!$events['controller']) $events['controller'] = $options['controller'];
		if (@!$events['action']) $events['action'] = $options['action'];
		
		// ceate regex
		$regex_all = '([A-Za-z0-9_\-\+.\/]+)';
		$pattern = '/^';
		foreach ($segments as $segment) {
			if ($segment->name == '*') {
				break;
			} else if ($segment->constraint && $segment->value) {
				$pattern .= '\/' . self::trim_slashes($segment->constraint);
			} else if ($segment->type == 'dynamic') {
				if ((substr($pattern, -strlen($regex_all)) != $regex_all)) {
					$pattern .= $regex_all;
				}
			} else {
				$pattern .= '\/' . $segment->name;
			}
		}
		$pattern .= '$/';
		
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
			'params'		=> $params,
			'regex'		=> $regex
			);
		
		if ($name == 'default') {
			$GLOBALS['CREOVEL']['ROUTING']['routes']['default'] = $data;
		} else {
			$GLOBALS['CREOVEL']['ROUTING']['routes'] = array($name => $data) + $GLOBALS['CREOVEL']['ROUTING']['routes'];
		}
	}
	
	/**
	 * Clean label string by removing ":" from string if the first character.
	 *
	 * @param string $label
	 * @return string
	 **/
	public function clean_label($label)
	{
		return $label && $label{0} == ':' ? substr($label, 1) : $label;
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function trim_slashes($pattern)
	{
		return preg_replace('/^\/(.*?)[\/]?$/', "\\1", $pattern);
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
		$uri = $uri ? $uri : $GLOBALS['CREOVEL']['ROUTING']['path'];
		
		// return default route
		if ($uri == '/') {
			// set current
			self::set_current_default();
			
			if ($return_params) {
				return self::default_params();
			} else {
				return self::default_events();
			}
		}
		
		// create pattern to match against URI
		foreach ($GLOBALS['CREOVEL']['ROUTING']['routes'] as $name => $route) {
			
			// skip default route
			if (@!$route['regex']) continue;
			
			// if match return events
			if (preg_match($route['regex'], $uri)) {
				// set current
				$GLOBALS['CREOVEL']['ROUTING']['current'] = $route;
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
	 * undocumented function
	 *
	 * @return void
	 * @author Nesbert Hidalgo
	 **/
	public function set_current_default()
	{
		$GLOBALS['CREOVEL']['ROUTING']['current'] = $GLOBALS['CREOVEL']['ROUTING']['routes']['default'];
	}
	
	/**
	 * Get default events array.
	 *
	 * @return array Events array.
	 **/
	public function default_events()
	{
		return $GLOBALS['CREOVEL']['ROUTING']['routes']['default']['events'];
	}
	
	/**
	 * Get default params array.
	 *
	 * @return array Params array.
	 **/
	public function default_params()
	{
		return $GLOBALS['CREOVEL']['ROUTING']['routes']['default']['params'];
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
		if (isset($GLOBALS['CREOVEL']['ROUTING']['routes'][$route_name]['events'])) {
			return $GLOBALS['CREOVEL']['ROUTING']['routes'][$route_name]['events'];
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
		return $GLOBALS['CREOVEL']['ROUTING']['routes']['default_error']['events'];
	}
} // END class Routing