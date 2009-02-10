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
     * @return boolean
     * @author Nesbert Hidalgo
     **/
    public static function initialize()
    {
        // If not PHP 5 stop.
        if (PHP_VERSION <= 5) {
            die('Creovel requires PHP >= 5!');
        }
        
        // Define creovel constants.
        define('CREOVEL_VERSION', '0.4 (sparatacus)');
        define('CREOVEL_RELEASE_DATE', '2008-07-02 22:55:55');
        
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
        require_once CREOVEL_PATH . 'classes/inflector.php';
        
        return true;
    }
    
    /**
     * Prepare framework for web applications by setting default routes and
     * set CREOVEL environment variables for web applications.
     *
     * @return void
     **/
    public function webapp()
    {
        // Include framework base classes.
        require_once CREOVEL_PATH . 'classes/action_controller.php';
        require_once CREOVEL_PATH . 'classes/action_view.php';
        require_once CREOVEL_PATH . 'classes/action_error_handler.php';
        require_once CREOVEL_PATH . 'classes/action_router.php';
        
        // Set default creovel global vars.
        $GLOBALS['CREOVEL']['DEFAULT_CONTROLLER'] = 'index';
        $GLOBALS['CREOVEL']['DEFAULT_ACTION'] = 'index';
        $GLOBALS['CREOVEL']['DEFAULT_LAYOUT'] = 'default';
        $GLOBALS['CREOVEL']['ERROR'] = new ActionErrorHandler;
        $GLOBALS['CREOVEL']['HTML_APPEND'] = false;
        $GLOBALS['CREOVEL']['MODE'] = 'production';
        $GLOBALS['CREOVEL']['PAGE_CONTENTS'] = '@@page_contents@@';
        $GLOBALS['CREOVEL']['SESSION'] = true;
        $GLOBALS['CREOVEL']['SHOW_SOURCE'] = false;
        
        // Set routing defaults
        $GLOBALS['CREOVEL']['ROUTING'] = parse_url(url());
        $GLOBALS['CREOVEL']['ROUTING']['current'] = array();
        $GLOBALS['CREOVEL']['ROUTING']['routes'] = array();
        
        // Include application config files
        require_once CONFIG_PATH . 'environment.php';
        require_once CONFIG_PATH . 'environment' . DS . CREO('mode') . '.php';
        
        // Include application config files
        require_once CONFIG_PATH.'databases.php';
        
        // Set session handler
        if ($GLOBALS['CREOVEL']['SESSION']) {
            session_start();
        }
        
        // Set default route
        ActionRouter::map('default', '/:controller/:action/*', array(
                    'controller' => 'index',
                    'action' => 'index'
                    ));
        
        // Set default error route
        ActionRouter::map('default_error', '/errors/:action/*', array(
                    'controller' => 'errors',
                    'action' => 'general'
                    ));
        
        // Include custom routes
        require_once CONFIG_PATH . 'routes.php';
    }
    
    /**
     * Set frame events and params. Build controller execution environment.
     *
     * @return void
     **/
    public function run($events = null, $params = null, $return_as_str = false)
    {
        // initialize framework
        self::initialize();
        
        // initialize web for web appliocations
        self::webapp();
        
        // set event and params
        $events = $events ? $events : self::events();
        $params = $params ? $params : self::params();
        
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
        $controller = humanize($events['controller']) . 'Controller';
        $controller = new $controller();
        
        // set controller properties
        $controller->__set_events($events);
        $controller->__set_params($params);
        
        // execute action
        $controller->__execute_action();
        
        // output to user
        return $controller->__output($return_as_str);
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
} // END class Creovel