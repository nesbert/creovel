<?php
/**
 * This file include base files need to start creovel.
 *
 * @package Creovel
 * @copyright  2008 Creovel, creovel.org
 * @license    http://creovel.googlecode.com/svn/trunk/License   MIT License
 * @version    $Id:$
 * @since      Class available since Release 0.1.0
 */

// Include application constant paths.
require_once '../config/paths.php';

// Initialize framework and include core libraries.
require_once CREOVEL_PATH . 'initialize.php';

// Include application config files
require_once CONFIG_PATH . 'environment.php';
require_once CONFIG_PATH . 'environment' . DS . CREO('mode') . '.php';

// Include application config files
require_once CONFIG_PATH.'databases.php';

// Set default routes
Mapper::connect();
Mapper::connect('default_error', array(
	'name' => 'default_error',
	'controller' => 'errors',
	'action' => 'general'
	));

// Include custom routes
require_once CONFIG_PATH.'routes.php';

// Go, go, go!
Creovel::run();