<?php
/*
 * Deep cleans arrays, objects, strings
 */ 
function strip_slashes($data) {
	
	switch ( true ) {
		
		// clean data array
		case ( is_array($data) ):
			$clean_values = array();				
			foreach ($data as $name => $value) $clean_values[$name] = is_array($value) ? array_map('strip_slashes', $value) : stripslashes(trim($value));
		break;
		
		// get vars from object -> clean data -> update and return object
		case ( is_object($data) ):
			$clean_values = $this->strip_slashes(get_object_vars($data));
			foreach ($clean_values as $name => $value) $data->$name = is_array($value) ? array_map('strip_slashes', $value) : stripslashes(trim($value));
			$clean_values = $data;
		break;
		
		// clean data
		default:
			$clean_values = stripslashes(trim($data));
		break;
		
	}
	
	return $clean_values;
	
}







function phone_to_db($phone_number) {

	$search_array = array('-', '(', ') ');
	
	foreach ($search_array as $mixed_search) {
	
		$phone_number = str_replace($mixed_search , '', $phone_number);
	
	}
	
	return $phone_number;

}

function phone_to_web($phone_number) {

	$phone_number = '('.substr($phone_number, 0, 3).') '.substr($phone_number, 3, 3).'-'.substr($phone_number, 6, 4);
	
	return $phone_number;

}


function quote_fix($string) {

	return str_replace("\"", "&quot;", $string);

}


?>