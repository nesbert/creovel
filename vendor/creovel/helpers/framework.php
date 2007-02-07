<?

/*

Script: framework

*/

/*

Function: __autoload
	Autoload Routine

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

/*

Function: get_events
	Returns the framework events (CONTORLLER & ACTION).

Parameters:
	event_to_return - optional name of event to return

Return:
	array

*/ 

function get_events($event_to_return = null)
{	
	return creovel::get_events($event_to_return); 
}

/*

Function: get_controller
	Returns the current CONTORLLER.

Returns:
	string

*/

function get_controller()
{
	return creovel::get_events('controller');
}

/*

Function: get_action
	Returns the current ACTION.

Returns:
	string

*/

function get_action()
{
	return creovel::get_events('action');
}

/*

Function: get_params
	Returns the framework params.

Parameters:
	param_to_return - optional name of param to return
Returns:
	array

*/

function get_params($param_to_return = null)
{
	return creovel::get_params($param_to_return);
}


/*

Function:
	Returns the framework version.

Returns:
	string

*/

function get_version()
{
	return creovel::VERSION;
}

/*

Function: get_release_date
	Returns the framework release date.

Returns:
	string

*/

function get_release_date()
{
	return creovel::RELEASE_DATE;
}

/*

Function: flash_notice
	Sets and unsets $_SESSION['notice'].

Parameters:
	message - optional

Returns:
	string or bool

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

/*

Function: application_error
	Stops the application and display an error message

Parameters:
	message - error message
	thow_exception - optional

*/

function application_error($message, $thow_exception = false)
{
	if ($thow_exception) { 
		$e = new Exception($message);
	}
	$_ENV['error']->add($message, $e);
}

/*

Function: get_files_from_dir
	Gets a directories files in a directory by file type. Returns an associative array with the file_name as key and file_path as value.

Parameters:
	dir_path - required
	file_type - optional default set to 'php'

Returns:
	object

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

/*

Function: get_creovel_adapters
	Returns an array of the adapters available to the framework.

Returns:
	array

*/	

function get_creovel_adapters()
{
	return get_files_from_dir(CREOVEL_PATH.'adapters');
}

/*

Function: get_creovel_services
	Returns an array of the services available to the framework.

Returns:
	array

*/	

function get_creovel_services()
{
	return get_files_from_dir(CREOVEL_PATH.'services');
}

?>
