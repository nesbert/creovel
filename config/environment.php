<?php

/*

	Scripts: environment
	
	This is used set framework configuration settings.
	
	See Also:
	
		<link to environment documentation>

*/

// Set application mode.
$_ENV['mode'] = 'development'; // development, test, production

// Email application errors when not in development mode (set to enable).
$_ENV['email_errors'] = 'youremail@yourdomain.com'; // use commas for multiple email addresses

// Set session handler.
$_ENV['sessions'] = true; // false, true, 'table'

// show source in debugger for all files
$_ENV['view_source'] = false;

// Include database configration settings.
require_once 'database.php';

// Include application paths.
require_once 'paths.php';

// Initialize framework and include core libraries.
require_once CREOVEL_PATH.'initialize.php';

?>