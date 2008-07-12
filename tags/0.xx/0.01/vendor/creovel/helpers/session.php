<?php
/**
 * Copyright (c) 2005-2006, creovel.org
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated 
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software,
 * and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions
 * of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED
 * TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * Licensed under The MIT License. Redistributions of files must retain the above copyright notice.
 */

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