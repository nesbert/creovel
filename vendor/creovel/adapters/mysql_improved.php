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

// include base class.
require_once 'adapter_base.php';

class MysqlImproved extends AdapterBase implements AdapterInterface, Iterator
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
    public $offset = 0;
    
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
        // open a connection to a MySQL Server and set db_link
        $this->db = @new mysqli(
            $db_properties['host'],
            $db_properties['username'],
            $db_properties['password'],
            $db_properties['default'],
            isset($db_properties['port']) ? $db_properties['port'] : null,
            isset($db_properties['socket']) ? $db_properties['socket'] : null
            );
        
        if (mysqli_connect_error()) {
            self::throw_error(mysqli_connect_error() . '.');
            exit();
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
            $this->db->close();
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
            CREO('log', 'Query: ' . $query);
        }
        
        $result = $this->db->query($query);
        
        if (!$result) {
            self::throw_error("{$this->db->error} Query \"" .
            str_replace(', ', ",\n", $this->query) . "\" failed.");
        }
        
        return $result;
    }
    
    /**
     * Performs a query on the database and sets result resources.
     *
     * @param string $query SQL string
     * @return void
     **/
    public function query($query)
    {
        // reset class properties
        $this->reset();
        
        // set database property
        $this->query = $query;
        
        // send a MySQL query and set query_link resource on success
        $this->result = $this->execute($query);
    }
    
    /**
     * Free result resource.
     *
     * @return void
     **/
    public function close()
    {
        if (isset($this->result) && is_resource($this->result)) {
            $this->result->close();
        }
    }
    
    /**
     * Returns an associative array that corresponds to the fetched row
     * or NULL if there are no more rows.
     *
     * @return array
     **/
    public function get_row()
    {
        return $this->result->fetch_assoc();
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
        return $this->result->num_rows;
    }
    
    /**
     * Returns the number of row(s) affect by a query (eg. UPDATE, DELETE).
     *
     * @return integer
     */
    public function affected_rows()
    {
        return $this->db->affected_rows;
    }
    
    /**
     * Returns the id of the row just inserted.
     *
     * @return integer
     */
    public function insert_id()
    {
        return $this->db->insert_id;
    }
    
    /**
     * Escapes any bad characters for query string.
     *
     * @param string $string
     * @return string
     */
    public function escape($string)
    {
        return $this->db->real_escape_string($string);
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
        if (is_resource($this->db) && is_resource($this->result)) {
            $this->result->close();
        }
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
        return $this->offset;
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
        return $this->result->data_seek($this->offset);
    }
    
    /**
     * Transaction methods.
     */
    
    /**
     * BEGIN transaction.
     *
     * @return void
     **/
    public function begin()
    {
        $this->db->autocommit(FALSE);
        $this->execute('START TRANSACTION;');
    }
    
    /**
     * ROLLBACK transaction.
     *
     * @return void
     **/
    public function rollback()
    {
        #$this->db->rollback();
        $this->execute('ROLLBACK;');
    }

    /**
     * COMMIT transaction.
     *
     * @return void
     **/
    public function commit()
    {
        #$this->db->commit();
        $this->execute('COMMIT;');
    }
} // END class MysqlImproved extends AdapterBase implements AdapterInterface, Iterator