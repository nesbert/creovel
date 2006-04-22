<?php
/*
 * Framework functions.
 */

/**
 * AUTOLOAD ROUTINE
 *
 * @author Nesbert Hidalgo
 * @access public
 */
function __autoload($class)
{

	$folders = split('_', $class);
	
	if ( count($folders) > 1 ) array_pop($folders);

	$path = implode(DS, $folders);
	
	switch ( true ) {
	
		case ( true ):
			$type = strstr($class, '_mailer') ? 'Mailer' : 'Model';
			$path = MODELS_PATH.$class.'.php';
			if ( file_exists($path) ) break;
			
		case ( true ):
			$type = 'Interface';
			$path = CREOVEL_PATH.'interfaces'.DS.$class.'.php';
			if ( file_exists($path) ) break;
			
		case ( true ):
			$type = 'Adapter';
			$path = CREOVEL_PATH.'adapters'.DS.$class.'.php';
			if ( file_exists($path) ) break;
			
		case ( true ):
			$type = 'Service';
			$path = CREOVEL_PATH.'services'.DS.$class.'.php';
			if ( file_exists($path) ) break;
			
		case ( true ):
			$type = 'Vendor';
			$path = VENDOR_PATH.$class.DS.$class.'.php';
			if ( file_exists($path) ) break;
			
	}

	try {
	
		if ( file_exists($path) ) {			
			require_once($path);
		} else {			
			throw new Exception("{$type} '{$class}' not found in <strong>".str_replace($classname.'.php', '', $path)."</strong>");
		}
	
	} catch(Exception $e) {
		
		// add to errors				
		$_ENV['error']->add('fatal', $e->getMessage(), $e);
		
	}
	
}

/**
 * Returns the framework events (CONTORLLER & ACTION).
 *
 * @author Nesbert Hidalgo
 * @access public
 * @param string $event_to_return optional name of event to return
 * @return array
 */ 
function get_events($event_to_return = null)
{	
	return creovel::get_events($event_to_return); 
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
	return creovel::get_events('controller');
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
	return creovel::get_events('action');
}

/**
 * Returns the framework params.
 *
 * @author Nesbert Hidalgo
 * @access public
 * @param string $param_to_return optional name of param to return
 * @return array
 */
function get_params($param_to_return = null)
{
	return creovel::get_params($param_to_return);
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
	return creovel::VERSION;
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
	return creovel::RELEASE_DATE;
}
?>