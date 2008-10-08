<?php
/**
 * Set environment variables.
 *
 * @package Creovel
 * @subpackage Creovel.Config
 * @copyright  2008 Creovel, creovel.org
 * @license    http://creovel.googlecode.com/svn/trunk/License   MIT License
 * @version    $Id:$
 * @since      File available since Release 0.1.0
 */

/**
 * Set application mode: 'development', 'test' or 'production'.
 */
CREO('mode', 'development');

/**
 * Email application errors when not in development mode (set to enable). Use
 * commas for multiple email addresses.
 */
CREO('server_admin', 'youremail@yourdomain.com');

/**
 * Set session handler: false, true, or 'table'.
 */
CREO('session', true);

/**
 * Show source in debugger for all files.
 */
CREO('show_source', true);
