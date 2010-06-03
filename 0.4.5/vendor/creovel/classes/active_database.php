<?php
/**
 * Database class layer for ActiveRecord.
 *
 * @package     Creovel
 * @subpackage  Core
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.x
 * @author      Nesbert Hidalgo
 **/
class ActiveDatabase
{
    /**#@+
     * Class member.
     *
     * @access private
     */
    // private $__host;
    // private $__port;
    // private $__socket;
    private $__database;
    private $__schema;
    private $__adapter;
    private $__adapter_obj;
    /**#@-*/
    
    /**
     * Pass an associative array of database settings to connect
     * to database on construction of class.
     *
     * @return void
     **/
    public function __construct($db_properties = null)
    {
        if (!empty($db_properties)) {
            $this->establish_connection($db_properties);
        }
    }
    
    /**
     * undocumented function
     *
     * @return void
     **/
    public function __get($member)
    {
        if (isset($member)) {
            return $this->{$member};
        }
    }
    
    /**
     * Get the current database connection settings based on Creovel mode.
     *
     * @return array
     **/
    final public function connection_properties()
    {
        return $GLOBALS['CREOVEL']['DATABASES'][strtoupper(CREO('mode'))];
    }
    
    /**
     * Choose the correct DB adapter to use and sets its properties and
     * return a database object.
     *
     * @param array $db_properties
     * @return boolean
     **/
    final public function establish_connection($db_properties = null)
    {
        try {
            
            if (!$db_properties || !is_array($db_properties)) {
                $db_properties = self::connection_properties();
            }
            
            $this->__adapter = $db_properties['adapter'];
            
            if ($this->__adapter == 'IbmDb2') {
                // uppercase properties
                $db_properties['database'] = strtoupper($db_properties['database']);
                $db_properties['schema'] = strtoupper($db_properties['schema']);
            } else {
                // use databse for schema for mysql
                if (empty($db_properties['schema'])) {
                    $db_properties['schema'] = $db_properties['database'];
                }
            }
            
            $this->__database = $db_properties['database'];
            $this->__schema = @$db_properties['schema'];
            
            if (class_exists($this->__adapter)) {
                $this->__adapter_obj = new $this->__adapter($db_properties);
            } else {
                throw new Exception(
                    "Unknown database adapter '{$adapter}'. " .
                    "Please check database configuration file.");
            }
            
        } catch (Exception $e) {
            CREO('application_error_code', 500);
            CREO('application_error', $e);
        }
    }
    
    /**
     * Pass through method to access the __adapter member. Checks for
     * db connection if not present will a new establish connection.
     *
     * @param string $table
     * @return object ActiveDatabase;
     **/
    public function adapter()
    {
        if (empty($this->__adapter_obj)) {
            $this->establish_connection();
        }
        return $this->__adapter_obj;
    }
    
    /**
     * Return an array ActiveRecordField objects for table column.
     *
     * @param string $table
     * @return array
     **/
    public function columns($table)
    {
        try {
            $columns = $this->adapter()->columns($table);
            
            if (empty($columns)) {
                throw new Exception("Unable to map columns for '{$table}'.");
            }
            
            $return = array();
            
            foreach ($columns as $name => $attr) {
                $attr->adapter = $this->__adapter;
                $attr->database = $this->__database;
                $attr->table = $table;
                $attr->name = $name;
                $return[$name] = ActiveRecordField::object($attr);
            }
            
            return $return;
        } catch (Exception $e) {
            CREO('application_error_code', 500);
            CREO('application_error', $e);
        }
    }
} // END class ActiveDatabase
