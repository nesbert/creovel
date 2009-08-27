<?php
/**
 * This file includes framework paths and starts Creovel.
 *
 * @package     Creovel
 * @subpackage  Misc
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.1.0
 **/

// Include application constant paths.
define('BASE_PATH', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
require_once BASE_PATH . 'config' . DIRECTORY_SEPARATOR . 'paths.php';

// Initialize framework and include core libraries.
require_once CREOVEL_PATH . 'classes' . DIRECTORY_SEPARATOR . 'creovel.php';
Creovel::run();