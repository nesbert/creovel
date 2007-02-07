<?php
/**
 * Copyright (c) 2005-2006, creovel.org
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated 
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software,
 * and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions
 * of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED
 * TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * Licensed under The MIT License. Redistributions of files must retain the above copyright notice.
 */

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
	
		case ( strstr($class, '_controller') ):
			$type = 'Controller';
			$path = CONTROLLERS_PATH.$class.'.php';
		break;
			
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
			
		case ( true ):
			$type = strstr($class, '_mailer') ? 'Mailer' : 'Model';
			$path = MODELS_PATH.$class.'.php';
			// if model found locallly
			if ( file_exists($path) ) {
				break;
			} else {
				// check shared
				$shared_path = SHARED_PATH.'models'.DS.$class.'.php';
				if ( file_exists($shared_path) ) {
					$path = $shared_path;
					break;
				}
			}
			
	}

	try {
	
        switch ( true )
        {
        
            case ( file_exists($path) ):
                require_once $path;
            break;
            
            // create virtual class for models
            case ( model::table_exits( pluralize($class) ) ):
                eval('class ' . singularize($class) . ' extends model { private $_is_virtual = true; }');
            break;
            
            default:
                if ( $type == 'Model' ) $class = singularize($class);
                throw new Exception("{$type} '{$class}' not found in <strong>{$path}</strong>");
            break;
            
        }
	
	} catch (Exception $e) {
		
		// add to errors
		$_ENV['error']->add($e->getMessage(), $e);
		
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

/*
 * Sets and unsets $_SESSION['notice'].
 *
 * @author Nesbert Hidalgo
 * @param string $message optional
 */
function flash_notice($message = null) {

	if ( $message || $_SESSION['notice']['message'] ) {

		if ( $message ) {
		
			$_SESSION['notice']['message'] = $message;
			$_SESSION['notice']['checked'] = 'no';
		
		} elseif ( $_SESSION['notice']['checked'] == 'no' ) {
		
			$_SESSION['notice']['checked'] = 'yes';	
			return true;
		
		} else {
		
			$message = $_SESSION['notice']['message'];
			unset($_SESSION['notice']);
			return $message;
			
		}
		
	} else {

		return false;
	
	}

}

/**
 * Stops the application and display an error message
 *
 * @author Nesbert Hidalgo
 * @access public
 * @param string $message
 * @param bool $thow_exception optional
 */
function application_error($message, $thow_exception = false)
{
	if ($thow_exception) { 
		$e = new Exception($message);
	}
	$_ENV['error']->add($message, $e);
}

/**
 * Gets a directories files in a directory by file type. Returns an
 * associative array with the file_name as key and file_path as value.
 *
 * @author Nesbert Hidalgo
 * @access public
 * @param string $dir_path required
 * @param string $file_type optional default set to 'php'
 * @return object
 */
function get_files_from_dir($dir_path, $file_type = 'php')
{
	$files = array();
	if ( $handle = opendir($dir_path) ) {
	   while ( false !== ($file = readdir($handle)) ) {
		   if ( strstr($file, '.'.$file_type) ) {
			   $files[substr($file, 0, -4)] = $dir_path.DS.$file;
		   }
	   }
	   closedir($handle);
	}
	return $files;
}

/**
 * Returns an array of the adapters available to the framework.
 *
 * @author Nesbert Hidalgo
 * @access public 
 * @return array
 */	
function get_creovel_adapters()
{
	return get_files_from_dir(CREOVEL_PATH.'adapters');
}

/**
 * Returns an array of the services available to the framework.
 *
 * @author Nesbert Hidalgo
 * @access public 
 * @return array
 */	
function get_creovel_services()
{
	return get_files_from_dir(CREOVEL_PATH.'services');
}

?>