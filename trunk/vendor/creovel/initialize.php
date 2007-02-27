<?php
/*

	Scripts: initialize
	
	This file includes all core libraries and intializes framework.

*/

// Include base helpers library.
require_once 'constants/common.php';

// Include base helpers library.
require_once 'helpers/ajax.php';
require_once 'helpers/datetime.php';
require_once 'helpers/form.php';
require_once 'helpers/framework.php';
require_once 'helpers/general.php';
require_once 'helpers/html.php';
require_once 'helpers/text.php';
require_once 'helpers/server.php';
require_once 'helpers/validation.php';

// Include base classes library.
require_once 'classes/controller.php';
require_once 'classes/creovel.php';
require_once 'classes/error.php';
require_once 'classes/file.php';
require_once 'classes/inflector.php';
require_once 'classes/mailer.php';
require_once 'classes/model.php';
require_once 'classes/pager.php';
require_once 'classes/rss.php';
require_once 'classes/unittest.php';
require_once 'classes/validation.php';
require_once 'classes/view.php';
require_once 'classes/xml.php';

// Include environment specific file
require_once CONFIG_PATH . 'environments' . DS . ( $_ENV['mode'] = ( isset($_ENV['mode']) ? $_ENV['mode'] : 'development' ) ) . '.php';

// Set error object
$_ENV['error'] = new error('application');

// Session logic.
if ($_ENV['sessions'])
{
	if ( $_ENV['sessions'] === 'table' ) {	
		// include/create session db object
		require_once 'classes/session.php';
		$_session = new session();
		// include session helpers
		require_once 'helpers/session.php';
	}

	// Fix for PHP 5.05
	// http://us2.php.net/manual/en/function.session-set-save-handler.php#61223 
	register_shutdown_function('session_write_close');

	session_start();
}

// Set environtment for command line interfaces.
if ( $_SERVER['SCRIPT_NAME'] != '/dispatch.php' ) {
	$_ENV['command_line'] = true;
}
?>
