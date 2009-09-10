<?php
/**
 * Set constants, include helpers, configuration files & core classes,
 * amp default routes and set CREOVEL & environment variables.
 **/

// If not PHP 5 stop.
if (PHP_VERSION <= 5) {
    die('Creovel requires PHP >= 5!');
}

// Define creovel constants.
define('CREOVEL_VERSION', '0.4.2');
define('CREOVEL_RELEASE_DATE', '2009-03-05 22:26:55');

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

// Include base helper libraries.
require_once CREOVEL_PATH . 'helpers/datetime.php';
require_once CREOVEL_PATH . 'helpers/form.php';
require_once CREOVEL_PATH . 'helpers/framework.php';
require_once CREOVEL_PATH . 'helpers/general.php';
require_once CREOVEL_PATH . 'helpers/html.php';
require_once CREOVEL_PATH . 'helpers/locale.php';
require_once CREOVEL_PATH . 'helpers/server.php';
require_once CREOVEL_PATH . 'helpers/text.php';
require_once CREOVEL_PATH . 'helpers/validation.php';

// Include application_helper
if (file_exists($helper = HELPERS_PATH . 'application_helper.php')) {
    require_once $helper;
}

// Include minimum base classes.
require_once CREOVEL_PATH . 'classes/object.php';
require_once CREOVEL_PATH . 'modules/module_base.php';
require_once CREOVEL_PATH . 'modules/inflector.php';

// Set default creovel global vars.
$GLOBALS['CREOVEL']['MODE'] = 'production';
$GLOBALS['CREOVEL']['LOG_ERRORS'] = false;

// set error handler
require_once CREOVEL_PATH . 'classes/action_error_handler.php';
$GLOBALS['CREOVEL']['ERROR'] = new ActionErrorHandler;
$GLOBALS['CREOVEL']['ERROR_CODE'] = '';
$GLOBALS['CREOVEL']['VALIDATION_ERRORS'] = array();

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
            // ignore certain requests
            self::ignore_check();
            
            // initialize web for web appliocations
            self::web();
            
            // set event and params
            $events = is_array($events) ? $events : self::events();
            $params = is_array($params) ? $params : self::params();
            
            // handle dashed controller names
            $events['controller'] = underscore($events['controller']);
            
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
            $controller = camelize($events['controller']) . 'Controller';
            
            if (class_exists($controller)) {
                $controller = new $controller();
            } else {
                throw new Exception("Class " . str_replace('Controller', '', $controller) .
                " not found in <strong>" . classify($controller) . "</strong>");
            }
            
            // set controller properties
            $controller->__set_events($events);
            $controller->__set_params($params);
            
            // execute action
            $controller->__execute_action();
            
            // output to user
            return $controller->__output($return_as_str);
            
        } catch (Exception $e) {
            CREO('error_code', 404);
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
        require_once CREOVEL_PATH . 'classes/action_controller.php';
        require_once CREOVEL_PATH . 'classes/action_view.php';
        require_once CREOVEL_PATH . 'classes/action_router.php';
        
        // Set default creovel global vars.
        $GLOBALS['CREOVEL']['DEFAULT_CONTROLLER'] = 'index';
        $GLOBALS['CREOVEL']['DEFAULT_ACTION'] = 'index';
        $GLOBALS['CREOVEL']['DEFAULT_LAYOUT'] = 'default';
        $GLOBALS['CREOVEL']['VIEW_EXTENSION'] = 'html';
        $GLOBALS['CREOVEL']['HTML_APPEND'] = false;
        $GLOBALS['CREOVEL']['PAGE_CONTENTS'] = '@@page_contents@@';
        $GLOBALS['CREOVEL']['SESSION'] = true;
        $GLOBALS['CREOVEL']['SHOW_SOURCE'] = false;
        
        // Set routing defaults
        $GLOBALS['CREOVEL']['ROUTING'] = parse_url(url());
        $GLOBALS['CREOVEL']['ROUTING']['current'] = array();
        $GLOBALS['CREOVEL']['ROUTING']['routes'] = array();
        
        // set configuration settings
        self::config();
        
        // set additional routing options
        if (empty($GLOBALS['CREOVEL']['DISPATCHER'])) $GLOBALS['CREOVEL']['DISPATCHER'] = 'index.php';
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
                if (in_string(':', $v)) {
                    $v  = explode(':', $v);
                    $params[$v[0]] = $v[1];
                } else if (starts_with('-', $v)) {
                    // double dash mean whole words
                    if (starts_with('--', $v)) {
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
     * @return mixed 
     **/
    public function events($event_to_return = null, $uri = null)
    {
        $events = ActionRouter::which($uri);
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
        $params = array_merge($_GET, $_POST, ActionRouter::which($uri, true));
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
                    throw new Exception(str_replace(' ', '', humanize($class)) .
                    " not found in <strong>" . str_replace('_controller' .
                    '.php', '', $controller_path) . "</strong>");
                }
                
                // include helper
                if (file_exists($helper_path)) require_once $helper_path;
                
                // append to path if a nested controller
                $path .= str_replace('application' . DS, '', $controller . DS);
            }
        } catch (Exception $e) {
            CREO('error_code', 404);
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
        if ( (!$GLOBALS['CREOVEL']['ROUTING']['base_path'] || $GLOBALS['CREOVEL']['ROUTING']['base_path'] == '/') && in_string($script, url()) ) {
            return $script . '/';
        } else {
            if (in_string($script, url())) {
                $p = explode($GLOBALS['CREOVEL']['ROUTING']['base_path'], url());
                return str_replace(http_host(), '', $p[0] . '/');
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
            case in_string('favicon.ico', $_SERVER['REQUEST_URI']):
            case in_string('robots.txt', $_SERVER['REQUEST_URI']):
                exit(0);
                break;
        }
    }
} // END class Creovel