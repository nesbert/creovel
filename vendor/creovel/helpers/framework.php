<?php
/**
 * Contains all Creovel specific functions.
 *
 * @package     Creovel
 * @subpackage  Helpers
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.1.0
 **/

// Be kind to existing __autoload routines
if (PHP <= '5.1.2') {
    function __autoload($class) { Creovel::autoload($class); }
} else {
    spl_autoload_register(array('Creovel', 'autoload'));
    if (function_exists('__autoload')) spl_autoload_register('__autoload');
}

/**
 * Set & get $GLOBALS['CREOVEL'] variables and other magical goodness.
 *
 * @param string $key
 * @param mixed $val
 * @return mixed
 * @author Nesbert Hidalgo
 **/
function CREO()
{
    // set args
    $args = func_get_args();
    $key = isset($args[0]) ? $args[0] : null;
    $val = isset($args[1]) ? $args[1] : null;
    
    // if no key return creovel super global
    if (!$key) return $GLOBALS['CREOVEL'];
    
    // uppercase all keys
    $key = strtoupper($key);
    
    // get or set values
    switch (true) {
        case $key == 'APPLICATION_ERROR':
            $GLOBALS['CREOVEL']['ERROR']->add($val);
            return;
            
        case $key == 'DATABASE':
            $mode = strtoupper($val['mode']);
            $GLOBALS['CREOVEL']['DATABASES'][$mode] = @array(
                'adapter'   => $val['adapter'],
                'host'      => $val['host'],
                'username'  => $val['username'],
                'password'  => $val['password'],
                'database'  => $val['database']
                );
            if (isset($val['default']) && empty($val['database'])) {
                $GLOBALS['CREOVEL']['DATABASES'][$mode]['database'] = $val['default'];
            }
            if (isset($val['port'])) {
                $GLOBALS['CREOVEL']['DATABASES'][$mode]['port'] = $val['port'];
            }
            if (isset($val['socket'])) {
                $GLOBALS['CREOVEL']['DATABASES'][$mode]['socket'] = $val['socket'];
            }
            if (isset($val['schema'])) {
                $GLOBALS['CREOVEL']['DATABASES'][$mode]['schema'] = $val['schema'];
            }
            if (isset($val['persistent'])) {
                $GLOBALS['CREOVEL']['DATABASES'][$mode]['persistent'] = $val['persistent'];
            }
            return;
        
        case $key == 'LOG':
            $log = new Logger(empty($args[2]) ? @LOG_PATH . $GLOBALS['CREOVEL']['MODE'] . '.log' : $args[2]);
            $log->write(str_replace(array('<em>', '</em>', '<strong>', '</strong>'), '"', $val));
            break;
            
        case $key == 'SESSION' && !empty($val):
            $GLOBALS['CREOVEL'][$key] = $val;
            ActiveSession::start();
            return $GLOBALS['CREOVEL'][$key];
            
        case $val !== null:
            return $GLOBALS['CREOVEL'][$key] = $val;
            
        default:
            return $GLOBALS['CREOVEL'][$key];
    }
}

/**
 * Creates a url path for lazy programmers.
 *
 * <code>
 * url_for('user', 'edit', 1234);
 * </code>
 *
 * @param string $controller
 * @param string $action Optional
 * @param mixed $id Optional ID or an associative array of parameters
 * @param boolean $https Optional
 * @return string
 * @author Nesbert Hidalgo
 **/
function url_for()
{
    $args = func_get_args();
    $use_pretty_urls = CString::ends_with('*', @$GLOBALS['CREOVEL']['ROUTING']['current']['url']);
    
    if (is_array($args[0])) {
        
        // Set Contoller
        $controller = $args[0]['controller'];
        unset($args[0]['controller']);
        
        // set action
        $action = $args[0]['action'];
        unset($args[0]['action']);
        
        // set id
        $id = $args[0]['id'];
        unset($args[0]['id']);
        
        // secure mode
        $https = isset($args[0]['https']) && is_bool($args[0]['https']) ? $args[0]['https'] : false;
        unset($args[0]['https']);
        
        // set misc
        $misc = http_build_query($args[0]);
    
    } else {
    
        // set controller
        $controller = $args[0];
        
        // set action
        $action = @$args[1];
        
        // set id and misc
        if (@is_array($args[2])) {
            $id = @$args[2]['id'];
            unset($args[2]['id']);
            $misc = http_build_query($args[2]);
        } else {
            $id = @$args[2];
        }
        
        // secure mode
        $https = isset($args[3]) && is_bool($args[3]) ? $args[3] : false;
    }
    
    if (@is_array($_ENV['secure_controllers'])
        && in_array($controller, $_ENV['secure_controllers'])) {
        $https = true;
    }
    // build url
    $uri = @$GLOBALS['CREOVEL']['ROUTING']['base_url'] . (!$controller && $action
                ? Creovel::events('controller')
                : $controller ) . ($action ? "/{$action}" : '');
    
    if (@$misc) {
        if ($use_pretty_urls) {
            $uri .= ($id ? '/id/' . urlencode($id) : '') .
                '/' . str_replace(array('&', '='), '/', $misc) .
                ($GLOBALS['CREOVEL']['VIEW_EXTENSION_APPEND'] ? '.' . $GLOBALS['CREOVEL']['VIEW_EXTENSION'] : '');
        } else {
            $uri .= "?" . ($id ? "id={$id}&" : '') . $misc;
        }
    } else if ($id) {
        if ($use_pretty_urls) {
            $uri .= '/id/' . urlencode($id) .
            ($GLOBALS['CREOVEL']['VIEW_EXTENSION_APPEND'] ? '.' . $GLOBALS['CREOVEL']['VIEW_EXTENSION'] : '');
        } else {
            $uri .= "/{$id}";
        }
    }
    
    return ($https ? str_replace('http://', 'https://', CNetwork::http_host()) : '') .
        (($uri != '/') && ($uri{strlen($uri)-1} == '/') ? substr($uri, 0, -1) : $uri);
}

/**
 * Redirects the page using a header location redirect. "Note should only be
 * used inside controllers".
 *
 * @param string $controller
 * @param string $action Optional
 * @param mixed $id Optional ID or an associative array of parameters
 * @return void
 * @author Nesbert Hidalgo
 **/
function redirect_to($controller = '', $action = '', $id = '')
{
    redirect_to_url(url_for($controller, $action, $id));
}

/**
 * Header redirect and die. "Note should only be used inside controllers".
 *
 * @param string $url
 * @return void
 * @author John Faircloth
 **/
function redirect_to_url($url)
{
    header('location: ' . $url);
    exit;
}
