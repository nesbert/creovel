<?php
// define application paths
define(BASE_PATH, 			dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
define(APP_PATH, 			BASE_PATH.'app/');
define(CONFIG_PATH, 		BASE_PATH.'config/');
define(PUBLIC_PATH, 		BASE_PATH.'public/');
define(CONTROLLERS_PATH, 	APP_PATH.'controllers/');
define(HELPERS_PATH, 		APP_PATH.'helpers/');
define(MODELS_PATH, 		APP_PATH.'models/');
define(VIEWS_PATH, 			APP_PATH.'views/');
define(VENDOR_PATH, 		BASE_PATH.'vendor/');
define(CREOVEL_PATH, 		VENDOR_PATH.'creovel/');

// include core libraries
require_once(CREOVEL_PATH.'helpers/all.php');

// Default Route Controller
$_ENV['routes']['default']['controller']	= 'index';

// set development database properties
$_ENV[development][adapter]		= 'mysql';
$_ENV[development][host]		= 'localhost';
$_ENV[development][database]	= 'database';
$_ENV[development][username]	= 'user';
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

session_start();
?>
