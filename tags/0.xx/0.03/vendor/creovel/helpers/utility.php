<?php
/* This file includes assorted utilities, being kept separate to know which functions
are not part of the original framework. */

function seek_files($location = null){
	$retval = array();
	$search = which($location, 'files');
	$files = scandir($search);
	foreach($files as $file){
		if (substr($file, 0, 1) == '.') continue;
		if (is_dir($search.'/'.$file)){
			array_push($retval, seek_files($location.'/'.$file));
		} else {
			array_push($retval, $search.'/'.$file);
		}
	}
	return(array_unique(flatten_array($retval)));	
}

function clean_seek_files($location = null){
	$_ = seek_files($location);
	$retval = array();
	foreach($_ as $k => $v) $retval[$v] = preg_replace('/\/+/', '/', basename($v));
	return $retval;
}

//truth selector
function which(){
	$args = func_get_args();
	foreach ($args as $value){
		if ($value) return $value;
	}
	return array_pop($args);
}

//recursive array_diff: returns differences IN A1.
function array_compare($a1, $a2){
	foreach($a1 as $key => $val){
		if (is_array($val)){
			if (is_array($a2[$key])){
				$retval[$key] = array_compare($val, $a2[$key]);
			} else {
				$retval[$key] = $a1[$key];
			}
		} else {
			if ($val !== $a2[$key]){
				$retval[$key] = $val;
			}
		}
	}
	return $retval;
}

//flattens an array into one dimension.
//theoretically flawed if, for instance, there are both $a['field_name'] and $a['field']['name']...
function flatten_array($data_array, $ubiq_array = array(), $args = ''){
	$args = preg_replace("/^_/", '', $args);

	if (count($data_array)){
		foreach ($data_array as $key => $data){
			if (is_array($data)){
				$ubiq_array = array_merge($ubiq_array, flatten_array($data, $ubiq_array, $args."_{$key}"));
			} else {
				$k = $_ = preg_replace("/^_/", '', $args."_{$key}");
				$ctr = 0;
				while(isset($ubiq_array[$k])){
					$k = $_."_{$ctr}";
				}
				$ubiq_array[$k] = "{$data}";
			}
		}
	}
	return $ubiq_array;
}

?>