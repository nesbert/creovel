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
     * Set frame events and params. Build controller execution environment.
     *
     * @return void
     **/
    public function run($events = null, $params = null, $return_as_str = false, $skip_init = false)
    {
        try {
            // start system
            include_once dirname(dirname(__FILE__)) .
                DIRECTORY_SEPARATOR . 'main.php';
            
            // gather up any output that occurs before output phase
            ob_start();

            // ignore certain requests
            self::ignore_check();
            
            // initialize web for web appliocations
            self::web();
            
            // set event and params
            $events = is_array($events) ? $events : self::events();
            $params = is_array($params) ? $params : self::params();
            
            // handle dashed controller names
            $events['controller'] = CString::underscore($events['controller']);
            
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
            $controller = CString::camelize($events['controller']) . 'Controller';
            
            if (class_exists($controller)) {
                $controller = new $controller();
            } else {
                throw new Exception("Class " . str_replace('Controller', '', $controller) .
                " not found in <strong>" . CString::classify($controller) . "</strong>");
            }
            
            // set controller properties
            $controller->__set_events($events);
            $controller->__set_params($params);
            
            // execute action
            $controller->__execute_action();
            
            // determine if any output has been sent to output buffer
            $buffer = ob_get_contents();
            
            // if buffer store in CREO varialbe
            if ($buffer) $GLOBALS['CREOVEL']['BUFFER'] = $buffer;

            // clean buffer
            ob_clean();
            
            // output to user
            return $controller->__output($return_as_str);
            
        } catch (Exception $e) {
            CREO('application_error_code', 404);
            CREO('application_error', $e);
        }
    }
    
    /**
     * Prepare framework for web applications by setting default routes and
     * set CREOVEL environment variables for web applications.
     *
     * @return void
     **/
    public function web()
    {
        // only run once
        static $initialized;
        if ($initialized) return $initialized;
        
        // Include framework base classes.
        require_once CREOVEL_PATH . 'classes/action_router.php';
        
        // Set routing defaults
        $GLOBALS['CREOVEL']['ROUTING'] = parse_url(CNetwork::url());
        $GLOBALS['CREOVEL']['ROUTING']['current'] = array();
        $GLOBALS['CREOVEL']['ROUTING']['routes'] = array();
        
        // set configuration settings
        self::config();
        
        // if global_xss_filtering is enabled
        if (!empty($GLOBALS['CREOVEL']['GLOBAL_XSS_FILTERING'])) {
            if (empty($GLOBALS['CREOVEL']['XSS_FILTERING_CALLBACK'])) {
                $xss_func = 'clean_array';
            } else {
                $xss_func = $GLOBALS['CREOVEL']['XSS_FILTERING_CALLBACK'];
            }
            // filter COOKIE, GET, POST, SERVER
            $_COOKIE = $xss_func($_COOKIE);
            $_GET = $xss_func($_GET);
            $_POST = $xss_func($_POST);
            $_SERVER = $xss_func($_SERVER);
        }
        
        // set additional routing options
        if (empty($GLOBALS['CREOVEL']['DISPATCHER'])) {
            $GLOBALS['CREOVEL']['DISPATCHER'] = basename($_SERVER['PHP_SELF']);
        }
        $GLOBALS['CREOVEL']['ROUTING']['base_path'] = self::base_path();
        $GLOBALS['CREOVEL']['ROUTING']['base_url'] = self::base_url();
        
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
        require_once CONFIG_PATH . 'routes.php';
        
        // Set session handler
        if ($GLOBALS['CREOVEL']['SESSION']) {
            
            if ($GLOBALS['CREOVEL']['SESSION'] === 'table') {
                // include/create session db object
                require_once CREOVEL_PATH . 'classes/active_session.php';
                $GLOBALS['CREOVEL']['SESSIONS_TABLE'] = 'active_sessions';
                $GLOBALS['CREOVEL']['SESSION_HANDLER'] = new ActiveSession;
            }
            
            // Fix for PHP 5.05
            // http://us2.php.net/manual/en/function.session-set-save-handler.php#61223
            register_shutdown_function('session_write_close');
            
            // start session
            if (session_id() == '') session_start();
        }
        
        return $initialized = true;
    }
    
    /**
     * Initialize framework for command line support.
     *
     * @return void
     **/
    public function cmd()
    {
        // start system
        include_once dirname(dirname(__FILE__)) .
            DIRECTORY_SEPARATOR . 'main.php';
            
        global $argc;
        global $argv;
        global $args;
        global $flags;
        global $params;
        
        // set flag for command line
        $GLOBALS['CREOVEL']['CMD'] = true;
        
        // set configuration settings
        self::config();
        
        // create local variables
        if ($argc > 1) {
            // set params & flagsforth argument and on
            // flags start with "-"
            $args = array();
            $flags = array();
            $params = array();
            foreach ($argv as $k => $v) {
                if (!$k) continue;
                if (CValidate::in_string(':', $v)) {
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
     * Read and set environment and databases files .
     *
     * @return void
     **/
    public function config()
    {
        // Include database setting filee
        require_once CONFIG_PATH . 'databases.php';
        // Include application config file
        require_once CONFIG_PATH . 'environment.php';
        // Include environment specific config file
        require_once CONFIG_PATH . 'environment' . DS . CREO('mode') . '.php';
    }
    
    /**
     * Return the an associative array of events or a particular event value.
     *
     * @param string $event_to_return Name of event to return.
     * @param string $uri
     * @param string $route_name Get events for specific route.
     * @return mixed 
     **/
    public function events($event_to_return = null, $uri = null, $route_name = '')
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
    public function params($param_to_return = null, $uri = null)
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
    public function include_controller($controller_path)
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
                                ' ', '', CString::humanize($class)) .
                    " not found in <strong>" . str_replace('_controller' .
                    '.php', '', $controller_path) . "</strong>");
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
    public function base_path()
    {
        $pattern = str_replace(
                    array('\\', '/public/' . $GLOBALS['CREOVEL']['DISPATCHER'], '/'),
                    array('/', '', '\/'),
                    $_SERVER['SCRIPT_NAME']
                    );
        return preg_replace('/^'.$pattern.'/', '', str_replace('/public/'. $GLOBALS['CREOVEL']['DISPATCHER'], '', $GLOBALS['CREOVEL']['ROUTING']['path']));
    }
    
    /**
     * Get framework base url. Used for url_for().
     *
     * @return string
     **/
    public function base_url()
    {
        $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
        if ( (!$GLOBALS['CREOVEL']['ROUTING']['base_path']
            || $GLOBALS['CREOVEL']['ROUTING']['base_path'] == '/')
            && CValidate::in_string($script, CNetwork::url()) ) {
            return $script . '/';
        } else {
            if (CValidate::in_string($script, url())) {
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
    public function ignore_check()
    {
        switch (true) {
            case CValidate::in_string('favicon.ico', $_SERVER['REQUEST_URI']):
            case CValidate::in_string('robots.txt', $_SERVER['REQUEST_URI']):
                exit(0);
                break;
        }
    }
} // END class Creovel