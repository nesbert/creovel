<?php
/**
 * Routing class to allow for custom pretty URLs.
 *
 * @package     Creovel
 * @subpackage  Core
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.1.0
 * @author      Nesbert Hidalgo
 **/
class ActionRouter extends CObject
{
    /**
     * Add route to framework.
     *
     * @return void
     **/
    public static function map($name, $url, $options = null, $requirements = null, $nested_controller = false)
    {
        $events = array();
        $params = array();
        $options['controller'] = !empty($options['controller'])
                                    ? $options['controller']
                                    : CREO('default_controller');
        $options['action'] = !empty($options['action'])
                                ? $options['action']
                                : CREO('default_action');
        
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
            if (!$segment || $segment == '*') continue;
            $label = self::clean_label($segment);
            $type = $segment{0} == ':' ? 'dynamic' : 'static';
            $segments[] = @(object) array(
                'name' => $label,
                'type' => $type,
                'value' => $type == 'static' ? '' : $options[$label],
                'constraint' => $requirements[$segment]
                );
        }
        
        $uri_path = explode('/', $GLOBALS['CREOVEL']['ROUTING']['base_path']);
        
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
        
        if (CString::contains('*', $url)) {
            $astrik_path = explode('/', $url);
            $start = array_search('*', $astrik_path) - 
                        ($astrik_path[0] ? 0 : 1);
            $end = count($uri_path);
            if ($end > $start) {
                for ($i = $start; $i <= $end; $i += 2) {
                    if (@$uri_path[$i]) @$params[(string) $uri_path[$i]] = $uri_path[$i + 1];
                }
            }
        }
        
        // if no events set events
        if (empty($events['controller'])) $events['controller'] = $options['controller'];
        if (empty($events['action'])) $events['action'] = $options['action'];
        
        // controller index.php fix
        $events['controller'] = basename($events['controller'], '.php');
        
        // create regex
        $regex_all = '([A-Za-z0-9_\-\+.:\/]+|$)';
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
     * Append a route to the routes array ($GLOBALS['CREOVEL']['ROUTES']).
     *
     * @param string $name
     * @param object $route Route object
     * @return void
     **/
    public static function add($name, $url, $events, $params = array(), $regex = '')
    {
        // clean up any file extension for a action
        $events['action'] = self::clean_event($events['action']);
        
        // default last in routes array
        $data = array(
                'name'      => $name,
                'url'       => $url,
                'events'    => $events,
                'params'    => $params,
                'regex'     => $regex
                );
        
        if ($name == 'default') {
            $GLOBALS['CREOVEL']['ROUTING']['routes']['default'] = $data;
        } else {
            $GLOBALS['CREOVEL']['ROUTING']['routes'] = array($name => $data) + $GLOBALS['CREOVEL']['ROUTING']['routes'];
        }
    }
    
    /**
     * Get the events route depending on URI pattern or params.
     *
     * @param string $uri
     * @param boolean $return_params Flag to return params
     * @return array Events|Params array.
     **/
    public static function which($uri = null, $return_params = false)
    {
        // set uri
        if (empty($uri)
            && !empty($GLOBALS['CREOVEL']['ROUTING']['base_path'])) {
            $uri = $GLOBALS['CREOVEL']['ROUTING']['base_path'];
        }
        
        // set static vars
        static $match;
        static $uri_check;
        
        // check if never matched and $uri never checked
        if ($uri_check != $uri) {
            $uri_checked = $uri;
            // create pattern to match against URI
            foreach ($GLOBALS['CREOVEL']['ROUTING']['routes'] as $name => $route) {
                #echo $route['regex'] . " :: $uri<br />";
                // if match return events
                if (preg_match($route['regex'], $uri)) {
                    // set current
                    $GLOBALS['CREOVEL']['ROUTING']['current'] = $route;
                    $match = $route;
                    break;
                }
            }
        }
        
        if (empty($match)) {
            @$GLOBALS['CREOVEL']['ROUTING']['current'] = $GLOBALS['CREOVEL']['ROUTING']['routes']['default'];
            @$match = $GLOBALS['CREOVEL']['ROUTING']['current'];
        }
        
        // return params or events
        if ($return_params) {
            if (isset($match['params'])) {
                if (is_array($match['params']) && end($match['params'])) {
                    $last = key($match['params']);
                    $match['params'][$last] = self::clean_event($match['params'][$last]);
                }
                return $match['params'];
            } else {
                return false;
            }
        } else {
            $match['events']['controller'] = self::clean_event($match['events']['controller']);
            if (!empty($match['events']['action']))
                $match['events']['action'] =
                    self::clean_event($match['events']['action']);
            return $match['events'];
        }
    }
    
    /**
     * Get events from route.
     *
     * @param string $uri
     * @param string $route_name Get events for specific route.
     * @return array Events array.
     **/
    public static function events($uri = null, $route_name = '')
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
    public static function params($uri = null)
    {
        return self::which($uri, true);
    }
    
    /**
     * Get default error events array.
     *
     * @return array Params array.
     **/
    public static function error()
    {
        return self::events('', 'errors');
    }
    
    /**
     * Clean label string by removing ":" from string if the first character.
     *
     * @param string $label
     * @return string
     **/
    public static function clean_label($label)
    {
        return !empty($label) && CString::starts_with(':', $label)
                ? substr($label, 1) : $label;
    }
    
    /**
     * Clean trim pattern.
     *
     * @return void
     **/
    public static function trim_slashes($pattern)
    {
        return preg_replace('/^[\/]?(.*?)[\/]?$/', "\\1", $pattern);
    }
    
    /**
     * Clean event string by removing current file extension.
     *
     * @param string $event
     * @return string
     **/
    public static function clean_event($event)
    {
        // clean up any file extension for a action
        return @preg_replace(
                '/.' . ($GLOBALS['CREOVEL']['VIEW_EXTENSION'] ? $GLOBALS['CREOVEL']['VIEW_EXTENSION'] : 'html') . '$/',
                '',
                $event
                );
    }
} // END class ActionRouter extends CObject