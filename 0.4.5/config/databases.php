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
    'adapter'   => 'Mysql',
    'host'      => 'mysqltest',
    'username'  => 'webuser',
    'password'  => 'webpass',
    'database'  => 'cmsdb'
    ));

CREO('database', array(
    'mode'      => 'development1',
    'adapter'   => 'IbmDb2',
    'host'      => 'localhost',
    'username'  => 'webuser',
    'password'  => 'webpass',
    'database'  => 'cmsdb',
    'schema'    => 'cmsdb',
    ));

/**
 * Test settings.
 */
CREO('database', array(
    'mode'      => 'test',
    'adapter'   => 'Mysql',
    'host'      => 'localhost',
    'username'  => '',
    'password'  => '',
    'database'  => 'creovel_test'
    ));

/**
 * Production settings.
 */
CREO('database', array(
    'mode'      => 'production',
    'adapter'   => 'Mysql',
    'host'      => 'localhost',
    'username'  => '',
    'password'  => '',
    'database'  => 'creovel_production'
    ));