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
 * Set flag to log errors in the log directory..
 */
CREO('log_errors', false);

/**
 * Set flag to log queries in the log directory..
 */
CREO('log_queries', false);

/**
 * Set session handler: false, true, or 'table'. This will set and
 * start session handling for application. This may also be done at
 * the controller level.
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

/**
 * Uncomment to load old top level functions.
 */
// LegacySupport::init();