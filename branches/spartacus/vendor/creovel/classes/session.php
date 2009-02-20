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
     * Create/use DB object and.
     *
     * @return object
     **/
    public function db()
    {
        static $db;
        
        if (empty($db)) {
            $db = ActiveRecord::table_object();
        }
        return $db;
    }
    
    /**
     * Create sessions table if it doesn't exists.
     *
     * @return boolean
     **/
    public function create_table()
    {
        return self::db()->query(
            "CREATE TABLE IF NOT EXISTS {$GLOBALS['CREOVEL']['SESSIONS_TABLE']}
                (
                    id VARCHAR (255) NOT NULL,
                    expires_at datetime NOT NULL,
                    data TEXT NOT NULL,
                    PRIMARY KEY (id)
                )");
    }
    
    /**
     * Open the session.
     *
     * @return void
     **/
    public function open()
    {
        return self::db()->db ? true : false;
    }
    
    /**
     * Close the session.
     *
     * @return void
     **/
    public function close()
    {
        return self::db()->disconnect();
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
        
        self::db()->query(sprintf("SELECT * FROM `{$GLOBALS['CREOVEL']['SESSIONS_TABLE']}` WHERE `id` = '%s';", self::db()->escape($id)));
        $result = self::db()->next();
        
        if (self::db()->total_rows() == 1) {
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
        
        self::db()->query(sprintf("REPLACE INTO `{$GLOBALS['CREOVEL']['SESSIONS_TABLE']}` VALUES('%s', '%s', '%s')",
            self::db()->escape($id),
            datetime(time() + get_cfg_var("session.gc_maxlifetime")),
            self::db()->escape($val)
            ));
        
        return self::db()->affected_rows();
    }
    
    /**
     * Deletes session data.
     *
     * @param string $id Session ID.
     * @return integer
     **/
    public function destroy($id)
    {
        self::db()->query("DELETE FROM `{$GLOBALS['CREOVEL']['SESSIONS_TABLE']}` WHERE `id` = '" . self::db()->escape($id) . "'");
        return self::db()->affected_rows();
    }
    
    /**
     * Delete all expired rows from session table.
     *
     * @param maxlifetime Session max life time.
     * @return integer
     **/
    public function gc($maxlifetime)
    {
        self::db()->query("DELETE FROM `{$GLOBALS['CREOVEL']['SESSIONS_TABLE']}` WHERE `expires_at` < '" . datetime() . "';");
        return self::db()->affected_rows();
    }
} // END class Session extends Object