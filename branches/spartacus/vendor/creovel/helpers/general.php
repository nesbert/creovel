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
    echo create_html_element('pre', array('class' => 'print_obj'), "\n".print_r($obj, 1));
    if ($kill) die;
}

/**
 * Cleans up javascript.
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
 * Check if an array is an associative array.
 *
 * @param array $_array
 * @link http://us3.php.net/manual/en/function.is-array.php#85324
 * @return boolean
 **/
function is_hash($array)
{
    if (is_array($array) == false) {
        return false;
    }
    
    foreach (array_keys($array) as $k => $v) {
        if ($k !== $v) return true;
    }
    
    return false;
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
    $size = is_numeric($file_or_size) ? $file_or_size : filesize($file_or_size);
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
            $clean_values = $this->strip_slashes(get_object_vars($data));
            foreach ($clean_values as $name => $value) {
                $data->$name = is_array($value)
                                ? array_map('addslashes', $value)
                                : addslashes(trim($value));
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
            $clean_values = $this->strip_slashes(get_object_vars($data));
            foreach ($clean_values as $name => $value) {
                $data->$name = is_array($value)
                                        ? array_map('strip_slashes', $value)
                                        : stripslashes(trim($value));
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