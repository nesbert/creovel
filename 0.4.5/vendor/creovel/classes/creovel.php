<?php
/**
 * The main class where the model, view and controller interact.
 *
 * @package     Creovel
 * @subpackage  Core
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.1.0
 * @author      Nesbert Hidalgo
 **/
class Creovel
{
    /**
     * Set constants, include helpers, configuration files & core classes,
     * amp default routes and set CREOVEL & environment variables.
     *
     * @return void
     **/
    public static function init()
    {
        if (defined('CREOVEL_VERSION')) return;
        
        // If not PHP 5 stop.
        if (PHP_VERSION <= 5) {
            die('Creovel requires PHP >= 5!');
        }
        
        // Define creovel constants.
        define('CREOVEL_VERSION', '0.4.5');
        define('CREOVEL_RELEASE_DATE', '2010-0?-?? ??:??:??');
        
        // Define environment constants.
        define('PHP', PHP_VERSION);
        
        // Define time constants.
        define('SECOND',  1);
        define('MINUTE', 60 * SECOND);
        define('HOUR',   60 * MINUTE);
        define('DAY',    24 * HOUR);
        define('WEEK',    7 * DAY);
        define('MONTH',  30 * DAY);
        define('YEAR',  365 * DAY);
        
        if (!defined('DS')) {
            define('DS', DIRECTORY_SEPARATOR);
        }
        if (!defined('CREOVEL_PATH')) {
            define('CREOVEL_PATH', dirname(dirname(__FILE__)) . DS);
        }
        
        // Include base helper libraries.
        require_once CREOVEL_PATH . 'helpers' . DS . 'framework.php';
        
        // Include minimum base classes.
        require_once CREOVEL_PATH . 'classes' . DS . 'c_object.php';
        require_once CREOVEL_PATH . 'classes' . DS . 'c_network.php';
        require_once CREOVEL_PATH . 'classes' . DS . 'c_string.php';
        require_once CREOVEL_PATH . 'modules' . DS . 'module_base.php';
        require_once CREOVEL_PATH . 'modules' . DS . 'inflector.php';
        
        // Set default mode.
        $GLOBALS['CREOVEL']['MODE'] = 'production';
        $GLOBALS['CREOVEL']['CLI'] = empty($_SERVER['DOCUMENT_ROOT']);
        $GLOBALS['CREOVEL']['BUFFER_HEADER'] = true;
        $GLOBALS['CREOVEL']['DEFAULT_CONTROLLER'] = 'index';
        $GLOBALS['CREOVEL']['DEFAULT_ACTION'] = 'index';
        $GLOBALS['CREOVEL']['DEFAULT_LAYOUT'] = 'default';
        $GLOBALS['CREOVEL']['PAGE_CONTENTS'] = '@@page_contents@@';
        $GLOBALS['CREOVEL']['SESSION'] = false;
        $GLOBALS['CREOVEL']['SHOW_SOURCE'] = false;
        $GLOBALS['CREOVEL']['VIEW_EXTENSION'] = 'html';
        $GLOBALS['CREOVEL']['VIEW_EXTENSION_APPEND'] = false;
        
        // Set error handler.
        require_once CREOVEL_PATH . 'classes' . DS . 'action_error_handler.php';
        $GLOBALS['CREOVEL']['ERROR'] = new ActionErrorHandler;
        $GLOBALS['CREOVEL']['APPLICATION_ERROR_CODE'] = '';
        $GLOBALS['CREOVEL']['VALIDATION_ERRORS'] = array();
        
        // set configuration settings include_once to make it optional
        if (defined('CONFIG_PATH')) {
            include_once CONFIG_PATH . 'databases.php';
            include_once CONFIG_PATH . 'environment.php';
            include_once CONFIG_PATH . 'environment' . DS .
                $GLOBALS['CREOVEL']['MODE'] . '.php';
        }
                
        // Include application_helper
        if (defined(HELPERS_PATH)
            && file_exists($helper = HELPERS_PATH . 'application_helper.php')) {
            require_once $helper;
        }
    }
    
