<?php
/*

	Scripts: environment
	
	This is used set framework configuration settings.
	
	See Also:
	
		<link to environment documentation>

*/

// set application mode
$_ENV['mode'] = 'development'; // development, test, production

// set development database properties
$_ENV['development']['adapter']		= 'mysql';
$_ENV['development']['host']		= 'localhost';
$_ENV['development']['database']	= 'database';
$_ENV['development']['username']	= 'username';
$_ENV['development']['password']	= 'password';

// set test database properties
$_ENV['test']['adapter']			= 'mysql';
$_ENV['test']['host']				= 'localhost';
$_ENV['test']['database']			= '';
$_ENV['test']['username']			= '';
$_ENV['test']['password']			= '';

// set production database properties
$_ENV['production']['adapter']		= 'mysql';
$_ENV['production']['host']			= 'localhost';
$_ENV['production']['database']		= '';
$_ENV['production']['username']		= '';
$_ENV['production']['password']		= '';

// set default routing: controller, action, layout
$_ENV['routes']['default']['controller']	= 'index';
$_ENV['routes']['default']['action'] 		= 'index';
$_ENV['routes']['default']['layout']		= 'default';

// set error routing: controller, action, layout
$_ENV['routes']['error']['controller'] 		= 'index';
$_ENV['routes']['error']['action'] 			= 'error';
$_ENV['routes']['error']['layout'] 			= 'default';

// email application errors when not in development mode
$_ENV['email_errors'] = 'youremail@yourdomain.com'; // use commas for multiple email addresses

// set session handler
$_ENV['sessions'] = false; // false, true, 'table'

// show source in debugger for all files
$_ENV['view_source'] = false;

// define application urls
define(BASE_URL, 			'http'.( $_SERVER['HTTPS'] == "on" ? 's' : '' ).'://'.$_SERVER['HTTP_HOST']);
define(CSS_URL,				BASE_URL.'/stylesheets/');
define(JAVASCRIPT_URL,		BASE_URL.'/javascripts/');

// define application paths
define(BASE_PATH, 			dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
define(CONFIG_PATH, 		BASE_PATH.'config'.DIRECTORY_SEPARATOR);
define(PUBLIC_PATH, 		BASE_PATH.'public'.DIRECTORY_SEPARATOR);
define(APP_PATH, 			BASE_PATH.'app'.DIRECTORY_SEPARATOR);
define(MODELS_PATH, 		APP_PATH.'models'.DIRECTORY_SEPARATOR);
define(VIEWS_PATH, 			APP_PATH.'views'.DIRECTORY_SEPARATOR);
define(CONTROLLERS_PATH, 	APP_PATH.'controllers'.DIRECTORY_SEPARATOR);
define(HELPERS_PATH, 		APP_PATH.'helpers'.DIRECTORY_SEPARATOR);
define(SCRIPT_PATH, 		BASE_PATH.'script'.DIRECTORY_SEPARATOR);
#define(SHARED_PATH, 		BASE_PATH.'shared'.DIRECTORY_SEPARATOR); // use/modify when needed
define(VENDOR_PATH, 		BASE_PATH.'vendor'.DIRECTORY_SEPARATOR);
define(CREOVEL_PATH, 		VENDOR_PATH.'creovel'.DIRECTORY_SEPARATOR);

// initialize framework and include core libraries
require_once(CREOVEL_PATH.'initialize.php');
?>