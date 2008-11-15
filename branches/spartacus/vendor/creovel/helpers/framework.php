<?php
/**
 * Contains all Creovel specific functions.
 *
 * @package Creovel
 * @subpackage Creovel.Helpers
 **/
/**
 * Autoload routine for controllers, interfaces, adapters, services, vendor,
 * mailer and models. Also creates "Virtual Model" if table exists (useful
 * for basic model functions and prototyping).
 *
 * @global function
 * @param string $class String of the class name not yet defined
 **/
function __autoload($class)
{
	try {
		
		$folders = split('_', $class);
		
		if (count($folders) > 1) array_pop($folders);
		
		$path = implode(DS, $folders);
		
		$class = Inflector::underscore($class);
		
		switch (true) {
		
			case (in_string('Controller', $class)):
				$type = 'Controller';
				$path = CONTROLLERS_PATH . $class . '.php';
				break;
			
			case (true):
				$type = 'Core Class';
				$path = CREOVEL_PATH . 'classes' . DS . $class.'.php';
				if (file_exists($path)) break;
			
			case (true):
				$type = 'Adapter';
				$path = CREOVEL_PATH . 'adapters' . DS . $class . '.php';
				if (file_exists($path)) break;
			
			case (true):
				$type = 'Service';
				$path = CREOVEL_PATH . 'services' . DS . $class . '.php';
				if (file_exists($path)) break;
			
			case (true):
				$type = 'Vendor';
				$path = VENDOR_PATH . $class . DS . $class . '.php';
				if (file_exists($path)) break;
			
			case (true):
				$type = in_string('Mailer', $class) ? 'Mailer' : 'Model';
				$path = MODELS_PATH . $class . '.php';
				// if model found locally
				if (file_exists($path)) {
					break;
				} else  {
					// check shared
					$shared_path = SHARED_PATH . 'Models' . DS . $class . '.php';
					if (file_exists($shared_path)) {
						$path = $shared_path;
						break;
					}
				}
		}
		
		if (file_exists($path)) {
			require_once $path;
		} else {
			$file = $class;
			if ($type == 'Controller') CREO('error_code', 404);
			if ($type == 'Controller' || $type == 'Model' || $type == 'Mailer') {
				$class = Inflector::classify($class);
			}
			throw new Exception("{$class} not found in <strong>{$path}</strong>");
		}
	} catch (Exception $e) {
		CREO('application_error', $e);
	}
}

/**
 * Set and get $GLOBALS['CREOVEL'] variables.
 *
 * @global function
 * @param string $key
 * @param mixed $val
 * @return mixed
 **/
function CREO($key = null, $val = null)
{
	if (!$key) return $GLOBALS['CREOVEL'];
	
	// uppercase all keys
	$key = strtoupper($key);
	
	// get or set values
	switch (true) {
		case ($key == 'APPLICATION_ERROR'):
			$GLOBALS['CREOVEL']['ERROR']->add($val);
			break;
		
		case ($key == 'DATABASE'):
			$mode = strtoupper($val['mode']);
			$GLOBALS['CREOVEL']['DATABASES'][$mode] = array(
				'adapter'	=> $val['adapter'],
				'host' 		=> $val['host'],
				'username'	=> $val['username'],
				'password'	=> $val['password'],
				'default'	=> $val['default']
				);
			if (isset($val['port'])) {
				$GLOBALS['CREOVEL']['DATABASES'][$mode] +=
					array('port' => $val['port']);
			}
			if (isset($val['socket'])) {
				$GLOBALS['CREOVEL']['DATABASES'][$mode] +=
					array('socket' => $val['socket']);
			}
			break;
			
		case ($val !== null):
			return $GLOBALS['CREOVEL'][$key] = $val;
			break;
			
		default:
			return $GLOBALS['CREOVEL'][$key];
			break;
	}
}

/**
 * Sets and unsets $_SESSION['flash']. Used by application notices.
 *
 * @global function
 * @param string $message Optional string to be displayed.
 * @param string $type - Type of notice passed
 * @return bool/string String or message 
 **/
function flash_message($message = null, $type = 'notice')
{
	if ($message || isset($_SESSION['flash']['message'])) {
		
		if ( $message ) {
		
			$_SESSION['flash']['message'] = $message;
			$_SESSION['flash']['type'] = $type;
			$_SESSION['flash']['checked'] = 'no';
		
		} elseif ( $_SESSION['flash']['checked'] == 'no' ) {
		
			$_SESSION['flash']['checked'] = 'yes';
			return true;
		
		} else {
		
			$message = $_SESSION['flash']['message'];
			unset($_SESSION['flash']);
			return $message;
			
		}
		
	} else {
		
		return false;
	
	}
}