    /**
     * Initialize framework for web applications. Set framework
     * events and params. Build controller execution environment.
     *
     * @return void
     **/
    public static function web($events = null, $params = null, $return_as_str = false, $execute = true)
    {
        try {
            self::init();
            
            // gather up any output that occurs before output phase
            if ($GLOBALS['CREOVEL']['BUFFER_HEADER']) {
                ob_start();
            }
            
            // ignore certain requests
            self::ignore_check();
            
            // if global_xss_filtering is enabled
            if (!empty($GLOBALS['CREOVEL']['GLOBAL_XSS_FILTERING'])) {
                if (empty($GLOBALS['CREOVEL']['XSS_FILTERING_CALLBACK'])) {
                    $xss_func = 'CArray::clean';
                } else {
                    $xss_func = $GLOBALS['CREOVEL']['XSS_FILTERING_CALLBACK'];
                }
                // filter COOKIE, GET, POST, SERVER
                $_COOKIE = $xss_func($_COOKIE);
                $_GET = $xss_func($_GET);
                $_POST = $xss_func($_POST);
                $_SERVER = $xss_func($_SERVER);
            }
            
            // initialize web for web appliocations only run once
            static $initialized;
            if (empty($initialized)) {
                
                // Set framework dispatch file
                if (empty($GLOBALS['CREOVEL']['DISPATCHER'])) {
                    $GLOBALS['CREOVEL']['DISPATCHER'] = basename($_SERVER['PHP_SELF']);
                }
                
                // Set routing defaults
                $GLOBALS['CREOVEL']['ROUTING'] = @parse_url(CNetwork::url());
                if (!$GLOBALS['CREOVEL']['ROUTING']) {
                    $GLOBALS['CREOVEL']['ROUTING']['path'] = null;
                }
                $GLOBALS['CREOVEL']['ROUTING']['current'] = array();
                $GLOBALS['CREOVEL']['ROUTING']['routes'] = array();
                
                // set additional routing options
                $GLOBALS['CREOVEL']['ROUTING']['base_path'] = self::base_path();
                $GLOBALS['CREOVEL']['ROUTING']['base_url'] = self::base_url();
                
                require_once CREOVEL_PATH . 'classes/action_router.php';
                
                // Set default route
                ActionRouter::map('default', '/:controller/:action/*', array(
                            'controller' => 'index',
                            'action' => 'index'
                            ));
                
                // Set default error route
                ActionRouter::map('errors', '/errors/:action/*', array(
                            'controller' => 'errors',
                            'action' => 'general'
                            ));
                
                // Include custom routes
                if (defined('CONFIG_PATH')) {
                    require_once CONFIG_PATH . 'routes.php';
                }
                
                $initialized = true;
            } // end if (empty($initialized))
            
            if (!$execute) return;
            
            // set event and params
            $events = is_array($events) ? $events : self::events();
            $params = is_array($params) ? $params : self::params();
            
            // handle dashed controller names
            $events['controller'] = Inflector::underscore($events['controller']);
            
            if (isset($params['nested_controller'])
                && $params['nested_controller']) {
                $events['nested_controller_path'] = $params['nested_controller'];
                $controller_path =  $events['nested_controller_path'] . DS .
                    $events['controller'];
                // remove from params
                unset($params['nested_controller']);
            } else {
                $controller_path = $events['controller'];
            }
            
            // include controller
            self::include_controller($controller_path);
            
            // create controller object and build the framework
            $controller = Inflector::camelize($events['controller']) . 'Controller';
            
            if (class_exists($controller)) {
                $controller = new $controller();
            } else {
                throw new Exception("Class " . str_replace('Controller', '', $controller) .
                " not found in <strong>" . Inflector::classify($controller) . "</strong>");
            }
            
            // set controller properties
            $controller->__set_events($events);
            $controller->__set_params($params);
            
            // execute action
            $controller->__execute_action();
            
            if ($GLOBALS['CREOVEL']['BUFFER_HEADER']) {
                // determine if any output has been sent to output buffer
                $buffer = ob_get_contents();
                
                // if buffer store in CREO varialbe
                if ($buffer) $GLOBALS['CREOVEL']['BUFFER'] = $buffer;
                
                // clean buffer
                ob_clean();
            }
            
            // output to user
            return $controller->__output($return_as_str);
            
        } catch (Exception $e) {
            CREO('application_error_code', 404);
            CREO('application_error', $e);
        }
    }
    
