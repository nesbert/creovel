<?php
/**
 * This file includes framework paths and starts Creovel for CLI apps.
 *
 * @access      private
 * @package     Creovel
 * @subpackage  UnitTest
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.5
 **/
 
// Include application constant paths.
define('BASE_PATH',
    dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR);
require_once BASE_PATH . 'config/paths.php';

// Initialize framework and include core libraries.
require_once CREOVEL_PATH . 'classes' . DS . 'creovel.php';

// Run framework.
Creovel::main();

// Include PHPUnit.
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Extensions/OutputTestCase.php';

// Testing class for storage or common functions.
class TestSetting {
    
    public static $mysql = array(
        'mode'      => 'mysql',
        'adapter'   => 'Mysql',
        'host'      => 'mysqltest',
        'username'  => 'phpunit',
        'password'  => 'phpunit',
        'database'  => 'test',
        );
    
    public static $mysqli = array(
        'mode'      => 'mysql2',
        'adapter'   => 'MysqlImproved',
        'host'      => 'mysqltest',
        'username'  => 'phpunit',
        'password'  => 'phpunit',
        'database'  => 'test',
    );

    public static $db2 = array(
        'mode'      => 'db2',
        'adapter'   => 'IbmDb2',
        'host'      => 'localhost',
        'username'  => 'db2inst1',
        'password'  => 'password',
        'database'  => 'TEST', // enter database
        'schema'    => 'TEST', // enter schema
        );

}