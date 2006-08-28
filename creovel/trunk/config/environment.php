<?php
// set application mode
$_ENV['mode'] = 'development'; // development, test, production

// set development database properties
$_ENV[development][adapter]		= 'mysql';
$_ENV[development][host]		= 'localhost';
$_ENV[development][database]	= 'database';
$_ENV[development][username]	= 'username';
$_ENV[development][password]	= 'password';

// set test database properties
$_ENV[test][adapter]			= 'mysql';
$_ENV[test][host]				= 'localhost';
$_ENV[test][database]			= '';
$_ENV[test][username]			= '';
$_ENV[test][password]			= '';

// set production database properties
$_ENV[production][adapter]		= 'mysql';
$_ENV[production][host]			= 'localhost';
$_ENV[production][database]		= '';
$_ENV[production][username]		= '';
$_ENV[production][password]		= '';

// set session handler
$_ENV['sessions'] = false; // false, true, 'table'

// default routing: controller, action, layout
$_ENV['routes']['default']['controller'] = 'index';
$_ENV['routes']['default']['action'] = 'index';
$_ENV['routes']['default']['layout'] = 'default';

// define application paths
define(BASE_PATH, 			dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
define(CONFIG_PATH, 		BASE_PATH.'config/');
define(PUBLIC_PATH, 		BASE_PATH.'public/');
define(APP_PATH, 			BASE_PATH.'app/');
define(MODELS_PATH, 		APP_PATH.'models/');
define(VIEWS_PATH, 			APP_PATH.'views/');
define(CONTROLLERS_PATH, 	APP_PATH.'controllers/');
define(HELPERS_PATH, 		APP_PATH.'helpers/');
define(SCRIPTS_PATH, 		BASE_PATH.'scripts/');
define(VENDOR_PATH, 		BASE_PATH.'vendor/');
define(CREOVEL_PATH, 		VENDOR_PATH.'creovel/');

// include core libraries
require_once(CREOVEL_PATH.'lib.php');
?>