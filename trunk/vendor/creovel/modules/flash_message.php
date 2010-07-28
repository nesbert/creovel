<?php
/**
 * Session message used for notices.
 *
 * @package     Creovel
 * @subpackage  Modules
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.5
 * @author      Nesbert Hidalgo
 **/
class FlashMessage extends ModuleBase
{
    public $message;
    public $type = 'notice';
    public $checked = false;
    
    /**
     * Returns the $_SESSION['FLASHMESSAGE']['type'].
     *
     * @return string
     **/
    public static function type()
    {
        return !empty($_SESSION['FLASHMESSAGE']->type)
            ? $_SESSION['FLASHMESSAGE']->type : 'notice';
    }
    
    /**
     * Sets and unsets $_SESSION['FLASHMESSAGE']. Used by application notices.
     *
     * @param string $message Optional string to be displayed.
     * @param string $type - Type of notice passed
     * @return bool/string String or message
     **/
    public static function notice($message = null, $type = 'notice')
    {
        if ($message || isset($_SESSION['FLASHMESSAGE']->message)) {
            
            if ($message) {
                
                $_SESSION['FLASHMESSAGE'] = new FlashMessage;
                $_SESSION['FLASHMESSAGE']->message = $message;
                $_SESSION['FLASHMESSAGE']->type = $type;
                $_SESSION['FLASHMESSAGE']->checked = false;
            
            } elseif ($_SESSION['FLASHMESSAGE']->checked === false) {
            
                $_SESSION['FLASHMESSAGE']->checked = true;
                return true;
            
            } else {
            
                $message = $_SESSION['FLASHMESSAGE']->message;
                unset($_SESSION['FLASHMESSAGE']);
                return $message;
            
            }
            
        } else {
            
            return false;
        
        }
    }
    
    /**
     * Alias for self::message($message, 'error').
     *
     * @param string $message Message for flash notice
     * @return mixed String or boolean
     **/
    public static function error($message = null)
    {
        return self::notice($message, 'error');
    }
    
    /**
     * Alias for self::message($message, 'warning').
     *
     * @param string $message Message for flash notice
     * @return mixed String or boolean
     **/
    public static function warning($message = null)
    {
        return self::notice($message, 'warning');
    }
    
    /**
     * Alias for self::message($message, 'sucess').
     *
     * @param string $message Message for flash notice
     * @return mixed String or boolean
     **/
    public static function success($message = null)
    {
        return self::notice($message, 'success');
    }

} // END class FlashMessage extends ModuleBase