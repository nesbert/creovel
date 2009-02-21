<?php
/**
 * Table session class and helpers functions.
 *
 * @package     Creovel
 * @subpackage  Core
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.1.0 
 **/
class Session extends Object
{
    /**
     * Storage resource.
     *
     * @var object
     **/
    public static $r;
    
    /**
     * Create sessions table if it doesn't exists.
     *
     * @param boolean $query_only
     * @return boolean
     **/
    public function create_table($query_only = false)
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$GLOBALS['CREOVEL']['SESSIONS_TABLE']}
                (
                    id VARCHAR (255) NOT NULL,
                    expires_at datetime NOT NULL,
                    data TEXT NOT NULL,
                    PRIMARY KEY (id)
                );";
        if ($query_only) {
            return $sql;
        } else {
            return self::$r->query($sql);
        }
    }
    
    /**
     * Open the session.
     *
     * @return void
     **/
    public function open()
    {
        self::$r = ActiveRecord::table_object();
        return is_resource(self::$r);
    }
    
    /**
     * Close the session.
     *
     * @return void
     **/
    public function close()
    {
        return true;
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
        
        self::$r->query(sprintf("SELECT * FROM `{$GLOBALS['CREOVEL']['SESSIONS_TABLE']}` WHERE `id` = '%s';", self::$r->escape($id)));
        $result = self::$r->next();
        
        if (self::$r->total_rows() == 1) {
            return $result['data'];
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
        
        self::$r->query(sprintf("REPLACE INTO `{$GLOBALS['CREOVEL']['SESSIONS_TABLE']}` VALUES('%s', '%s', '%s')",
            self::$r->escape($id),
            datetime(time() + get_cfg_var("session.gc_maxlifetime")),
            self::$r->escape($val)
            ));
        
        return self::$r->affected_rows();
    }
    
    /**
     * Deletes session data.
     *
     * @param string $id Session ID.
     * @return integer
     **/
    public function destroy($id)
    {
        self::$r->query("DELETE FROM `{$GLOBALS['CREOVEL']['SESSIONS_TABLE']}` WHERE `id` = '" . self::$r->escape($id) . "'");
        return self::$r->affected_rows();
    }
    
    /**
     * Delete all expired rows from session table.
     *
     * @param maxlifetime Session max life time.
     * @return integer
     **/
    public function gc($maxlifetime)
    {
        self::$r->query("DELETE FROM `{$GLOBALS['CREOVEL']['SESSIONS_TABLE']}` WHERE `expires_at` < '" . datetime() . "';");
        return self::$r->affected_rows();
    }
} // END class Session extends Object