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

// include PHPUnit
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Extensions/OutputTestCase.php';