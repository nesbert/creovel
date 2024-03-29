<?php
/**
 * Sever application custom, server and URL paths.
 *
 * @package     Application
 * @subpackage  Config
 **/

/*
 * Define server paths.
 */
define('DS', DIRECTORY_SEPARATOR);
define('CONFIG_PATH',       BASE_PATH . 'config' . DS);
define('PUBLIC_PATH',       BASE_PATH . 'public' . DS);
define('APP_PATH',          BASE_PATH . 'app' . DS);
define('LOG_PATH',          BASE_PATH . 'log' . DS);
define('MODELS_PATH',       APP_PATH . 'models' . DS);
define('VIEWS_PATH',        APP_PATH . 'views' . DS);
define('CONTROLLERS_PATH',  APP_PATH . 'controllers' . DS);
define('HELPERS_PATH',      APP_PATH . 'helpers' . DS);
define('SCHEMAS_PATH',      APP_PATH . 'schemas' . DS);
define('SCRIPT_PATH',       BASE_PATH . 'script' . DS);
define('VENDOR_PATH',       BASE_PATH . 'vendor' . DS);
#define('SHARED_PATH',       BASE_PATH . 'shared' . DS); // use when needed
define('CREOVEL_PATH',      VENDOR_PATH . 'creovel' . DS);

/*
 * Define application URLs.
 */
define('CSS_URL',           '/css/');
define('JAVASCRIPT_URL',    '/js/');