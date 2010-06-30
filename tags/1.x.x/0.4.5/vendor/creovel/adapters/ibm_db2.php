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
     * Iterator offset DB2 starts 1.
     *
     * @var resource
     **/
    public $offset = 1;
    
    /**
     * Temporay autocommit mode holder.
     *
     * @var resource
     **/
    public $autocommit;
    
    /**
     * Pass an associative array of database settings to connect
     * to database on construction of class.
     *
     * @return void
     **/
    public function __construct($db_properties = null)
    {
    	parent::__construct($db_properties);
    	
    	$this->offset = 1;
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
        if (empty($db_properties['database'])
            || empty($db_properties['username'])
            || empty($db_properties['password'])) {
            self::throw_error('Could not connect to server because of '.
                'missing arguments for $db_properties.');
            
        }
        
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
        
        if (empty($db_properties['persistent'])) {
            $this->db = db2_connect($conn_string, '', '');
        } else {
            $this->db = db2_pconnect($conn_string, '', '');
        }
        
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
        
        $stmt = db2_prepare($this->db, $query);
        if (!$stmt) {
            self::throw_error(db2_stmt_errormsg() . " Query \"" .
            str_replace(', ', ",\n", $query) . "\" failed. #" . db2_stmt_error($this->db));
        }
        
        $stm_options = array(
                    'rowcount' => DB2_ROWCOUNT_PREFETCH_ON,
                    'cursor' => DB2_SCROLLABLE
                    );
        
        if (!db2_set_option($stmt, $stm_options, 2)) {
            self::throw_error(db2_stmt_errormsg() . " Query \"" .
            str_replace(', ', ",\n", $query) . "\" failed. #" . db2_stmt_error($this->db));
        }
        
        $result = db2_execute($stmt);
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
     * Returns an object that corresponds to the fetched row
     * or NULL if there are no more rows.
     *
     * @return object
     **/
    public function get_row($result = null)
    {
        if ($result) {
            return db2_fetch_object($result);
        } else if ($this->offset >= 1) {
            return db2_fetch_object($this->result, $this->offset);
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
        $table_name = strtoupper($table_name);
        // set fields object to return
        $fields = array();
        
        // foreach row in results insert into fields object
        $result = db2_columns($this->db, null, $this->schema, $table_name, '%');
        while ($row = db2_fetch_object($result)) {
            $fields[$row->COLUMN_NAME] = $row;
        }
        $this->free_result($result);
        
        // get and set primary key info
        $result = db2_primary_keys($this->db, null, $this->schema, $table_name);
        
        while ($row = db2_fetch_object($result)) {
            if (isset($fields[$row->COLUMN_NAME])) {
                $fields[$row->COLUMN_NAME]->KEY = 'PK';
                $fields[$row->COLUMN_NAME]->KEY_NAME = $row->PK_NAME;
            }
        }
        $this->free_result($result);
        
        // get identity solumn for table
        $q = "SELECT TABSCHEMA, TABNAME, COLNO, COLNAME, TYPENAME, LENGTH, DEFAULT, IDENTITY, GENERATED " .
             "FROM SYSCAT.COLUMNS " .
             "WHERE TABSCHEMA = '{$this->schema}' AND TABNAME = '{$table_name}' AND IDENTITY = 'Y';";
        $result = $this->execute($q);
        
        while ($row = db2_fetch_object($result)) {
        	if ($row->IDENTITY == 'Y' && $row->GENERATED == true && $row->TYPENAME == 'INTEGER') {
               $fields[$row->COLNAME]->IS_IDENTITY = true;
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
        $result = $result ? $result : $this->result;
        return is_resource($result) ? db2_num_rows($result) : 0;
    }
    
    /**
     * Returns the number of row(s) affect by a query (eg. UPDATE, DELETE).
     *
     * @return integer
     */
    public function affected_rows()
    {
        return db2_num_rows($this->result);
    }
    
    /**
     * Returns the id of the row just inserted.
     *
     * @return integer
     */
    public function insert_id()
    {
        return db2_last_insert_id($this->db);
    }
    
    /**
     * Escapes any bad characters for query string.
     *
     * @param string $string
     * @return string
     */
    public function escape($string)
    {
        return db2_escape_string($string);
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
        $return = db2_free_result($result ? $result : $this->result);
        if ($return ) $this->result = null;
        return $return;
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
        $this->offset = 1;
    }
    
    /**
     * Adjusts the result pointer to an arbitrary row in the result
     * and returns TRUE on success or FALSE on failure.
     *
     * @return boolean
     **/
    public function valid()
    {
        if ($this->offset <= $this->total_rows()) {
            if ($this->offset) {
                return db2_fetch_object($this->result, $this->offset) !== false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }
    
    /**
     * Transaction methods.
     */
    
    /**
     * START transaction and save current autocommit mode before
     * transaction begins.
     *
     * @return void
     **/
    public function start_tran()
    {
        $this->autocommit = db2_autocommit($this->db);
        db2_autocommit($this->db, DB2_AUTOCOMMIT_OFF);
    }
    
    /**
     * ROLLBACK transaction and revert autocommit back to the same
     * mode before transaction.
     *
     * @return void
     **/
    public function rollback()
    {
        db2_rollback($this->db);
        db2_autocommit($this->db, $this->autocommit);
    }
    
    /**
     * COMMIT transaction and revert autocommit back to the same
     * mode before transaction.
     *
     * @return void
     **/
    public function commit()
    {
        db2_commit($this->db);
        db2_autocommit($this->db, $this->autocommit);
    }
} // END class IbmDb2 extends AdapterBase