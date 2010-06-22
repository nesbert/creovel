<?php
/**
 * ORM MySQLi Adapter.
 *
 * @package     Creovel
 * @subpackage  Adapters
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.0
 * @author      Nesbert Hidalgo
 */
class MysqlImproved extends AdapterBase
{
    /**
     * Opens a connection to the MySQL Server with $db_properties an
     * array of database settings.
     *
     * @param array $db_properties
     * @return void
     **/
    public function connect($db_properties)
    {
        if (empty($db_properties['host'])
            || empty($db_properties['username'])
            || empty($db_properties['password'])) {
            self::throw_error('Could not connect to server because of '.
                'missing arguments for $db_properties.');
            
        }
        
        if (!empty($db_properties['persistent'])
            && PHP_VERSION >= '5.3') {
            $db_properties['host'] = 'p:'.$db_properties['host'];
        }
        
        // open a connection to a MySQL Server and set db_link
        $this->db = @new mysqli(
            $db_properties['host'],
            $db_properties['username'],
            $db_properties['password'],
            !empty($db_properties['database']) ? $db_properties['database'] : null,
            !empty($db_properties['port']) ? $db_properties['port'] : null,
            !empty($db_properties['socket']) ? $db_properties['socket'] : null
            );
        
        if (mysqli_connect_error()) {
            self::throw_error(mysqli_connect_error() . '.');
        }
    }
    
    /**
     * Close current connection to the MySQL Server.
     *
     * @return void
     **/
    public function disconnect()
    {
        // close result resource
        $this->close();
        
        // close MySQL connection
        if (isset($this->db) && is_object($this->db)) {
            return $this->db->close();
        }
    }
    
    /**
     * Execute query and return result object/resource or false. Option to log
     * log queries if $GLOBALS['CREOVEL']['LOG_QUERIES'] is set to true. All
     * queries should pass through this function.
     *
     * @param string $query SQL string
     * @return object/false
     **/
    public function execute($query)
    {
        $result = $this->db->query($query);
        
        // log queries
        if (!empty($GLOBALS['CREOVEL']['LOG_QUERIES'])) {
            CREO('log', "Query: {$query}");
        }
        
        if (!$result) {
            self::throw_error("{$this->db->error} Query \"" .
            str_replace(', ', ",\n", $query) . "\" failed. #{$this->db->errno}");
        }
        
        return $result;
    }
    
    /**
     * Performs a query on the database and sets result resources.
     *
     * @param string $query SQL string
     * @return object/resource Result
     **/
    public function query($query)
    {
        // reset class properties
        $this->reset();
        
        // set database property
        $this->query = $query;
        
        // send a MySQL query and set query_link resource on success
        return $this->result = $this->execute($query);
    }
    
    /**
     * Free result resource.
     *
     * @return void
     **/
    public function close()
    {
        if (isset($this->result) && is_object($this->result)) {
            $this->free_result();
        }
    }
    
    /**
     * Returns an associative array that corresponds to the fetched row
     * or NULL if there are no more rows.
     *
     * @return object
     **/
    public function get_row()
    {
        if ($this->valid()) {
            return $this->result->fetch_object();
        } else {
            return false;
        }
    }
    
    /**
     * Returns an object modeled by the current table structure.
     *
     * @param string $table_name
     * @return object
     */
    public function columns($table_name)
    {
        // send a DESCRIBE query and set result on success
        $sql = "DESCRIBE `{$table_name}`;";
        $result = $this->execute($sql);
        
        // set fields object to return
        $fields = array();
        
        // foreach row in results insert into fields object
        while ($row = @$result->fetch_assoc()) {
            
            // set fields into an associative array
            foreach ($row as $key => $value) {
                if ($key != 'Field') {
                    $temp_arr[strtolower($key)] = $value;
                }
            }
            // get default value for field
            if ($row['Default'] !== 'NULL') {
                $temp_arr['value'] = $row['Default'];
            } else {
                $temp_arr['value'] = null;
            }
            
            // set property in fields object
            $fields[$row['Field']] = (object) $temp_arr;
        }
        
        $result->close();
        
        return $fields;
    }
    
    /**
     * Returns the number of row(s) from a result set after a query.
     *
     * @return integer
     */
    public function total_rows()
    {
        return @$this->result->num_rows;
    }
    
    /**
     * Returns the number of row(s) affect by a query (eg. UPDATE, DELETE).
     *
     * @return integer
     */
    public function affected_rows()
    {
        return @$this->db->affected_rows;
    }
    
    /**
     * Returns the id of the row just inserted.
     *
     * @return integer
     */
    public function insert_id()
    {
        return @$this->db->insert_id;
    }
    
    /**
     * Escapes any bad characters for query string.
     *
     * @param string $string
     * @return string
     */
    public function escape($string)
    {
        return @$this->db->real_escape_string($string);
    }
    
    /**
     * Resets DB properties and frees result resources.
     *
     * @return void
     **/
    public function reset()
    {
        parent::reset();
    }
    
    /**
     * Free results resource.
     *
     * @return void
     **/
    public function free_result()
    {
        $this->result->close();
        $this->result = null;
    }
    
    /**
     * Iterator methods.
     */
    
    /**
     * Adjusts the result pointer to an arbitrary row in the result
     * and returns TRUE on success or FALSE on failure.
     *
     * @return boolean
     **/
    public function valid()
    {
        if ($this->result) {
            return $this->result->data_seek($this->offset);
        } else {
            return false;
        }
    }
    
    /**
     * Transaction methods. Will revisit later base methods still work
     * as usual with MySQLi.
     */
    
    // /**
    //  * BEGIN transaction.
    //  *
    //  * @return void
    //  **/
    // public function start_tran()
    // {
    //     $this->db->autocommit(false);
    //     #$this->execute('START TRANSACTION;');
    // }
    // 
    // /**
    //  * ROLLBACK transaction.
    //  *
    //  * @return void
    //  **/
    // public function rollback()
    // {
    //     $this->db->rollback();
    //     #$this->execute('ROLLBACK;');
    //     $this->db->autocommit(true);
    // }
    // 
    // /**
    //  * COMMIT transaction.
    //  *
    //  * @return void
    //  **/
    // public function commit()
    // {
    //     $this->db->commit();
    //     #$this->execute('COMMIT;');
    //     $this->db->autocommit(true);
    // }
} // END class MysqlImproved extends AdapterBase