    /**
     * Initialize framework for command line support.
     *
     * @return void
     **/
    public static function cli()
    {
        self::init();
        
        $GLOBALS['CREOVEL']['CLI'] = true;
        
        global $argc;
        global $argv;
        global $args;
        global $flags;
        global $params;
        
        // create local variables
        if ($argc > 1) {
            // set params & flagsforth argument and on
            // flags start with "-"
            $args = array();
            $flags = array();
            $params = array();
            foreach ($argv as $k => $v) {
                if (!$k) continue;
                if (CString::contains(':', $v)) {
                    $v  = explode(':', $v);
                    $params[$v[0]] = $v[1];
                } else if (CString::starts_with('-', $v)) {
                    // double dash mean whole words
                    if (CString::starts_with('--', $v)) {
                        $flags[] = substr($v, 2);
                    } else {
                        // split each single dash char into a flag
                        foreach (str_split(substr($v, 1)) as $___) {
                            $flags[] = $___;
                        }
                    }
                } else {
                    $args[] = $v;
                }
            }
            
        }
    }
    
    /**
     * Set constants, include helpers, configuration files & core classes,
     * amp default routes and set CREOVEL & environment variables.
     *
     * @return void
     **/
    public static function main($force_cli = false)
    {
        self::init();
        
        if ($force_cli) $GLOBALS['CREOVEL']['CLI'] = true; 
        
        // Run framework in command line or web mode.
        if ($GLOBALS['CREOVEL']['CLI']) {
            self::cli();
        } else {
            self::web();
        }
    }
    
    /**
     * Alias to Creovel::main();
     *
     * @return void
     **/
    public static function run()
    {
        self::main();
    }
    
    /**
     * Return the an associative array of events or a particular event value.
     *
     * @param string $event_to_return Name of event to return.
     * @param string $uri
     * @param string $route_name Get events for specific route.
     * @return mixed 
     **/
    public static function events($event_to_return = null, $uri = null, $route_name = '')
    {
        $events = ActionRouter::events($uri, $route_name);
        return $event_to_return ? $events[$event_to_return] : $events;
    }
    
    /**
     * Return the an associative array of params or a particular param value.
     *
     * @param string $param_to_return Name of param to return.
     * @param string $uri
     * @return mixed 
     **/
    public static function params($param_to_return = null, $uri = null)
    {
        $params = array_merge($_GET, $_POST, (array) ActionRouter::params($uri));
        return $param_to_return ? $params[$param_to_return] : $params;
    }
    
    /**
     * Includes the required files for a controller and the controller helpers.
     *
     * @param string $controller_path Server path of controller to include.
     * @return void 
     **/
    public static function include_controller($controller_path)
    {
        try {
            // include application controller
            $controllers = array_merge(array('application'),
                                        explode(DS, $controller_path));
            
            $path = '';
            
            foreach ($controllers as $controller) {
                $class = $controller . '_controller';
                $controller_path = CONTROLLERS_PATH . $path . $class . '.php';
                $helper_path = HELPERS_PATH . $path . $controller .
                                '_helper.php';
                
                if (file_exists($controller_path)) {
                    require_once $controller_path;
                } else {
                    $controller_path = str_replace($class . '.php', '',
                                            $controller_path);
                    throw new Exception(str_replace(
                                ' ', '', Inflector::classify($class)) .
                    " not found in <strong>" . str_replace('_controller' .
                    '.php', '', $controller_path) . "</strong>.");
                }
                
                // include helper
                if (file_exists($helper_path)) require_once $helper_path;
                
                // append to path if a nested controller
                $path .= str_replace('application' . DS, '', $controller . DS);
            }
        } catch (Exception $e) {
            CREO('application_error_code', 404);
            CREO('application_error', $e);
        }
    }
    
    /**
     * Get framework base path. No mod_rewrite & subfolder fix.
     *
     * @return string
     **/
    public static function base_path()
    {
        $pattern = str_replace(
                    array('\\', '/public/' . $GLOBALS['CREOVEL']['DISPATCHER'], '/'),
                    array('/', '', '\/'),
                    $_SERVER['SCRIPT_NAME']
                    );
        if (empty($GLOBALS['CREOVEL']['ROUTING']['path'])) $GLOBALS['CREOVEL']['ROUTING']['path'] = '/';
        return preg_replace('/^'.$pattern.'/', '', str_replace('/public/'. $GLOBALS['CREOVEL']['DISPATCHER'], '', $GLOBALS['CREOVEL']['ROUTING']['path']));
    }
    
