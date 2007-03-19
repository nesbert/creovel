<?php

/*

	Scripts: paths
	
	Define application server paths and URL paths.
	
	See Also:
	
		<link to paths documentation>

*/

// Define application URLs.
define('BASE_URL', 			'http'.( $_SERVER['HTTPS'] == "on" ? 's' : '' ).'://'.$_SERVER['HTTP_HOST']);
define('CSS_URL',				BASE_URL.'/stylesheets/');
define('JAVASCRIPT_URL',		BASE_URL.'/javascripts/');

// Define server paths.
define('BASE_PATH', 			dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
define('CONFIG_PATH', 		BASE_PATH.'config'.DIRECTORY_SEPARATOR);
define('PUBLIC_PATH', 		BASE_PATH.'public'.DIRECTORY_SEPARATOR);
define('APP_PATH', 			BASE_PATH.'app'.DIRECTORY_SEPARATOR);
define('MODELS_PATH', 		APP_PATH.'models'.DIRECTORY_SEPARATOR);
define('VIEWS_PATH', 			APP_PATH.'views'.DIRECTORY_SEPARATOR);
define('CONTROLLERS_PATH', 	APP_PATH.'controllers'.DIRECTORY_SEPARATOR);
define('HELPERS_PATH', 		APP_PATH.'helpers'.DIRECTORY_SEPARATOR);
define('SCRIPT_PATH', 		BASE_PATH.'script'.DIRECTORY_SEPARATOR);
#define('SHARED_PATH', 		BASE_PATH.'shared'.DIRECTORY_SEPARATOR); // use/modify when needed
define('VENDOR_PATH', 		BASE_PATH.'vendor'.DIRECTORY_SEPARATOR);
define('CREOVEL_PATH', 		VENDOR_PATH.'creovel'.DIRECTORY_SEPARATOR);

?>