<?php
/**
 * Set constants, include helpers, configuration files & core classes,
 * amp default routes and set CREOVEL & environment variables.
 **/

// If not PHP 5 stop.
if (PHP_VERSION <= 5) {
    die('Creovel requires PHP >= 5!');
}

// Define creovel constants.
define('CREOVEL_VERSION', '0.4.5');
define('CREOVEL_RELEASE_DATE', '2010-0?-?? ??:??:??');

// Define environment constants.
define('PHP', PHP_VERSION);

// Define time constants.
define('SECOND',  1);
define('MINUTE', 60 * SECOND);
define('HOUR',   60 * MINUTE);
define('DAY',    24 * HOUR);
define('WEEK',    7 * DAY);
define('MONTH',  30 * DAY);
define('YEAR',  365 * DAY);

// Include base helper libraries.
require_once CREOVEL_PATH . 'helpers/datetime.php';
require_once CREOVEL_PATH . 'helpers/form.php';
require_once CREOVEL_PATH . 'helpers/framework.php';
require_once CREOVEL_PATH . 'helpers/general.php';
require_once CREOVEL_PATH . 'helpers/html.php';
require_once CREOVEL_PATH . 'helpers/locale.php';
require_once CREOVEL_PATH . 'helpers/server.php';
require_once CREOVEL_PATH . 'helpers/text.php';
require_once CREOVEL_PATH . 'helpers/validation.php';

// Be kind to existing __autoload routines
if (PHP <= '5.1.2') {
    function __autoload($class) { __autoload_creovel($class); }
} else {
    spl_autoload_register('__autoload_creovel');
}

// Include application_helper
if (file_exists($helper = HELPERS_PATH . 'application_helper.php')) {
    require_once $helper;
}

// Include minimum base classes.
require_once CREOVEL_PATH . 'classes/c_object.php';
require_once CREOVEL_PATH . 'modules/module_base.php';
require_once CREOVEL_PATH . 'modules/inflector.php';

// Set default creovel global vars.
$GLOBALS['CREOVEL']['MODE'] = 'production';
$GLOBALS['CREOVEL']['PAGE_CONTENTS'] = '@@page_contents@@';
$GLOBALS['CREOVEL']['SESSION'] = true;
$GLOBALS['CREOVEL']['VIEW_EXTENSION'] = 'html';
$GLOBALS['CREOVEL']['VIEW_EXTENSION_APPEND'] = false;
$GLOBALS['CREOVEL']['DEFAULT_CONTROLLER'] = 'index';
$GLOBALS['CREOVEL']['DEFAULT_ACTION'] = 'index';
$GLOBALS['CREOVEL']['DEFAULT_LAYOUT'] = 'default';
$GLOBALS['CREOVEL']['SHOW_SOURCE'] = false;

// set error handler
require_once CREOVEL_PATH . 'classes/action_error_handler.php';
$GLOBALS['CREOVEL']['ERROR'] = new ActionErrorHandler;
$GLOBALS['CREOVEL']['APPLICATION_ERROR_CODE'] = '';
$GLOBALS['CREOVEL']['VALIDATION_ERRORS'] = array();