    /**
     * Get framework base url. Used for url_for().
     *
     * @return string
     **/
    public static function base_url()
    {
        $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
        if ( (!$GLOBALS['CREOVEL']['ROUTING']['base_path']
            || $GLOBALS['CREOVEL']['ROUTING']['base_path'] == '/')
            && CString::contains($script, CNetwork::url()) ) {
            return $script . '/';
        } else {
            if (CString::contains($script, CNetwork::url())) {
                $p = explode($GLOBALS['CREOVEL']['ROUTING']['base_path'],
                        CNetwork::url());
                return str_replace(CNetwork::http_host(), '', $p[0] . '/');
            } else {
                return str_replace(array('/public/' . $GLOBALS['CREOVEL']['DISPATCHER'], '/' . $GLOBALS['CREOVEL']['DISPATCHER']), '', $script) . '/';
            }
        }
    }
    
    /**
     * Do not process certain requests.
     *
     * @return void
     **/
    public static function ignore_check()
    {
        switch (true) {
            case @CString::contains('favicon.ico', $_SERVER['REQUEST_URI']):
            case @CString::contains('robots.txt', $_SERVER['REQUEST_URI']):
                exit(0);
                break;
        }
    }
    
    /**
     * Returns an array of the adapters available to the framework.
     *
     * @return array
     * @author Nesbert Hidalgo
     **/
    public function adapters()
    {
        return CDirectory::ls_with_file_name(CREOVEL_PATH.'adapters');
    }
    
    /**
     * Returns an array of the services available to the framework.
     *
     * @return array
     * @author Nesbert Hidalgo
     **/
    public function modules()
    {
        return CDirectory::ls_with_file_name(CREOVEL_PATH.'modules');
    }
    
    /**
     * Create a URL to view source of page. Used for when framework is 
     * in dev mode and viewing source set to "true".
     *
     * @access private
     * @param string $file
     * @return string
     * @author Nesbert Hidalgo
     **/
    public function source_url($file)
    {
        return $_SERVER['REQUEST_URI'] .
                    (strstr($_SERVER['REQUEST_URI'], '?') ? '&' : '?') .
                        'view_source=' . $file;
    }
    
    /**
     * Stops the application and display an error message or handle error
     * gracefully if not in development mode.
     *
     * @param string $message Error message
     * @param boolean $thow_exception Optional displays additional debugging info
     * @return mixed String or boolean
     * @author Nesbert Hidalgo
     **/
    public function error($message, $thow_exception = false)
    {
        if ($thow_exception) {
            $thow_exception = new Exception($message);
        }
        $GLOBALS['CREOVEL']['ERROR']->add($message, $thow_exception);
    }
    
    /**
     * Autoload routine for controllers, interfaces, adapters,
     * services, vendor, mailer and models.
     *
     * @param string $class
     * @author Nesbert Hidalgo
     **/
    public static function autoload($class)
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
                    $type = CString::contains('Mailer', $class) ? 'Mailer' : 'Model';
                    $path = MODELS_PATH . $class . '.php';
                    if (file_exists($path)) break;

                case (true):
                    $type = 'Shared';
                    @$path = SHARED_PATH . $class . '.php';
                    if (file_exists($path)) break;

                case (true):
                    $type = 'Vendor';
                    $path = VENDOR_PATH . $class . '.php';
                    if (file_exists($path)) break;

                case (true):
                    $type = 'Module';
                    $path = CREOVEL_PATH . 'modules' . DS . $class . '.php';
                    if (file_exists($path)) break;
                
                default:
                    $type = 'Model';
                    $path = MODELS_PATH . $class . '.php';
                    break;
            }
            
            if (file_exists($path)) {
                require_once $path;
            } else {
                $file = $class;
                if ($type == 'Controller') CREO('application_error_code', 404);
                if ($type == 'Controller' || $type == 'Model' || $type == 'Mailer') {
                    $folders = explode('/', $class);
                    foreach ($folders as $k => $v) {
                        $folders[$k] = Inflector::classify($v);
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
    
} // END class Creovel