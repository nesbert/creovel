<?php
/**
 * Set environment variables.
 *
 * @package     Creovel
 * @subpackage  Creovel.Config
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.1.0
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
#CREO('show_source', true);

/**
 * Set error reporting level.
 */
error_reporting(CREO('mode') == 'development' ? E_ALL ^ E_NOTICE : 0);
