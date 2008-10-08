<?php
/**
 * Define application server paths and URL paths.
 *
 * @package Creovel
 * @subpackage Creovel.Config
 * @copyright  2008 Creovel, creovel.org
 * @license    http://creovel.googlecode.com/svn/trunk/License   MIT License
 * @version    $Id:$
 * @since      File available since Release 0.1.0
 */

/*
 * Define server paths.
 */
define('DS', DIRECTORY_SEPARATOR);
define('BASE_PATH', 		dirname(dirname(__FILE__)).DS);
define('CONFIG_PATH', 		BASE_PATH.'config'.DS);
define('PUBLIC_PATH', 		BASE_PATH.'public'.DS);
define('APP_PATH', 			BASE_PATH.'app'.DS);
define('MODELS_PATH', 		APP_PATH.'models'.DS);
define('VIEWS_PATH', 		APP_PATH.'views'.DS);
define('CONTROLLERS_PATH',	APP_PATH.'controllers'.DS);
define('HELPERS_PATH', 		APP_PATH.'helpers'.DS);
define('SCRIPT_PATH', 		BASE_PATH.'script'.DS);
define('VENDOR_PATH', 		BASE_PATH.'vendor'.DS);
define('SHARED_PATH', 		BASE_PATH.'shared'.DS); // use/modify when needed
define('CREOVEL_PATH', 		VENDOR_PATH.'Creovel'.DS);

/*
 * Define application URLs.
 */
define('BASE_URL',			'http' . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on" ? 's' : '') . '://'.$_SERVER['HTTP_HOST'] . '/');
define('CSS_URL',			'/css/');
define('JAVASCRIPT_URL',	'/js/');