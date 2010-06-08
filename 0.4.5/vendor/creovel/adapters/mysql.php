<?php
/**
 * ORM MySQL Adapter.
 *
 * @package     Creovel
 * @subpackage  Adapters
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.0
 * @author      Nesbert Hidalgo
 */
class Mysql extends AdapterBase
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
        
        $server = $db_properties['host'];
        
        if (!empty($db_properties['port'])) {
            $server .= ':' . $db_properties['port'];
        }
        
        if (!empty($db_properties['socket'])) {
            $server = $db_properties['socket'];
        }
        
        // open a connection to a MySQL Server and set db_link
        if (empty($db_properties['persistent'])) {
            $this->db = @mysql_connect(
                $server,
                $db_properties['username'],
                $db_properties['password']
                );
        } else {
            $this->db = @mysql_pconnect(
                $server,
                $db_properties['username'],
                $db_properties['password']
                );
        }
        
        if (!$this->db) {
            self::throw_error("Could not connect to server ({$server}). " .
                                mysql_error() . '.');
        }
        
        if (!empty($db_properties['database'])
            && !mysql_select_db($db_properties['database'], $this->db)) {
            self::throw_error(mysql_error() . '.');
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
        if (isset($this->db) && is_resource($this->db)) {
            return mysql_close($this->db);
        }
    }
    
    /**
     * Execute query and return result object/resource or false. Option to
     * log queries if $GLOBALS['CREOVEL']['LOG_QUERIES'] is set to true. All
     * queries should pass through this function.
     *
     * @param string $query SQL string
     * @return object/false
     **/
    public function execute($query)
    {
        if (!is_resource($this->db)) {
            self::throw_error("Could not connect to server to execute query.");
        }
        
        $result = mysql_query($query, $this->db);
        
        // log queries
        if (!empty($GLOBALS['CREOVEL']['LOG_QUERIES'])) {
            CREO('log', "Query: {$query}");
        }
        
        if (!$result) {
            self::throw_error(mysql_error() . " Query \"" .
            str_replace(', ', ",\n", $query) . "\" failed. #" . mysql_errno($this->db));
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
        if (isset($this->result) && is_resource($this->result)) {
            $this->free_result($this->result);
        }
    }
    
    /**
     * Returns an object that corresponds to the fetched row
     * or false if there are no more rows.
     *
     * @return object||false
     **/
    public function get_row($result = null)
    {
        if ($result) {
            return mysql_fetch_object($result);
        } else {
            if ($this->valid()) {
                return mysql_fetch_object($this->result);
            } else {
                return false;
            }
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
        while ($row = $this->get_row($result)) {
            
            // set fields into an associative array
            foreach ($row as $key => $value) {
                if ($key != 'Field') {
                    $temp_arr[strtolower($key)] = $value;
                }
            }
            // get default value for field
            if ($row->Default !== 'NULL') {
                $temp_arr['value'] = $row->Default;
            } else {
                $temp_arr['value'] = null;
            }
            
            // set property in fields object
            $fields[$row->Field] = (object) $temp_arr;
        }
        
        $this->free_result($result);
        
        return $fields;
    }
    
    /**
     * Returns the number of row(s) from a result set after a query.
     *
     * @return integer
     */
    public function total_rows($result = null)
    {
        $result = $result ? $result : $this->result;
        return is_resource($result) ? mysql_num_rows($result) : 0;
    }
    
    /**
     * Returns the number of row(s) affect by a query (eg. UPDATE, DELETE).
     *
     * @return integer
     */
    public function affected_rows()
    {
        return @mysql_affected_rows($this->db);
    }
    
    /**
     * Returns the id of the row just inserted.
     *
     * @return integer
     */
    public function insert_id()
    {
        return @mysql_insert_id($this->db);
    }
    
    /**
     * Escapes any bad characters for query string.
     *
     * @param string $string
     * @return string
     */
    public function escape($string)
    {
        return @mysql_real_escape_string($string);
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
     * @return boolean
     **/
    public function free_result($result = null)
    {
        return mysql_free_result($result ? $result : $this->result);
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
        if ($this->offset <= $this->total_rows()
            && $this->offset >= 0
            && is_resource($this->result)) {
            return mysql_data_seek($this->result, $this->offset);
        } else {
            return false;
        }
    } 
} // END class Mysql extends AdapterBase