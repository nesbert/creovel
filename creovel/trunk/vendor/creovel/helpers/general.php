<?php
/*
 * General top-level functions.
 */

/**
 * Prints human-readable information about a variable much prettier.
 *
 * @author John Faircloth
 */
 
function print_obj($obj, $kill = false)
{

	echo '<pre class="print_obj" style="text-align: left;">'."\n";
	print_r($obj);
	echo "\n</pre>\n";
	if ( $kill ) die;

}

/*
 * Return user definde constats
 *
 * @author Nesbert Hidalgo
 * @return array
 */
 function get_user_defined_constants()
 {
 	$return = get_defined_constants(true);
	return $return['user'];
 }
 
/*
 * Returns a human readable size or a file or a size
 * http://us2.php.net/manual/hk/function.filesize.php#64387
 *
 * @author Nesbert Hidalgo
 * @param mixed $file_or_size
 * @return string
 */
function get_filesize($file_or_size)
{
	$iec = array("B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB");	
	$size = is_numeric($file_or_size) ? $file_or_size : filesize($file_or_size);
	$i = 0;
	while ( ($size/1024) > 1 ) {
		$size = $size / 1024;
		$i++;
	}
	return substr($size, 0, strpos($size,'.') + 4).' '.$iec[$i];
}

/*
 * Deep cleans arrays, objects, strings
 *
 * @author Nesbert Hidalgo
 * @param mixed $data
 * @return mixed
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
?>