/*

	Function: flash_type
	
	Returns the $_SESSION['flash']['type'].
	
	Returns:
	
		String.

*/

function flash_type()
{
	return $_SESSION['flash']['type'] ? $_SESSION['flash']['type'] : 'notice';
}

/*

	Function: flash_warning
	
	Alias for flash_message($message, 'notice').
	
	Parameters:
	
		message - Message for flash
	
	Returns:
	
		String or boolean.

*/

function flash_notice($message = null)
{
	return flash_message($message, 'notice');
}

/*


	Function: flash_error
	
	Alias for flash_message($message, 'error').
	
	Parameters:
	
		message - Message for flash
	
	Returns:
	
		String or boolean.

*/

function flash_error($message = null)
{
	return flash_message($message, 'error');
}

/*


	Function: flash_warning
	
	Alias for flash_message($message, 'warning').
	
	Parameters:
	
		message - Message for flash
	
	Returns:
	
		String or boolean.

*/

function flash_warning($message = null)
{
	return flash_message($message, 'warning');
}

/*

	Function: flash_warning
	
	Alias for flash_success($message, 'success')
	
	Parameters:
	
	message - Message for flash.
	
	Returns:
	
		String or boolean.

*/

function flash_success($message = null)
{
	return flash_message($message, 'success');
}

/*

	Function: application_error
	
	Stops the application and display an error message.
	
	Parameters:
	
		message - Error message.
		thow_exception - Optional bool. If set to true displays additional debugging info on error.

*/

function application_error($message, $thow_exception = false)
{
	if ($thow_exception) { 
		$e = new Exception($message);
	}
	$_ENV['error']->add($message, $e);
}

/*

	Function: get_creovel_adapters
	
	Returns an array of the adapters available to the framework.
	
	Returns:
	
	Array adapters.

*/	

function get_creovel_adapters()
{
	return get_files_from_dir(CREOVEL_PATH.'adapters');
}

/*

	Function: get_creovel_services
	
	Returns an array of the services available to the framework.
	
	Returns:
	
		Array of services.

*/	

function get_creovel_services()
{
	return get_files_from_dir(CREOVEL_PATH.'services');
}

/*
	Function: url_for
	
	Creates a url path for lazy programmers.

	(start code)
 		url_for('user', 'edit', 1234)
	(end)
	
	Parameters:
	
		controller - required
		action - required
		id - optional
		https - optional
		
	Returns:
	
		String.
*/

function url_for()
{
	$args = func_get_args();

	if (is_array($args[0])) {
		
		// Set Contoller
		$controller = $args[0]['controller'];
		unset($args[0]['controller']);
		
		// set action
		$action = $args[0]['action'] . (CREO('html_append') ? '.html' : '');
		unset($args[0]['action']);
		
		// set id
		$id = $args[0]['id'];
		unset($args[0]['id']);
		
		// secure mode
		$https = $args[0]['https'];
		unset($args[0]['https']);
		
		// set misc
		$misc = urlencode_array($args[0]);
		
	} else {
		
		// set controller
		$controller = $args[0];
		
		// set action
		$action = $args[1] . (CREO('html_append') ? '.html' : '');
		
		// set id and misc
		if (is_array($args[2])) {
			$id = @$args[2]['id'];
			unset($args[2]['id']);
			$misc = urlencode_array($args[2]);
		} else {
			$id = $args[2];
		}
		
		// secure mode
		$https = @$args[3];
	}

	if (@is_array($_ENV['secure_controllers']) && in_array($controller, $_ENV['secure_controllers'])) {
		$https = true;
	}
	// build url
	$uri = '/'.(!$controller && $action ? get_controller() : $controller).($action ? "/{$action}" : '');
	
	if (@$misc) {
		$uri .= "?" . ($id ? "id={$id}&" : '') . $misc;
	} else if ($id) {
		$uri .= "/{$id}";
	}
	
	return ($https ? str_replace('http://', 'https://', BASE_URL) : '').$uri;
}

/*
	Function: redirecto_to
	
	Redirects the page. *Note should only be used inside controllers.*
	
	Parameters:
	
		controller - controller
		action - action
		id - id
*/

function redirect_to($controller = '', $action = '', $id = '')
{
	redirect_to_url(url_for($controller, $action, $id));
}

/*
	Function: redirect_to_url
	
	Header redirect.
	
	Parameters:
	
	url - String
*/

function redirect_to_url($url)
{
	header('location: ' . $url);
	die;
}

function view_source_url($file)
{
	return $_SERVER['REQUEST_URI'] . (strstr($_SERVER['REQUEST_URI'], '?') ? '&' : '?' ) . 'view_source=' . $file;
}