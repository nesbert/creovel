<?php
/**
 * Set environment variables.
 *
 * @package     Application
 * @subpackage  Config
 **/

/**
 * Set application mode: 'development', 'test' or 'production'.
 */
CREO('mode', 'development');

/**
 * Set session handler: false, true, or 'table'.
 */
CREO('session', false);

/**
 * Show source in debugger for all files.
 */
CREO('show_source', false);

/**
 * Set error reporting level.
 */
error_reporting(CREO('mode') == 'development' ? E_ALL : 0);
