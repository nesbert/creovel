<?php
/**
 * General top-level functions.
 *
 * @package     Creovel
 * @subpackage  Helpers
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.1.0
**/

/**
 * Prints human-readable information about a variable much prettier.
 *
 * @param mixed $obj The value to print out
 * @param boolean $kill Die after print out to screen.
 * @return void
 * @author John Faircloth
 **/
function print_obj($obj, $kill = false)
{
    echo create_html_element('pre', array('class' => 'print_obj'), "\n".print_r($obj, 1)). "\n";
    if ($kill) die;
}

/**
 * Cleans up javascript and properly escapes quotes.
 *
 * @param string $javascript
 * @return string
 * @author Nesbert Hidalgo
 **/
function escape_javascript($javascript)
{
    $escape = array(
        "\r\n"  => '\n',
        "\r"    => '\n',
        "\n"    => '\n',
        '"'     => '\"',
        "'"     => "\\'"
    );
    return str_replace(array_keys($escape), array_values($escape), $javascript);
}

/**
 * Returns an array user defined constants.
 *
 * @return array
 * @author Nesbert Hidalgo
 **/
function get_user_defined_constants()
{
    $return = get_defined_constants(true);
    return $return['user'];
}

/**
 * Get an array of all class parents.
 *
 * @link http://us.php.net/manual/en/function.get-parent-class.php#57548
 * @return array
 **/
function get_ancestors($class)
{
    $classes = array($class);
    while($class = get_parent_class($class)) { $classes[] = $class; }
    return $classes;
}

/**
 * Returns a human readable size or a file or a size
 *
 * @param string $file_or_size File path or size.
 * @link http://us2.php.net/manual/hk/function.filesize.php#64387
 * @return string
 **/
function get_filesize($file_or_size)
{
    $iec = array("B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB");
    $size = is_numeric($file_or_size) ? $file_or_size : @filesize($file_or_size);
    $i = 0;
    while ( ($size/1024) > 1 ) {
        $size = $size / 1024;
        $i++;
    }
    return substr($size, 0, strpos($size,'.') + 4).' '.$iec[$i];
}

/**
 * Get the mime type of a file.
 *
 * @param string $filepath
 * @link http://us.php.net/manual/en/function.finfo-open.php#78927
 * @return string
 **/
function get_mime_type($filepath)
{
    ob_start();
    system("file -i -b {$filepath}");
    $output = ob_get_clean();
    $output = explode("; ",$output);
    if ( is_array($output) ) {
        $output = $output[0];
    }
    return str_replace("\n", '', $output);
}

/**
 * Add slashes to arrays, objects, and strings recursively.
 *
 * @param mixed $data
 * @return mixed
 * @author Nesbert Hidalgo
 **/
function add_slashes($data)
{
    switch (true) {
        // clean data array
        case is_array($data):
            $clean_values = array();
            foreach ($data as $name => $value) {
                $clean_values[$name] = is_array($value)
                                        ? array_map('addslashes', $value)
                                        : addslashes(trim($value));
            }
            break;
        
        // get vars from object -> clean data -> update and return object
        case is_object($data):
            $clean_values = get_object_vars($data);
            foreach ($clean_values as $name => $value) {
                $data->{$name} = add_slashes($value);
            }
            $clean_values = $data;
            break;
        
        // clean data
        default:
            $clean_values = addslashes(trim($data));
            break;
    }
    
    return $clean_values;
}

/**
 * Strip slashes to arrays, objects, and strings recursively.
 *
 * @param mixed $data
 * @return mixed
 * @author Nesbert Hidalgo
 **/
function strip_slashes($data)
{
    switch (true) {
        // clean data array
        case is_array($data):
            $clean_values = array();
            foreach ($data as $name => $value) {
                $clean_values[$name] = is_array($value)
                                        ? array_map('strip_slashes', $value)
                                        : stripslashes(trim($value));
            }
            break;
        
        // get vars from object -> clean data -> update and return object
        case is_object($data):
            $clean_values = get_object_vars($data);
            foreach ($clean_values as $name => $value) {
                $data->{$name} = strip_slashes($value);
            }
            $clean_values = $data;
            break;
        
        // clean data
        default:
            $clean_values = stripslashes(trim($data));
            break;
    }
    
    return $clean_values;
}

/**
 * String replaces a string using array keys with array values.
 *
 * @param string $string
 * @param array $array
 * @return string
 * @author Nesbert Hidalgo
 **/
function str_replace_array($string, $array)
{
    return str_replace(array_keys($array), array_values($array), $string);
}

/**
 * A faster/less memory substitute for strstr() used to check the occurrence
 * of a subject in a string.
 *
 * @param string $needle
 * @param array $haystack
 * @return boolean
 * @author Nesbert Hidalgo
 **/
function in_string($needle, $haystack)
{
    if (strpos($haystack, (string) $needle) === false) {
        return false;
    } else {
        return true;
    }
}

/**
 * Get the data type of a variable.
 *
 * @param $var
 * @link http://us3.php.net/manual/en/function.gettype.php#78381
 * @return string
 **/
