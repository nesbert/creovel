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
class ActiveDatabase extends CObject
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
            $this->connect($db_properties);
        }
    }
    
    /**
     * undocumented function
     *
     * @return void
     **/
    public function __get($member)
    {
        if (isset($this->{$member})) {
            return $this->{$member};
        }
    }
    
    /**
     * Get the current database connection settings based on Creovel mode.
     *
     * @return array
     **/
    public function connection_properties()
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
    public function connect($db_properties = null)
    {
        try {
            
            if (!$db_properties || !is_array($db_properties)) {
                $db_properties = self::connection_properties();
            }
            
            $this->__adapter = $db_properties['adapter'];
            
            // Use databse for schema for mysql
            if (empty($db_properties['schema'])) {
                $db_properties['schema'] = $db_properties['database'];
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
            
            return true;
            
        } catch (Exception $e) {
            CREO('application_error_code', 500);
            CREO('application_error', $e);
        }
    }
    
    /**
     * Disconnect from database and free resources.
     *
     * @return void
     **/
    public function disconnect()
    {
        return $this->adapter()->disconnect();
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
            $this->connect();
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
                $attr->adapter_type = $this->get_adapter_type();
                $return[$name] = ActiveRecordField::object($attr);
            }
            
            return $return;
        } catch (Exception $e) {
            CREO('application_error_code', 500);
            CREO('application_error', $e);
        }
    }
    
    /**
     * Buid a lowercased string of the current DB adapter.
     * 
     * @return string
     **/
    public function get_adapter_type()
    {
        if (is_resource($this->__adapter_obj->db)) {
            $resource_type = get_resource_type($this->__adapter_obj->db);
        } else {
            $resource_type = get_class($this->__adapter_obj->db);
        }
        
        $resource_type = strtolower($resource_type);
        
        switch ($resource_type) {
            case CString::contains('db2', $resource_type):
                return 'db2';
            default:
                return 'mysql';
        }
    }
} // END class ActiveDatabase extends CObject
