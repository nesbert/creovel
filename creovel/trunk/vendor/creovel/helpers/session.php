<?php
/*
 * Table session helpers
 */

function creovel_session_open($save_path, $session_name)
{
	return true;
}

function creovel_session_close()
{
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
?>