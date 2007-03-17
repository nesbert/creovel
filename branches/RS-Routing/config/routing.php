<?php

/*

	Script: routing

*/

$_ENV['routing'] = new routing();
$_ENV['routing']->add_route(new route(array( 'name' => 'default', 'prototype' => ':controller/:action/:id' )));
$_ENV['routing']->add_route(new route(array( 'name' => 'error', 'prototype' => 'index/error' )));

?>
