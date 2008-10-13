<?php
/**
 * Set database connection settings.
 *
 * @package Creovel
 * @subpackage Creovel.Config
 * @copyright  2008 Creovel, creovel.org
 * @license    http://creovel.googlecode.com/svn/trunk/License   MIT License
 * @version    $Id:$
 * @since      File available since Release 0.1.0
 */

/**
 * Development settings.
 */
CREO('database', array(
	'mode'		=> 'development',
	'adapter'	=> 'mysql_improved',
	'host'		=> 'localhost',
	'username'	=> '',
	'password'	=> '',
	'default'	=> 'creovel_development'
	));

/**
 * Test settings.
 */
CREO('database', array(
	'mode'		=> 'test',
	'adapter'	=> 'mysql',
	'host'		=> 'localhost',
	'username'	=> '',
	'password'	=> '',
	'default'	=> 'creovel_test'
	));

/**
 * Production settings.
 */
CREO('database', array(
	'mode'		=> 'production',
	'adapter'	=> 'mysql',
	'host'		=> 'localhost',
	'username'	=> '',
	'password'	=> '',
	'default'	=> 'creovel_production'
	));