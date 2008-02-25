<?php
require_once '_adapter.php';

/**
 * MySQL Adapter.
 *
 * @copyright	Copyright (c) 2005-2006, creovel.org
 * @package		creovel
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @author		Nesbert Hidalgo
 */
class mysql extends _adapter implements adapter_interface
{

	private $mode;			// Server mode developement, test, productions
	private $db_link;		// MySQL link identifier
	private $query;			// SQL query
	private $query_link;	// MySQL result resource
	private $table;			// current table
	private $database;		// current database
	
	public $pointer = -1;	// MySQL result pointer
	public $row_count = 0;	// number of rows in MySQL result
	
	
	public function  __construct($db_properties = null)
	{
		// if properties passed connect to database
		if ( is_array($db_properties) ) $this->connect($db_properties);
	}
	
	public function __destruct()
	{
		// free memory and close database connection
		$this->disconnect();
	}
	
	public function connect($db_properties)
	{	
		// set properties
		$this->database = $db_properties['database'];
		$this->mode = $db_properties['mode'];
		
		// open a connection to a MySQL Server and set db_link
		$this->db_link = @mysql_connect($db_properties['host'], $db_properties['username'], $db_properties['password']) or $this->handle_error('Could not connect to database ('.$db_properties['host'].'.'.$db_properties['database'].'). ' . mysql_error() . '.');
		
		// set database to use
		$this->set_database($db_properties['database']);
	}
	
	public function disconnect()
	{
		// free result memory
		if ( $this->query_link ) @mysql_free_result($this->query_link);
		
		// close MySQL connection
		//if ($this->db_link) @mysql_close($this->db_link);
	}
	
	public function set_database($database)
	{
		// select a MySQL database to use
		@mysql_select_db($database) or $this->handle_error("Could not select database named <strong>{$database}</strong>. " . mysql_error() . '.');
	}
	
	public function get_database()
	{
		return $this->database;
	}
	
	public function set_table($table)
	{
		$this->table = $table;
	}
	
	public function table_exists($table)
    {
        return mysql_query('DESCRIBE ' . $table) or false;
    }
		
	public function get_fields_object()
	{
		// reset class properties
		$this->reset();
		
		// send a DESCRIBE query and set result on success
		$result = mysql_query('DESCRIBE ' . $this->table);
		
		// foreach row in results insert into fields object
		while ( $row = @mysql_fetch_assoc($result) ) {
		
			// set fields into an associative array
			foreach ( $row as $key => $value ) if ( $key != 'Field' ) $temp_arr[strtolower($key)] = $value;
			// get default value for field
			$temp_arr['value'] = ( $row['Default'] !== 'NULL' ? $row['Default'] : null );
			// set property in fields object
			$fields->$row['Field'] = (object) $temp_arr;
			
		}
		
		return $fields;
	}
	
	public function query($query = null)
	{
		// reset class properties
		$this->reset();
		
		// set database property
		$this->query = $query;
		
		// send a MySQL query and set query_link resource on success
		$this->query_link = @mysql_query($this->query, $this->db_link) or $this->handle_error('Error ' . mysql_errno($this->db_link) . ': ' . $this->get_mysql_error() . ". Query \"" . str_replace(', ', ",\n", $this->query) . "\" failed.");		

		// set row_count with number of rows in result
		$this->row_count = 	@mysql_num_rows($this->query_link);
	}
	
	public function reset()
	{
		// reset properties
		$this->pointer = 0;
		$this->row_count = 0;
		$this->query = null;
	
		// release resource
		if ( $this->query_link ) {
			@mysql_free_result($this->query_link);
			$this->query_link = null;
		}
	}
	
	public function rewind()
	{
		$this->pointer = 0;
	}
	
	public function get_row($pointer = 0)
	{
		// set pointer
		$this->pointer = $pointer;
		
		// move internal result pointer
		mysql_data_seek($this->query_link, $this->pointer);
		
		// fetch and return a result row as an associative array
		return mysql_fetch_assoc($this->query_link);
	}
	
	public function get_affected_rows()
	{
		return mysql_affected_rows();
	}
	
	public function get_insert_id()
	{
		return @mysql_insert_id();
	}
	
	public function all_tables()
	{
		$this->reset();
		
		$result = mysql_query('SHOW TABLES');
		
		while ( $row = @mysql_fetch_assoc($result) ) $tables[] = $row['Tables_in_'.$this->database];
		return $tables;
	}
	
	public function field_breakdown($table_name)
	{
		// reset class properties
		$this->reset();
		
		// send a DESCRIBE query and set result on success
		$result = mysql_query('DESCRIBE ' . $table_name);
		
		// foreach row in results insert into fields object
		while ( $row = @mysql_fetch_assoc($result) ) {
		
			// set fields into an associative array
			foreach ( $row as $key => $value ) $temp_arr[strtolower($key)] = $value;
			// get default value for field
			$temp_arr['value'] = ( $row['Default'] !== 'NULL' ? $row['Default'] : null );
			// set property in fields object
			$fields[] = $temp_arr;
			
		}
		
		return $fields;
	}

	public function key_breakdown($table_name)
	{
		// reset class properties
		$this->reset();
		
		// send a DESCRIBE query and set result on success
		$result = mysql_query('SHOW INDEX FROM ' . $table_name);
		
		// foreach row in results insert into fields object
		while ( $row = @mysql_fetch_assoc($result) ) {
		
			// set fields into an associative array
			foreach ( $row as $key => $value ) $temp_arr[strtolower($key)] = $value;
			// get default value for field
			$temp_arr['value'] = ( $row['Default'] !== 'NULL' ? $row['Default'] : null );
			// set property in fields object
			$keys[] = $temp_arr;
			
		}
		
		return $keys;
	}

	public function transaction_string($action)
	{
		switch ($action)
		{
			case 'start':
				return 'START TRANSACTION';
				break;
			case 'rollback':
				return 'ROLLBACK';
				break;
			case 'commit':
				return 'COMMIT';
				break;
		}
	}
	
	private function get_mysql_error()
	{
		// returns the text of the error message from previous MySQL operation
		return mysql_error($this->db_link);
	}

}
?>
