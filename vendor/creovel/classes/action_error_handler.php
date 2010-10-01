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
class ActionErrorHandler extends CObject
{
    /**
     * Add/process application error.
     *
     * @param string/object $error Error message or Exception object.
     * @return void
     **/
    public function add($error)
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
            $subject = 'Application Error: ' . CNetwork::url();
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
    private function __process($error)
    {
        if (is_object($error)) {
            $this->exception = $error;
            $this->message = $this->exception->getMessage();
        } else {
            $this->exception = '';
            $this->message = $error;
        }
        
        // log errors
        if ($GLOBALS['CREOVEL']['LOG_ERRORS']) {
            CREO('log', 'Error: ' . $this->message);
        }
        
        static $has_errored;
        // prevent error from ever looping
        if ($has_errored) {
            if (!$GLOBALS['CREOVEL']['CLI']) {
                // show internal server error
                include_once CREOVEL_PATH . 'views' . DS . 'layouts' . DS . 'apache_500_error.php';
            }
        
            exit(2);
        }
        
        // check for custom errors
        $this->__custom_errors();
        
        // set header for error pages
        switch ($GLOBALS['CREOVEL']['APPLICATION_ERROR_CODE']) {
            case '401':
                @header('Status: 401 Unauthorized', true, 401);
                $action = 'unauthorized';
                break;
                
            case '404':
                @header('Status: 404 Not Found', true, 404);
                $action = 'not_found';
                break;
            
            default:
                @header('Status: 500 Internal Server Error', true, 500);
                $action = 'general';
                break;
        }
        
        $has_errored = true;
        // if command line show text errors
        if ($GLOBALS['CREOVEL']['CLI'] || CValidate::ajax()) {
            // only show erros in dev mode
            if (CREO('mode') == 'development') {
                @header('Content-Type: text/plain; charset=utf-8');
                include_once CREOVEL_PATH . 'views' . DS . 'debugger' . DS . 'error_cli.php';
            }
        // grace fully handle errors in none devlopment mode
        } else if (CREO('mode') != 'development') {
            // get default error events
            $events = ActionRouter::error();
            if (isset($action)) $events['action'] = $action;
            
            // set params
            $params = (array) Creovel::params() + array('error' => $this->message, 'exception' => $this->exception);
            // clean output buffer for application errors
            @ob_end_clean();
            Creovel::web($events, $params);
        } else {
            // show debugger
            $this->__debug();
        }
        
        exit(1);
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
            && CString::contains(BASE_PATH, $_GET['view_source'])) {
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
     * Catch certain errors and see if we can correct them.
     *
     * @return void
     **/
    private function __custom_errors()
    {
        if (CString::contains(".active_sessions' doesn't exist", $this->message)) {
            ActiveSession::create_table();
            $this->message .= " The following Query has been executed: \"". ActiveSession::create_table(1) ."\". You should not see this message again.";
        }
    }
} // END class ActionErrorHandler extends CObject