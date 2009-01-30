<?php
/**
 * This file include base files need to start Creovel.
 *
 * @package     Creovel
 * @subpackage  Creovel.Helpers
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.1.0
 **/

// Include application constant paths.
require_once '../config/paths.php';

// Initialize framework and include core libraries.
require_once CREOVEL_PATH . 'initialize.php';

// Go, go, go!
Dispatcher::run();