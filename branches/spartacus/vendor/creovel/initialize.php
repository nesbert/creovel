<?php
/**
 * This file includes base files need to initialize Creovel.
 *
 * @package     Creovel
 * @subpackage  Misc
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.1.0
 **/

// If not PHP 5 stop.
if (PHP_VERSION <= 5) {
	die('Creovel requires PHP >= 5!');
}

// Define creovel constants.
define('CREOVEL_VERSION', 'sparatacus');
define('CREOVEL_RELEASE_DATE', '2008-07-02 22:55:55');

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
require_once 'helpers/datetime.php';
require_once 'helpers/form.php';
require_once 'helpers/framework.php';
require_once 'helpers/general.php';
require_once 'helpers/html.php';
require_once 'helpers/locale.php';
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
$GLOBALS['CREOVEL']['DEFAULT_CONTROLLER'] = 'index';
$GLOBALS['CREOVEL']['DEFAULT_ACTION'] = 'index';
$GLOBALS['CREOVEL']['DEFAULT_LAYOUT'] = 'default';
$GLOBALS['CREOVEL']['ERROR'] = new ErrorHandler;
$GLOBALS['CREOVEL']['HTML_APPEND'] = false;
$GLOBALS['CREOVEL']['MODE'] = 'production';
$GLOBALS['CREOVEL']['PAGE_CONTENTS'] = '@@page_contents@@';
$GLOBALS['CREOVEL']['SESSION'] = true;
$GLOBALS['CREOVEL']['SHOW_SOURCE'] = false;

// Set routing defaults
$GLOBALS['CREOVEL']['ROUTING'] = parse_url(url());
$GLOBALS['CREOVEL']['ROUTING']['current'] = array();
$GLOBALS['CREOVEL']['ROUTING']['routes'] = array();

// Include application config files
require_once CONFIG_PATH . 'environment.php';
require_once CONFIG_PATH . 'environment' . DS . CREO('mode') . '.php';

// Include application config files
require_once CONFIG_PATH.'databases.php';

// Set session handler
if ($GLOBALS['CREOVEL']['SESSION']) {
	session_start();
}

// Set default route
Routing::map('default', '/:controller/:action/*', array(
            'controller' => 'index',
            'action' => 'index'
            ));

// Set default error route
Routing::map('default_error', '/errors/:action/*', array(
            'controller' => 'errors',
            'action' => 'general'
            ));

// Include custom routes
require_once CONFIG_PATH.'routes.php';

// Include application_helper
if (file_exists($helper = HELPERS_PATH . 'application_helper.php')) {
    require_once $helper;
} 
