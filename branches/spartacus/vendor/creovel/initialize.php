<?php
// If not PHP 5 stop.
if (PHP_VERSION <= 5) {
	die('Creovel requires PHP 5!');
}

// Define creovel constants.
define('CREOVEL_VERSION', '1.xx');
define('CREOVEL_RELEASE_DATE', '2008-07-02 22:55:55');

// Define environment constants.
define('PHP', phpversion());

// Define time constants.
define('SECOND',  1);
define('MINUTE', 60 * SECOND);
define('HOUR',   60 * MINUTE);
define('DAY',    24 * HOUR);
define('WEEK',    7 * DAY);
define('MONTH',  30 * DAY);
define('YEAR',  365 * DAY);

// Include base helper libraries.
require_once 'helpers/datetime.php';
require_once 'helpers/form.php';
require_once 'helpers/framework.php';
require_once 'helpers/general.php';
require_once 'helpers/html.php';
require_once 'helpers/server.php';
require_once 'helpers/text.php';
require_once 'helpers/validation.php';

// Include minimum base classes.
require_once 'classes/dispatcher.php';
require_once 'classes/inflector.php';
require_once 'classes/action_controller.php';
require_once 'classes/action_view.php';
require_once 'classes/error_handler.php';
require_once 'classes/routing.php';

// Set default creovel global vars.
$GLOBALS['CREOVEL']['MODE'] = 'production';
$GLOBALS['CREOVEL']['SERVER_ADMIN'] = false;
$GLOBALS['CREOVEL']['SESSION'] = true;
$GLOBALS['CREOVEL']['SHOW_SOURCE'] = false;
$GLOBALS['CREOVEL']['EMAIL_ON_ERROR'] = false;
$GLOBALS['CREOVEL']['ERROR'] = new ErrorHandler;
$GLOBALS['CREOVEL']['PAGE_CONTENTS'] = '@@page_contents@@';
$GLOBALS['CREOVEL']['DEFAULT_CONTROLLER'] = 'index';
$GLOBALS['CREOVEL']['DEFAULT_ACTION'] = 'index';
$GLOBALS['CREOVEL']['DEFAULT_LAYOUT'] = 'default';
$GLOBALS['CREOVEL']['EMAIL_ON_ERROR'] = false;

// Set routing defaults
$GLOBALS['CREOVEL']['ROUTING'] = parse_url(url());
$GLOBALS['CREOVEL']['ROUTING']['current'] = array();
$GLOBALS['CREOVEL']['ROUTING']['routes'] = array();