<?php
/*

Script: initialize
	This file includes all core libraries and intializes framework.

*/
 
// Include base helpers library.
require_once 'constants/common.php';

// Include base helpers library.
require_once 'helpers/ajax.php';
require_once 'helpers/constants.php';
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
	
	session_start();
}

?>