<?php
/**
 * Set database connection settings.
 *
 * @package     Application
 * @subpackage  Config
 **/

/**
 * Development settings.
 */
CREO('database', array(
    'mode'      => 'development',
    'adapter'   => 'mysql',
    'host'      => 'localhost',
    'username'  => '',
    'password'  => '',
    'default'   => 'creovel_development'
    ));

/**
 * Test settings.
 */
CREO('database', array(
    'mode'      => 'test',
    'adapter'   => 'mysql',
    'host'      => 'localhost',
    'username'  => '',
    'password'  => '',
    'default'   => 'creovel_test'
    ));

/**
 * Production settings.
 */
CREO('database', array(
    'mode'      => 'production',
    'adapter'   => 'mysql',
    'host'      => 'localhost',
    'username'  => '',
    'password'  => '',
    'default'   => 'creovel_production'
    ));