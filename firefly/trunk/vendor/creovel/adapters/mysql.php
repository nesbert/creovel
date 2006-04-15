<?php
/**
 * MySQL Adapter.
 *
 * @author Nesbert Hidalgo
 */
class mysql implements adapter_interface
{

	private $db;			// associative array of databse properties
	private $db_link;		// MySQL link identifier
	private $query;			// SQL query
	private $query_link;	// MySQL result resource
	private $table;			// current table
	
	public $pointer = 0;	// MySQL result pointer
	public $row_count = 0;	// number of rows in MySQL result
	public $insert_id;		// ID generated from a MySQL INSERT operation

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
		// set database properties
		$this->db->host = $db_properties['host'];
		$this->db->username = $db_properties['username'];
		$this->db->password = $db_properties['password'];
		$this->db->mode = $db_properties['mode'];
		
		// open a connection to a MySQL Server and set db_link
		$this->db_link = mysql_connect($this->db->host, $this->db->username, $this->db->password) or $this->handle_error('<strong>Error:</strong> Could not connect to database. ' . mysql_error() . '<br />');		
		
		// set database to use
		$this->set_database($db_properties['database']);
	}
	
	public function disconnect()
	{
		// free result memory
		if ( $this->query_link ) mysql_free_result($this->query_link);
		
		// close MySQL connection
		mysql_close($this->db_link);	
	}
	
	public function set_database($database)
	{
		// set database property
		$this->db->database = $database;
		
		// select a MySQL database to use
		mysql_select_db($this->db->database) or $this->handle_error("<strong>Error:</strong> Could not select database ({$this->db->database}@{$this->db->host}). " . mysql_error() . '<br />');
	}
	
	public function set_table($table)
	{
		// set table property
		$this->table = $table;
	}
		
	public function get_fields_object()
	{
		// reset class properties
		$this->reset();
		
		// send a DESCRIBE query and set result on success
		$result = mysql_query('DESCRIBE ' . $this->table);
		
		// foreach row in results insert into fields object
		while ( $row = mysql_fetch_assoc($result) ) {
		
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
		$this->query_link = mysql_query($this->query, $this->db_link) or $this->handle_error("<strong>Error:</strong> Query failed. " . $this->get_mysql_error() . "\n" . str_replace(', ', ",\n", $this->query) . "\n\n");
		
		// set row_count with number of rows in result
		$this->row_count = 	mysql_num_rows($this->query_link);	
		
		// set insert_id with the ID generated from the previous INSERT operation
		if ( mysql_insert_id() ) $this->insert_id = mysql_insert_id();
		
	}
	
	public function reset()
	{
	
		// reset properties
		$this->pointer = 0;
		$this->row_count = 0;
		$this->query = null;
	
		// release resource
		if ( $this->query_link ) {
			mysql_free_result($this->query_link);
			$this->query_link = null;
		}
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
	
	private function get_mysql_error()
	{
		// returns the text of the error message from previous MySQL operation
		return mysql_error($this->db_link);		
	}
	
	private function handle_error($message)
	{
	
		if ( $_ENV['mode'] == 'production' ) {
			die('error_handler');
		} else {
			die($message);
		}
		
	}
	
	// error_reporting(E_ALL);
	
}
?>
