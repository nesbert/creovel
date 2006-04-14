<?php
/*
 * Framework functions.
 */

/**
 * AUTOLOAD ROUTINE
 *
 * @author Nesbert Hidalgo
 */

function __autoload($classname) {

	$folders = split('_', $classname);
	
	if ( count($folders) > 1 ) {
		array_pop($folders);
	}

	$path = implode(DS, $folders);
	
	switch ( true ) {

		case ( strstr($classname, '_controller') ):
			$class = preg_split('/_controller/', $classname);
			$class = str_replace('_', '/', $class[0]);
			$path = CONTROLLERS_PATH.$class.'_controller.php';
			$controller = str_replace('_controller', '', $classname);
			$helper_path = HELPERS_PATH.$controller.'_helper.php';
			if ( is_file($helper_path) ) { require_once($helper_path); }
		break;
			
		case ( strstr($classname, '_model') ):
		case ( strstr($classname, '_mailer') ):
			$path = MODELS_PATH.$classname.'.php';			
			if ( !file_exists($path) ) $path = CREOVEL_PATH.'models'.DS.$classname.'.php';
		break;
		
		case ( strstr($classname, '_interface') ):
			$path = CREOVEL_PATH.'interfaces'.DS.$classname.'.php';
		break;
		
		default:
			$path = CREOVEL_PATH.'classes'.DS.$classname.'.php';
			if ( !file_exists($path) ) $path = CREOVEL_PATH.'adapters'.DS.$classname.'.php';
			if ( !file_exists($path) ) $path = CREOVEL_PATH.'services'.DS.$classname.'.php';
			if ( !file_exists($path) ) $path = APP_PATH.'vendor'.DS.$classname.'.php';
			if ( !file_exists($path) ) $path = CREOVEL_PATH.'vendor'.DS.$classname.'.php';
		break;
		
	}
	
	try {
	
		if ( !file_exists($path) ) {
			throw new Exception("{$classname} not found...");
		} else {
			require_once($path);
		}
	
	} catch(Exception $e) {
		
		echo '<h1>Required File Not Found...</h1>';
		echo "<p>Looking for <b>{$classname}</b> at <b>{$path}</b></p>";
	 	
		foreach ( debug_backtrace() as $path ) {
			echo "<b>File:</b> {$path['file']}<br />";
			echo "<b>Line:</b> {$path['line']}<br />";
			echo "<b>Function:</b> {$path['function']}<br />";
			echo "<hr />";
		}
		
		die();
		
	}
	
}

/**
 * Builds the MVC framework.
 *
 * @author Nesbert Hidalgo
 * @access public
 * @return object
 */

function creovel($params = null) {

	// Require Environment File
	$_ENV['mode'] = (isset($_ENV['mode'])) ? $_ENV['mode'] : 'development';
	require_once CONFIG_PATH."environments/{$_ENV['mode']}.php";

	// set event params
	if ( !$params ) $params = get_event_params();
	
	// set and call controller
	$controller = ($params['controller'] != '') ? str_replace('/', '_', $params['controller']).'_controller' : $_ENV['routes']['default']['controller'].'_controller';;
	$base_controller = new $controller();
	
	// set events and params for framework
	$base_controller->set_properties($params);
	
	// execute action
	$base_controller->execute_action();

	// show page
	$base_controller->build_page();
	
	return $base_controller;
	
}

/**
 * Returns the framework events (CONTORLLER & ACTION).
 *
 * @author Nesbert Hidalgo
 * @access public
 * @return array
 */
 
function get_event_params($param_to_return = null)
{
	// split framework variables and the query_sting
	$url_arr = explode('?', $_SERVER['REQUEST_URI']);

	// set array with framework variables
	$args_arr = explode('/', $url_arr[0]);

	// set array with framework variables
	array_shift($args_arr);

	// support for nested controllers
	for ($i = 0; $i < count($args_arr); $i++) {
		$path = array();
		for ($k = 0; $k <= $i; $k++) { $path[] = $args_arr[$k]; }
		if (file_exists(APP_PATH."controllers/".implode('/', $path)."_controller.php")) {
			$params['controller'] = implode('/', $path);
			$params['action'] = $args_arr[$i + 1];
			$params['id'] = $args_arr[$i + 2];
			break;
		}
	}
		
	$requests = array($_GET, $_POST);

	// add each request attribute into $params
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
 * Alias to get_event_params.
 *
 * @author Nesbert Hidalgo
 * @access public
 * @return array
 */

function get_params($event_to_return = null)
{

	return get_event_params($event_to_return);
 
}

/**
 * Returns the current CONTORLLER.
 *
 * @author Nesbert Hidalgo
 * @access public
 * @return string
 */

function get_controller()
{

	return get_event_params('controller');
 
}

/**
 * Returns the current ACTION.
 *
 * @author Nesbert Hidalgo
 * @access public
 * @return string
 */

function get_action()
{

	return get_event_params('action');
 
}

/**
 * Returns the framework version.
 *
 * @author Nesbert Hidalgo
 * @access public
 * @return string
 */

function get_version()
{

	return VERSION;
 
}

/**
 * Returns the framework release date.
 *
 * @author Nesbert Hidalgo
 * @access public
 * @return string
 */

function get_release_date()
{

	return RELEASE_DATE;
 
}

?>
