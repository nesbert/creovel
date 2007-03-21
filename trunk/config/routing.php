<?php

/*

	Scripts: routing
	
	Define application server paths and URL paths.
	
	See Also:
	
		<link to paths documentation>

*/

$_ENV['routing']->add_route(new route(array( 'name' => 'default', 'prototype' => ':controller/:action/:id' )));
$_ENV['routing']->add_route(new route(array( 'name' => 'error', 'prototype' => 'index/error' )));


//print_obj($_ENV['routing'], 1);
?>