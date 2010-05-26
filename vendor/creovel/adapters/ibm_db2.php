<?php
/**
 * ORM IbmDb2 Adapter.
 *
 * @package     Creovel
 * @subpackage  Adapters
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.0
 * @author      Nesbert Hidalgo
 */
class IbmDb2 extends AdapterBase
{
    /**
     * Database resource.
     *
     * @var resource
     **/
    public $db;
    
    /**
     * Database name.
     *
     * @var resource
     **/
    public $database;
    
    /**
     * Database schema.
     *
     * @var resource
     **/
    public $schema;
    
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
     * Opens a connection to the server with $db_properties an
     * array of database settings.
     *
     * @param array $db_properties
     * @return void
     **/
    public function connect($db_properties)
    {
        $server = $db_properties['host'];
        
        if (empty($db_properties['port'])) {
            $db_properties['port'] = 50001;
        }
        
        if (!empty($db_properties['socket'])) {
            $server = $db_properties['socket'];
        }
        
        $conn_string = sprintf(
                        "DRIVER={IBM DB2 ODBC DRIVER};DATABASE=%s;HOSTNAME=%s;PORT=%d;PROTOCOL=TCPIP;UID=%s;PWD=%s;",
                        $db_properties['database'],
                        $db_properties['host'],
                        $db_properties['port'],
                        $db_properties['username'],
                        $db_properties['password']
                        );
        
        $this->database = $db_properties['database'];
        $this->schema = $db_properties['schema'];
        
        $this->db = db2_connect($conn_string, '', '');
        
        if (empty($this->db)) {
            self::throw_error("Could not connect to server {$server} (" .
                    db2_conn_errormsg() . ').');
        }
        
        if (empty($this->schema)) {
            self::throw_error("No schema schema set.");
        }
        
        $this->execute(sprintf("SET SCHEMA \"%s\";", $this->schema));
    }
    
    /**
     * Close current connection to the server.
     *
     * @return void
     **/
    public function disconnect()
    {
        // close result resource
        $this->close();
        
        // close server connection
        if (isset($this->db) && is_resource($this->db)) {
            return db2_close($this->db);
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
        // log queries
        if (!empty($GLOBALS['CREOVEL']['LOG_QUERIES'])) {
            CREO('log', "Query: {$query}");
        }
        
        $query = str_replace_array($query, array(
            'LIMIT 1' => 'OPTIMIZE FOR 1 ROWS'
            ));
        $query = str_replace(array('`', ')', '('), '', $query);
        
        echo $query . __LINE__ . '<br>';
        
        $stmt = db2_prepare($this->db, $query);
        if (!$stmt) {
            self::throw_error(db2_stmt_errormsg() . " Query \"" .
            str_replace(', ', ",\n", $query) . "\" failed. #" . db2_stmt_error($this->db));
        }
        
        $result = db2_execute($stmt, array());
        if (!$result) {
            self::throw_error(db2_stmt_errormsg() . " Query \"" .
            str_replace(', ', ",\n", $query) . "\" failed. #" . db2_stmt_error($this->db));
        }
        
        return $stmt;
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
        
        // send a query and set query_link resource on success
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
     * Returns an associative array that corresponds to the fetched row
     * or NULL if there are no more rows.
     *
     * @return object
     **/
    public function get_row($result = null)
    {
        return db2_fetch_object($result ? $result : $this->result);
    }
    
    /**
     * Returns an object modeled by the current table structure.
     *
     * @param string $table_name
     * @return object
     */
    public function columns($table_name)
    {
        $table_name = strtoupper($table_name);
        // set fields object to return
        $fields = array();
        
        // foreach row in results insert into fields object
        $result = db2_columns($this->db, null, $this->schema, $table_name, '%');
        while ($row = $this->get_row($result)) {
            $fields[$row->COLUMN_NAME] = new stdClass;
            $fields[$row->COLUMN_NAME]->type = strtoupper($row->TYPE_NAME);
            $fields[$row->COLUMN_NAME]->null = $row->IS_NULLABLE;
            $fields[$row->COLUMN_NAME]->default = null;
            $fields[$row->COLUMN_NAME]->extra = null;
            $fields[$row->COLUMN_NAME]->value = null;
        }
        $this->free_result($result);
        
        // get and set primary key info
        $result = db2_primary_keys($this->db, null, $this->schema, $table_name);
        
        while ($row = $this->get_row($result)) {
            if (isset($fields[$row->COLUMN_NAME])) {
                $fields[$row->COLUMN_NAME]->key = 'PRI';
                $fields[$row->COLUMN_NAME]->key_name = $row->PK_NAME;
            }
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
        return @db2_num_rows($result ? $result : $this->result);
    }
    
    /**
     * Returns the number of row(s) affect by a query (eg. UPDATE, DELETE).
     *
     * @return integer
     */
    public function affected_rows()
    {
        return @db2_num_rows($this->db);
    }
    
    /**
     * Returns the id of the row just inserted.
     *
     * @return integer
     */
    public function insert_id()
    {
        return @db2_last_insert_id($this->db);
    }
    
    /**
     * Escapes any bad characters for query string.
     *
     * @param string $string
     * @return string
     */
    public function escape($string)
    {
        return @db2_escape_string($string);
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
    public function free_result($result = null)
    {
        return @db2_free_result($result ? $result : $this->result);
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
        if ($this->offset < $this->total_rows()) {
            return db2_fetch_row($this->result, $this->offset);
        } else {
            return false;
        }
    } 
} // END class IbmDb2 extends AdapterBase