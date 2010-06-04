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
     * Storage resource.
     *
     * @var object
     **/
    private $r;
    
    /**
     * Storage type.
     *
     * @var string
     **/
    private $type = 'table';
    
    /**
     * undocumented function
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
        return is_object($this->r = ActiveRecord::table_object());
    }
    
    /**
     * Close the session.
     *
     * @return void
     **/
    public function close()
    {
        return @$this->r->disconnect();
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
        
        $this->r->query(sprintf("SELECT * FROM `{$GLOBALS['CREOVEL']['SESSIONS_TABLE']}` WHERE `id` = '%s';", $this->r->escape($id)));
        $result = $this->r->next();
        
        if ($this->r->total_rows() == 1) {
            return $result->data;
        } else {
            return "";
        }
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
        if (!$id) return false;
        
        $this->r->query(sprintf(
            "REPLACE INTO `{$GLOBALS['CREOVEL']['SESSIONS_TABLE']}` VALUES('%s', '%s', '%s');",
            $this->r->escape($id),
            CDate::datetime(time() + get_cfg_var("session.gc_maxlifetime")),
            $this->r->escape($val)
            ));
        
        return $this->r->affected_rows();
    }
    
    /**
     * Deletes session data.
     *
     * @param string $id Session ID.
     * @return integer
     **/
    public function destroy($id)
    {
        $this->r->query("DELETE FROM `{$GLOBALS['CREOVEL']['SESSIONS_TABLE']}` WHERE `id` = '" . $this->r->escape($id) . "'");
        return $this->r->affected_rows();
    }
    
    /**
     * Delete all expired rows from session table.
     *
     * @param maxlifetime Session max life time.
     * @return integer
     **/
    public function gc($maxlifetime)
    {
        $this->r->query("DELETE FROM `{$GLOBALS['CREOVEL']['SESSIONS_TABLE']}` WHERE `expires_at` < '" . CDate::datetime() . "';");
        return $this->r->affected_rows();
    }
    
    /**
     * Create sessions table if it doesn't exists.
     *
     * @param boolean $query_only
     * @return boolean
     **/
    public function create_table($query_only = false)
    {
        $sql = "CREATE TABLE IF NOT EXISTS `{$GLOBALS['CREOVEL']['SESSIONS_TABLE']}`
                (
                    `id` VARCHAR (255) NOT NULL,
                    `expires_at` datetime NOT NULL,
                    `data` TEXT NOT NULL,
                    PRIMARY KEY (`id`)
                );";
        if ($query_only) {
            return $sql;
        } else {
            return ActiveRecord::table_object()->query($sql);
        }
    }
} // END class Session extends CObject