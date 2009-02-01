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
class ActionErrorHandler
{
    /**
     * Add/process application error.
     *
     * @param string/object $error Error message or Exception object.
     * @return void
     **/
    public function add(&$error)
    {
        $this->process($error);
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
    private function process(&$error)
    {
        if (is_object($error)) {
            $this->exception = $error;
            $this->message = $this->exception->getMessage();
        } else {
            $this->exception = '';
            $this->message = $error;
        }
        
        // set header for error pages
        $code = isset($GLOBALS['CREOVEL']['ERROR_CODE'])
                    ? $GLOBALS['CREOVEL']['ERROR_CODE']
                    : '';
        
        switch ($code) {
            case '404':
                @header('Status: 404 Not Found', true, 404);
                $action = 'not_found';
                break;
            
            default:
                @header('Status: 500 Internal Server Error', true, 500);
                $action = 'general';
                break;
        }
        
        // grace fully handle errors in none devlopment mode
        if (CREO('mode') != 'development') {
            // get default error events
            $events = ActionRouter::error();
            if (isset($action)) $events['action'] = $action;
            // clean output buffer for application errors
            @ob_end_clean();
            Creovel::run($events, array(
                                'error' => $this->message,
                                'exception' => $this->exception
                                ));
            die;
        }
        
        // show debugger
        $this->__debug();
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
        die;
    }
} // END class ActionErrorHandler