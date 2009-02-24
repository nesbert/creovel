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
require_once '../config/paths.php';

// Initialize framework and include core libraries.
require_once CREOVEL_PATH . 'classes' . DS . 'creovel.php';
Creovel::run();