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

// include base and interface classes.
require_once 'adapter_base.php';

class Mysql extends AdapterBase implements AdapterInterface, Iterator
{
    /**
     * Database resource.
     *
     * @var resource
     **/
    public $db;
    
    /**
     * SQL query string.
     *
     * @var string
     **/
    public $query = '';
    
    /**
     * Result row offset. Must be between zero and the total number
     * of rows minus one.
     *
     * @var integer
     **/
    //public $offset = 0;
    
    /**
     * Pass an associative array of database settings to connect
     * to database on construction of class.
     *
     * @return void
     **/
    public function  __construct($db_properties = null)
    {
        // if properties passed connect to database
        if (is_array($db_properties)) $this->connect($db_properties);
    }
    
    /**
     * Opens a connection to the MySQL Server with $db_properties an
     * array of database settings.
     *
     * @param array $db_properties
     * @return void
     **/
    public function connect($db_properties)
    {
        $server = $db_properties['host'];
        
        if (!empty($db_properties['port'])) {
            $server .= ':' . $db_properties['port'];
        }
        
        if (!empty($db_properties['socket'])) {
            $server = $db_properties['socket'];
        }
        
        // open a connection to a MySQL Server and set db_link
        $this->db = @mysql_connect(
            $server,
            $db_properties['username'],
            $db_properties['password']
            );
        
        if (!$this->db) {
            self::throw_error("Could not connect to server ({$server}). " .
                                mysql_error() . '.');
        }
        
        if (!empty($db_properties['default'])
            && !mysql_select_db($db_properties['default'], $this->db)) {
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
     * Execute query and return result object/resource or false. Option to log
     * log queries if $GLOBALS['CREOVEL']['LOG_QUERIES'] is set to true. All
     * queries should pass through this function.
     *
     * @param string $query SQL string
     * @return object/false
     **/
    public function execute($query)
    {
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
        
        // if connection lost reconnect
        if (!is_resource($this->db)) {
            $this->connect(ActiveRecord::connection_properties());
        }
        
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
            mysql_free_result($this->result);
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
        return mysql_fetch_object($this->result);
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
        while ($row = mysql_fetch_assoc($result)) {
            
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
        
        mysql_free_result($result);
        
        return $fields;
    }
    
    /**
     * Returns the number of row(s) from a result set after a query.
     *
     * @return integer
     */
    public function total_rows()
    {
        return @mysql_num_rows($this->result);
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
        // reset properties
        $this->query = '';
        $this->offset = 0;
        
        // release result resource
        if (is_resource($this->db) && !empty($this->result) && is_resource($this->result)) {
            $this->free_result();
        }
    }
    
    /**
     * Free results resource.
     *
     * @return boolean
     **/
    public function free_result()
    {
        return mysql_free_result($this->result);
    }
    
    /**
     * Iterator methods.
     */
    
    /**
     * Set the result object pointer to its first element.
     *
     * @return void
     **/
    public function rewind()
    {
        $this->offset = 0;
    }
    
    /**
     * Returns an associative array of the current row.
     * 
     * @return array
     * @see function get_row
     **/
    public function current()
    {
        return $this->get_row();
    }
    
    /**
     * Returns the index element of the current result object pointer.
     *
     * @return integer
     **/
    public function key()
    {
        return (int) $this->offset;
    }
    
    /**
     * Advance the result object pointer and return an associative
     * array of the current row.
     * 
     * @return array
     * @see function current
     **/
    public function next()
    {
        $this->offset++;
        return $this->current();
    }
    
    /**
     * Rewind the result object pointer by one and return an associative
     * array of the current row.
     *
     * @return array
     * @see function current
     **/
    public function prev()
    {
        $this->offset--;
        return $this->current();
    }
    
    /**
     * Adjusts the result pointer to an arbitrary row in the result
     * and returns TRUE on success or FALSE on failure.
     *
     * @return boolean
     **/
    public function valid()
    {
        if ($this->offset < $this->total_rows()) {
            return mysql_data_seek($this->result, $this->offset);
        } else {
            return false;
        }
    } 
} // END class Mysql extends AdapterBase implements AdapterInterface, Iterator