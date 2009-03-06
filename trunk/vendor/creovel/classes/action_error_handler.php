<?php
/**
 * Creovel application error handler and debugger. Controls catchable
 * framework errors and allows for graceful recovery.
 *
 * @package     Creovel
 * @subpackage  Core
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.1.0
 * @author      Nesbert Hidalgo
 */
class ActionErrorHandler extends Object
{
    /**
     * Add/process application error.
     *
     * @param string/object $error Error message or Exception object.
     * @return void
     **/
    public function add(&$error)
    {
        $this->__process($error);
    }
    
    /**
     * Email application error or a custom error message to $emails.
     *
     * @param string $emails
     * @param string $error_message Optional error message
     * @param string $error_message Optional subject
     * @return boolean Returns false if email not sent
     **/
    public function email($emails, $error_message = '', $subject = '')
    {
        $message = '';
        
        if (!$subject) {
            $subject = 'Application Error: ' . url();
        }
        
        if ($error_message) {
            $message = $error_message . "\n\n";
        }
        
        $message .= print_r(array($_SERVER, $_SESSION), 1);
        
        return mail($emails, $subject, strip_tags($message));
    }
    
    /**
     * Process and/or display application errors to user.
     *
     * @param string/object $error Error message or Exception object.
     * @return void
     **/
    private function __process(&$error)
    {
        static $has_errored;
        
        if (is_object($error)) {
            $this->exception = $error;
            $this->message = $this->exception->getMessage();
        } else {
            $this->exception = '';
            $this->message = $error;
        }
        
        // log errors
        if (!empty($GLOBALS['CREOVEL']['LOG_ERRORS'])) {
            $log = new Log(LOG_PATH . CREO('mode') . '.log');
            $log->write(strip_tags($this->message));
        }
        
        // if command line show text errors
        if (!empty($GLOBALS['CREOVEL']['CMD'])) {
            include_once(CREOVEL_PATH . 'views' . DS . 'debugger' . DS . 'error_cli.php');
            die;
        }
        
        // check for custom errors
        $this->__custom_errors();
        
        // set header for error pages
        switch ($GLOBALS['CREOVEL']['ERROR_CODE']) {
            case '404':
                @header('Status: 404 Not Found', true, 404);
                $action = 'not_found';
                break;
            
            default:
                @header('Status: 500 Internal Server Error', true, 500);
                $action = 'general';
                break;
        }
        
        // prevent error from looping
        if (!$has_errored) {
            $has_errored = true;
            // grace fully handle errors in none devlopment mode
            if (CREO('mode') != 'development') {
                // get default error events
                $events = ActionRouter::error();
                if (isset($action)) $events['action'] = $action;
                // set params
                $params = array('error' => $this->message, 'exception' => $this->exception);
                // clean output buffer for application errors
                @ob_end_clean();
                Creovel::run($events, $params);
            } else {
                // show debugger
                $this->__debug();
            }
            die;
        }
        
        // show internal server error
        include_once(CREOVEL_PATH . 'views' . DS . 'layouts' . DS . 'apache_500_error.php');
        die;
    }
    
    /**
     * Show Creovel debugger and application settings and files.
     *
     * @return void
     **/
    private function __debug()
    {
        // clean output buffer for application errors
        @ob_end_clean();
        
        if (CREO('show_source')
            && isset($_GET['view_source'])
            && in_string(BASE_PATH, $_GET['view_source'])) {
            // show source view on application error
            ActionView::show(CREOVEL_PATH . 'views' . DS . 'debugger' . DS .
                            'view_source.php',
                            CREOVEL_PATH . 'views' . DS . 'layouts' . DS .
                            'debugger.php');
        } else {
            // else show source view on application error
            ActionView::show(CREOVEL_PATH . 'views' . DS . 'debugger' . DS .
                            'error.php',
                            CREOVEL_PATH . 'views' . DS . 'layouts' . DS .
                            'debugger.php');
        }
    }
    
    /**
     * undocumented function
     *
     * @return void
     * @author Nesbert Hidalgo
     **/
    private function __custom_errors()
    {
        if (in_string(".active_sessions' doesn't exist", $this->message)) {
            ActiveSession::create_table();
            $this->message .= " The following Query has been executed: \"". ActiveSession::create_table(1) ."\". You should not see this message again.";
        }
    }
} // END class ActionErrorHandler extends Object