function get_type($var)
{
    return
        (is_array($var) ? 'array' :
        (is_bool($var) ? 'boolean' :
        (is_float($var) ? 'float' :
        (is_int($var) ? 'integer' :
        (is_null($var) ? 'null' :
        (is_numeric($var) ? 'numeric' :
        (is_object($var) ? 'object' :
        (is_resource($var) ? 'resource' :
        (is_string($var) ? 'string' :
        'unknown' )))))))));
}

/**
 * Get an array of directory listings with $options for flexibility.
 * Options for $options array are as follows:
 *
 * 'recursive': default false
 * 'show_dirs': default false, add directory to array
 * 'show_invisibles': default false, add .* files to array
 * 'group': default false, Group files by directory
 * 'filter': default '', use a regex to filter array, example '/.php$/'
 *
 * @param string $dir
 * @param array Assoctive array of options
 * @return array
 * @author Nesbert Hidalgo
 * @link http://snippets.dzone.com/posts/show/155
 **/
function dir_to_array($dir, $options = array())
{
	// set default options
	if (!is_array($options)) $options = array();
	if (!isset($options['recursive'])) $options['recursive'] = false;
	if (!isset($options['show_dirs'])) $options['show_dirs'] = false;
	if (!isset($options['show_invisibles'])) $options['show_invisibles'] = false;
	if (!isset($options['group'])) $options['group'] = false;
	if (!isset($options['filter'])) $options['filter'] = '';
	
	$array_items = array();
	if ($handle = opendir($dir)) {
		while (false !== ($file = readdir($handle))) {
			
			if (!$options['show_invisibles'] && $file{0} == '.') continue;
			
			if ($file != '.' && $file != '..') {
				
				$filepath = $dir . DS . $file;
				
				// be nice to non *nix machines
				$dir_regex = DS == '/' ? '/\/\//si' : '/\\\\/si';
				
				if (is_dir($filepath)) {
					
					if ($options['show_dirs']) {
						if ($options['group']) {
							$array_items[dirname($filepath)][] = preg_replace($dir_regex, DS, $filepath);
						} else {
							$array_items[] = preg_replace($dir_regex, DS, $filepath);
						}
					}
					if ($options['recursive']) {
						$array_items = array_merge($array_items, dir_to_array($filepath, $options));
					}
					
				} else {
				
					if ($options['filter'] && !preg_match($options['filter'], $file)) continue;
					
					if ($options['group']) {
						$array_items[dirname($filepath)][] = preg_replace($dir_regex, DS, $filepath);
					} else {
						$array_items[] = preg_replace($dir_regex, DS, $filepath);
					}
					
				}
				
			}
		}
		closedir($handle);
	}
	return $array_items;
}

/**
 * Gets a directories files in a directory by file type. Returns an
 * associative array with the file_name as key and file_path as value.
 *
 * @param string $dir_path
 * @param string $file_type Optional default set to 'php'
 * @return array
 * @author Nesbert Hidalgo
 **/
function get_files_from_dir($dir_path, $file_type = 'php', $show_invisibles = false)
{
    $files = array();
    if ( $handle = opendir($dir_path) ) {
        while ( false !== ($file = readdir($handle)) ) {
            if (!$show_invisibles && $file{0} == '.') continue;
            if ( in_string('.'.$file_type, $file) ) {
                $files[substr($file, 0, -4)] = $dir_path.DS.$file;
            }
        }
        closedir($handle);
    }
    asort($files);
    return $files;
}

/**
 * Returns the raw post from php://input. It is a less memory intensive
 * alternative to $HTTP_RAW_POST_DATA and does not need any special php.ini
 * directives. php://input is not available with enctype="multipart/form-data".
 *
 * @link http://us.php.net/wrappers.php
 * @return string
 * @author Nesbert Hidalgo
 **/
function get_raw_post()
{
    return trim(file_get_contents('php://input'));
}

/**
 * Sanitize a string by not allowing HTML, encoding and using HTML Special
 * characters for certain tags. Basic layer for XSS prevention.
 *
 * @param string $str
 * @param string $length
 * @param string $allowed_tags
 * @author Nesbert Hidalgo
 **/
function clean_str($str, $length = 0, $allowed_tags = false)
{
    // strip or allow only certain tags
    $str = strip_tags($str, (!$allowed_tags ? null : $allowed_tags));
    // trim, utf-8 and HTML encode
    $str = htmlspecialchars(utf8_encode(trim($str)), ENT_QUOTES);
    // limit length of string
    $length = intval($length);
    if ($length) $str = substr($str, 0, $length);
    return $str;
}

/**
 * Sanitize associative array values.
 *
 * @param array $array
 * @return array
 * @see clean_str()
 * @author Nesbert Hidalgo
 **/
function clean_array($array)
{
    if (is_array($array)) {
        foreach ($array as $k => $v) {
            $array[$k] = is_array($v) ? clean_array($v) : clean_str($v);
        }
        return $array;
    } else {
        return clean_str($array);
    }
}

/**
 * Search a multidimensional array for a certain value and return the
 * array with the match.
 *
 * @return array
 * @author Nesbert Hidalgo
 **/
function search_array($i, $val, $array)
{
    if (is_array($array)) foreach ($array as $row) {
        if (@$row[$i] == $val) return $row;
    } else {
        return null;
    }
}