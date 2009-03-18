<?php

/*

	Scripts: routes
	
	Define application server paths and URL paths.
	
	See Also:
	
		<link to paths documentation>

*/

// Sample routes.
#mapper::connect(':controller/:action/:id', array( 'controller' => 'blog' ));
#mapper::connect('date/:year/:month/:day/:title', array( 'controller' => 'blog', 'action' => 'by_date', ':month' => '03', 'requirements' => array( ':year' => '/\d{4}/', ':day' => '/\d{1,2}/', ':month' => '/\d{1,2}/' ) ));
#mapper::connect('posts/:category', array( ':controller' => 'blog', ':action' => 'posts', ':category' => 'all' ));
#mapper::connect('users/:id/:action', array( 'controller' => 'users', 'action' => 'profile' ));

?>