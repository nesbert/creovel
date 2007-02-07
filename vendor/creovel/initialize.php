<?php
/**
 * Copyright (c) 2005-2006, creovel.org
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated 
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software,
 * and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions
 * of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED
 * TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * Licensed under The MIT License. Redistributions of files must retain the above copyright notice.
 */
 
/**
 * This file includes all core libraries and intializes framework.
 *
 * @copyright	Copyright (c) 2005-2006, creovel.org
 * @package		creovel
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 */
 
/*
 * Include base helpers library.
 */
require_once 'helpers/ajax.php';
require_once 'helpers/constants.php';
require_once 'helpers/datetime.php';
require_once 'helpers/form.php';
require_once 'helpers/framework.php';
require_once 'helpers/general.php';
require_once 'helpers/html.php';
require_once 'helpers/text.php';
require_once 'helpers/server.php';
require_once 'helpers/validation.php';

/*
 * Include base classes library.
 */
require_once 'classes/controller.php';
require_once 'classes/creovel.php';
require_once 'classes/error.php';
require_once 'classes/file.php';
require_once 'classes/inflector.php';
require_once 'classes/mailer.php';
require_once 'classes/model.php';
require_once 'classes/pager.php';
require_once 'classes/rss.php';
require_once 'classes/unittest.php';
require_once 'classes/validation.php';
require_once 'classes/view.php';
require_once 'classes/xml.php';

/*
 * Set error object
 */
$_ENV['error'] = new error('application');

/*
 * Session logic.
 */
if ( $_ENV['sessions'] ) {

	if ( $_ENV['sessions'] === 'table' ) {	
		// include/create session db object
		require_once 'classes/session.php';
		$_session = new session();
		// include session helpers
		require_once 'helpers/session.php';
	}
	
	session_start();

}
?>
