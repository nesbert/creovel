<?

$_ENV['routing'] = new routing();
$_ENV['routing']->add_route(new route(array( 'prototype' => ':controller/:action/:id' )));

?>
