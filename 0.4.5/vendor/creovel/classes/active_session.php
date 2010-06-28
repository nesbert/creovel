<?php
/**
 * Table session class and helpers functions.
 *
 * @package     Creovel
 * @subpackage  Core
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.1.0 
 **/
class ActiveSession extends CObject
{
    /**
     * Table name.
     *
     * @var string
     **/
    public static $_table_name_ = 'active_sessions';

    /**
     * Storage resource.
     *
     * @var object
     **/
    private $__r;
    
    /**
     * Class construct to set session save handlers.
     *
     * @return void
     * @author Nesbert Hidalgo
     **/
    public function __construct()
    {
        ini_set('session.save_handler', 'user');
        
        session_set_save_handler(
            array(&$this, 'open'),
            array(&$this, 'close'),
            array(&$this, 'read'),
            array(&$this, 'write'),
            array(&$this, 'destroy'),
            array(&$this, 'gc')
            );
    }
    
    /**
     * Open the session.
     *
     * @return void
     **/
    public function open()
    {
        $this->__r = new ActiveQuery;
        return is_object($this->__r->connect());
    }
    
    /**
     * Close the session.
     *
     * @return void
     **/
    public function close()
    {
        return !empty($this->__r) && $this->__r->disconnect();
    }
    
    /**
     * Get session data.
     *
     * @param string $id Session ID.
     * @return string
     **/
    public function read($id)
    {
        if (!$id) return false;
        
        $result = $this->__r->find_row(
            $GLOBALS['CREOVEL']['SESSIONS_TABLE'],
            array('conditions' => array('ID' => $id))
            );
        
        return $result ? $result->DATA : '';
    }
    
    /**
     * Sets session data.
     *
     * @param string $id Session ID.
     * @param string $val Session value.
     * @return integer
     **/
    public function write($id = false, $val = '')
    {
        if (empty($id)) return false;
        
        $expires = CDate::datetime(time() + ini_get('session.gc_maxlifetime'));
        
        $columns = array(
            'ID' => $id,
            'EXPIRES' => $expires,
            'DATA' => $val
            );
        
        // find session
        $this->read($id);
        
        if ($this->__r->total_rows() == 1) {
            unset($columns['id']);
            $affected_rows = $this->__r->update_row(
                $GLOBALS['CREOVEL']['SESSIONS_TABLE'],
                $columns,
                array('ID' => $id)
                );
        } else {
            $affected_rows = $this->__r->insert_row(
                $GLOBALS['CREOVEL']['SESSIONS_TABLE'],
                $columns
                );
        }
        
        return $affected_rows;
    }
    
    /**
     * Deletes session data.
     *
     * @param string $id Session ID.
     * @return integer
     **/
    public function destroy($id)
    {
        $affected_rows = $this->__r->delete(
            $GLOBALS['CREOVEL']['SESSIONS_TABLE'],
            array('ID' => $id)
            );
        return $affected_rows;
    }
    
    /**
     * Delete all expired rows from session table.
     *
     * @param integer $maxlifetime Session max life time.
     * @return integer
     **/
    public function gc($maxlifetime)
    {
        $affected_rows = $this->__r->delete(
            $GLOBALS['CREOVEL']['SESSIONS_TABLE'],
            array(
                "EXPIRES < :EXPIRES",
                array('EXPIRES' => CDate::datetime())
                )
            );
        return $affected_rows;
    }
    
    /**
     * Create sessions table if it doesn't exists.
     *
     * @param boolean $query_only
     * @return boolean
     **/
    public function create_table($query_only = false)
    {
        $sql = "CREATE TABLE {$GLOBALS['CREOVEL']['SESSIONS_TABLE']}
                (
                    ID VARCHAR (255) NOT NULL,
                    EXPIRES TIMESTAMP NOT NULL,
                    DATA TEXT NOT NULL,
                    PRIMARY KEY (ID)
                );";
        if ($query_only) {
            return $sql;
        } else {
            $a = new ActiveQuery;
            return $a->query($sql);
        }
    }
    
    /**
     * Start session if $GLOBALS['CREOVEL']['SESSION'] is
     * set to true or 'table'.
     *
     * @return void
     **/
    static public function start()
    {
        if ($GLOBALS['CREOVEL']['SESSION']) {
            
            if ($GLOBALS['CREOVEL']['SESSION'] === 'table') {
                // include/create session db object
                require_once CREOVEL_PATH . 'classes/active_session.php';
                $GLOBALS['CREOVEL']['SESSIONS_TABLE'] = self::$_table_name_;
                $GLOBALS['CREOVEL']['SESSION_HANDLER'] = new ActiveSession;
            }
            
            // Fix for PHP 5.05
            // http://us2.php.net/manual/en/function.session-set-save-handler.php#61223
            register_shutdown_function('session_write_close');
            
            // start session
            if (session_id() == '') session_start();
        }
    }
} // END class Session extends CObject