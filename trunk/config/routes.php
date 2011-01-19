<?php
/**
 * Define custom routing here. See samples below.
 *
 * @package     Application
 * @subpackage  Config
 **/
 
/*
// Make the default controller "blog"
ActionRouter::map('default', '/:controller/:action/*', array(
            'controller' => 'blog')
            );
*/

/*
// http://www.test.com/date/2008/10/14/New+Macbooks!;
ActionRouter::map('blog', '/date/:year/:month/:day/:title/*', array(
            'controller' => 'blog',
            'action' => 'posts',
            'year' => date('Y'),
            'month' => date('m'),
            'day' => date('d')),
            array(
                ':year' => '/\d{4}/',
                ':month' => '/\d{1,2}/',
                ':day' => '/\d{1,2}/'
                )
            );
*/

// Nested controller example:
#
#	/app/controllers
#	    /admin_controler.php
#	    /admin/index_controller.php
#       /admin/user_controller.php
#
/*
ActionRouter::map(
			'nested_admin_controller', // route name
			'/admin/:controller/:action/*', // URI qualifier
			array(
            	'nested_controller' => 'admin' // folder path
            	) // options array
            );
*/