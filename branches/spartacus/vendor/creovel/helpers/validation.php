<?php
/*
	Script: validation
*/

/*
	Function: is_hostname
	
	Check if $var is a valid host name. Hostnames must use a-z,0-9, and '-'. A hostname
	cannot have any spaces nor can it start with a '-'.

	Parameters:
	
		var - value to validate
	
	Returns:
	
		Boolean.
*/

function is_hostname($var)
{
	return eregi('^[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$', $var) && $var{0} != '-' ? true : false;
}

/*
	Function: is_email
	
	Finds whether a variable is a valid email address.

	Parameters:
	
		var - value to validate
	
	Returns:
	
		Boolean.

*/

function is_email($var)
{
	$var = @explode('@', $var);
	return count($var) == 2 && eregi('^[_a-z0-9-]+(\.[_a-z0-9-]+)*$', $var[0]) && is_hostname($var[1]) ? true : false;
}

/*
	Function: is_email
	
	Finds whether a variable is a valid email address.

	Parameters:
	
		var - value to validate
	
	Returns:
	
		Boolean.

*/

function is_url($var)
{
	$var = @parse_url($var);
	return eregi('^(http|https|ftp)$', $var[scheme]) && is_hostname($var[host]) ? true : false;
}

/*

Function: is_alpha
	Finds whether a variable only contains characters A-Z or a-z

Parameters:
	var - value to validate

Returns:
	bool

*/

function is_alpha($var)
{
	return preg_match('/^[a-z]+$/i', $var) ? true : false;
}

/*

Function: is_alpha_numeric
	Finds whether a variable only contains characters A-Z or a-z or 0-9

Parameters:
	var - value to validate

Returns:
	bool

*/

function is_alpha_numeric($var)
{
	return preg_match('/^[a-zA-Z0-9]+$/', $var) ? true : false;
}

/*

Function: is_number
	Finds whether a variable is a number

Parameters:
	var - value to validate

Returns:
	bool

*/

function is_number($var)
{
	return preg_match('/^[0-9]+?[.]?[0-9]*$/', $var) ? true : false;
}

/*

Function: is_positive_number
	Finds whether a variable is a positive number

Parameters:
	var - value to validate

Returns:
	bool

*/

function is_positive_number($var)
{
	return is_number($var) && $var > 0 ? true : false;
}

/*

Function: is_match
	Finds whether a $var1 is equal to $var2

Parameters:
	var1 - value to validate
	var2 - value to validate

Returns:
	bool

*/

function is_match($var1, $var2)
{
	return $var1 == $var2;
}

/*

Function: is_between
	Finds whether a variable is between $min and $max

Parameters:
	var - value to validate
	min - min number
	max - max number

Returns:
	bool

*/

function is_between($var, $min, $max)
{
	return ( (is_numeric($min) && is_numeric($max)) && ($var >= $min && $var <= $max) );
}

/*

Function: is_length
	Finds whether a variable length equals $length

Parameters:
	var - value to validate
	length - length

Returns:
	bool

*/

function is_length($var, $length)
{
	return count(str_split($var)) == $length;
}

/*

Function: is_length_between
	Finds whether a variable length is between $min and $max

Parameters:
	var - value to validate
	min - min length
	max - max length

Returns:
	bool

*/

function is_length_between($var, $min, $max)
{
	$length = strlen($var);
	return ( $length >= $min ) && ( $length <= $max );
}