<?php
/**
 * Contains all Creovel specific functions.
 *
 * @package     Creovel
 * @subpackage  Helpers
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.1.0
 **/

/**
 * Autoload routine for controllers, interfaces, adapters, services, vendor,
 * mailer and models.
 *
 * @access private
 * @param string $class
 * @author Nesbert Hidalgo
 **/
function __autoload_creovel($class)
{
    try {
        // check for nested paths
        $class = Inflector::patherize($class);
        
        // make all file names under score
        $class = strtolower($class);
        
        switch (true) {
            
            case (true):
                $type = 'Core Class';
                $path = CREOVEL_PATH . 'classes' . DS . $class.'.php';
                if (file_exists($path)) break;
                
            case (true):
                $type = 'Adapter';
                $path = CREOVEL_PATH . 'adapters' . DS . $class . '.php';
                if (file_exists($path)) break;
                
            case (true):
                $type = 'Controller';
                $path = CONTROLLERS_PATH . $class . '.php';
                if (file_exists($path)) break;
                
            case (true):
                $type = CValidate::in_string('Mailer', $class) ? 'Mailer' : 'Model';
                $path = MODELS_PATH . $class . '.php';
                if (file_exists($path)) break;
                
            case (true):
                $type = 'Shared';
                @$shared_path = SHARED_PATH . $class . '.php';
                if (file_exists($shared_path)) {
                    $path = $shared_path;
                    break;
                }
                
            case (true):
                $type = 'Vendor';
                $path = VENDOR_PATH . $class . '.php';
                if (file_exists($path)) break;
                
            case (true):
                $type = 'Module';
                $path = CREOVEL_PATH . 'modules' . DS . $class . '.php';
                if (file_exists($path)) break;
        }
        
        if (file_exists($path)) {
            require_once $path;
        } else {
            $file = $class;
            if ($type == 'Controller') CREO('application_error_code', 404);
            if ($type == 'Controller' || $type == 'Model' || $type == 'Mailer') {
                $folders = explode('/', $class);
                foreach ($folders as $k => $v) {
                    $folders[$k] = CString::classify($v);
                }
                $class = implode('_', $folders);
            }
            throw new Exception("{$class} not found in <strong>{$path}</strong>");
        }
    } catch (Exception $e) {
        // __PHP_Incomplete_Class Object bypass flag 
        if (empty($GLOBALS['CREOVEL']['SKIP_AUTOLOAD_ERRORS'])) {
            CREO('application_error', $e);
        } else {
            if (!empty($GLOBALS['CREOVEL']['LOG_ERRORS'])) {
                CREO('log', 'Error: ' . $e->getMessage());
            }
        }
    }
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
            break;
            
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
            break;
        
        case $key == 'LOG':
            $log = new Logger(empty($args[2]) ? @LOG_PATH . $GLOBALS['CREOVEL']['MODE'] . '.log' : $args[2]);
            $log->write(str_replace(array('<em>', '</em>', '<strong>', '</strong>'), '"', $val));
            break;
            
        case $val !== null:
            return $GLOBALS['CREOVEL'][$key] = $val;
            break;
            
        default:
            return $GLOBALS['CREOVEL'][$key];
            break;
    }
}

/**
 * Sets and unsets $_SESSION['flash']. Used by application notices.
 *
 * @param string $message Optional string to be displayed.
 * @param string $type - Type of notice passed
 * @return bool/string String or message
 * @author Nesbert Hidalgo
 **/
function flash_message($message = null, $type = 'notice')
{
    if ($message || isset($_SESSION['flash']['message'])) {
        
        if ( $message ) {
            
            $_SESSION['flash']['message'] = $message;
            $_SESSION['flash']['type'] = $type;
            $_SESSION['flash']['checked'] = 'no';
        
        } elseif ( $_SESSION['flash']['checked'] == 'no' ) {
        
            $_SESSION['flash']['checked'] = 'yes';
            return true;
        
        } else {
        
            $message = $_SESSION['flash']['message'];
            unset($_SESSION['flash']);
            return $message;
        
        }
        
    } else {
        
        return false;
    
    }
}

/**
 * Returns the $_SESSION['flash']['type'].
 *
 * @return string
 * @author Nesbert Hidalgo
 **/
function flash_type()
{
    return $_SESSION['flash']['type'] ? $_SESSION['flash']['type'] : 'notice';
}

/**
 * Alias for flash_message($message, 'notice').
 *
 * @param string $message Message for flash notice
 * @return mixed String or boolean
 * @author Nesbert Hidalgo
 **/
function flash_notice($message = null)
{
    return flash_message($message, 'notice');
}

/**
 * Alias for flash_message($message, 'error').
 *
 * @param string $message Message for flash notice
 * @return mixed String or boolean
 * @author Nesbert Hidalgo
 **/
function flash_error($message = null)
{
    return flash_message($message, 'error');
}

/**
 * Alias for flash_message($message, 'warning').
 *
 * @param string $message Message for flash notice
 * @return mixed String or boolean
 * @author Nesbert Hidalgo
 **/
function flash_warning($message = null)
{
    return flash_message($message, 'warning');
}

/**
 * Alias for flash_message($message, 'sucess').
 *
 * @param string $message Message for flash notice
 * @return mixed String or boolean
 * @author Nesbert Hidalgo
 **/
function flash_success($message = null)
{
    return flash_message($message, 'success');
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
    $use_pretty_urls = CString::ends_with('*', $GLOBALS['CREOVEL']['ROUTING']['current']['url']);
    
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
        $https = $args[0]['https'];
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
        $https = @$args[3];
    }
    
    if (@is_array($_ENV['secure_controllers'])
        && in_array($controller, $_ENV['secure_controllers'])) {
        $https = true;
    }
    // build url
    $uri = $GLOBALS['CREOVEL']['ROUTING']['base_url'] . (!$controller && $action
                ? get_controller()
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
    
    return ($https ? str_replace('http://', 'https://', BASE_URL) : '') .
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
    die;
}
