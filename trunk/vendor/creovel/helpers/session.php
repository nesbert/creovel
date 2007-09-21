<?php
/*

Script: session
	Table session helpers. Overrides session handler data to a database.

*/

function creovel_session_open($save_path, $session_name)
{
	return true;
}

function creovel_session_close()
{
	creovel_session_gc(get_cfg_var("session.gc_maxlifetime")); // http://us.php.net/manual/en/function.session-set-save-handler.php#69763
	return true;
}

function creovel_session_read($id)
{
	global $_session;
	return $_session->get_session_data($id);
}

function creovel_session_write($id, $val)
{
	global $_session;
	return $_session->set_session_data($id, $val);
}

function creovel_session_destroy($id)
{
	global $_session;
	return $_session->destroy_session_data($id);
}

function creovel_session_gc($maxlifetime)
{
	global $_session;
	return $_session->clean_session_data($maxlifetime);
}

// register session functions
session_set_save_handler(
	'creovel_session_open',
	'creovel_session_close',
	'creovel_session_read',
	'creovel_session_write',
	'creovel_session_destroy',
	'creovel_session_gc'
	);
	
// Fix for PHP 5.05
// http://us2.php.net/manual/en/function.session-set-save-handler.php#61223
register_shutdown_function('session_write_close');

session_start();

?>