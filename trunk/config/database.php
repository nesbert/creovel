<?php

/*

	Scripts: database
	
	Set database connection properties for each environment mode.
	
	See Also:
	
		<link to database documentation>

*/

// Set development database properties.
$_ENV['development']['adapter']		= 'mysql';
$_ENV['development']['database']	= 'creovel_development';
$_ENV['development']['host']		= 'localhost';
$_ENV['development']['username']	= 'root';
$_ENV['development']['password']	= '';

// Set test database properties.
$_ENV['test']['adapter']			= 'mysql';
$_ENV['test']['database']			= 'creovel_test';
$_ENV['test']['host']				= 'localhost';
$_ENV['test']['username']			= 'root';
$_ENV['test']['password']			= '';

// Set production database properties.
$_ENV['production']['adapter']		= 'mysql';
$_ENV['production']['database']	= 'creovel_production';
$_ENV['production']['host']		= 'localhost';
$_ENV['production']['username']	= 'root';
$_ENV['production']['password']	= '';

?>