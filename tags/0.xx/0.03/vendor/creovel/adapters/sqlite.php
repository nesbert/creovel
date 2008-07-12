<?php
/**
 * Copyright (c) 2005-2006, creovel.org
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated 
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software,
 * and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions
 * of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED
 * TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * Licensed under The MIT License. Redistributions of files must retain the above copyright notice.
 */

/**
 * Sqlite Adapter.
 *
 * @copyright	Copyright (c) 2005-2006, creovel.org
 * @package		creovel
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @author		Russ Smith
 */
class sqlite extends adapter_base implements adapter_interface

{

	private $mode;			// Server mode developement, test, productions
	private $db_link;		// Sqlite link identifier
	private $query;			// SQL query
	private $query_link;	// Sqlite result resource
	private $table;			// current table
	private $database;		// current database
	
	public $pointer = -1;	// Sqlite result pointer
	public $row_count = 0;	// number of rows in Sqlite result
	
	
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
		$this->db_link = sqlite_open($db_properties['database']) or $this->handle_error('Could not open database file ('.$db_properties['database'].'.');
	}
	
	public function disconnect()
	{
		// close Sqlite connection
		if ($this->db_link) $this->db_link = null;
	}
	
	public function set_database($database)
	{
		// select a MySQL database to use
		$this->db_link = sqlite_open($database) or $this->handle_error('Could not open database file ('.$db_properties['database'].'.');
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
        return (in_array($table, $tis->all_tables()));
    }
		
	public function get_fields_object()
	{
		// reset class properties
		$this->reset();

		$result = sqlite_query($this->db_link, "pragma table_info('{$this->table}');"); 

		while ($row = sqlite_fetch_array($result))
		{
			// set fields into an associative array
			foreach ($row as $key => $value) if ($key != 'cid') $temp_arr[strtolower($key)] = $value;
			// get default value for field
			$temp_arr['value'] = ( $row['dflt_value'] !== '' ? $row['dflt_value'] : null );
			// set property in fields object
			$fields->$row['name'] = (object)$temp_arr;
		}
		
		return $fields;
	}
	
	public function query($query = null)
	{
		// reset class properties
		$this->reset();
		
		// set database property
		$this->query = $query;

		$this->query_link = sqlite_query($this->db_link, $this->query);

		// set row_count with number of rows in result
		$this->row_count = 	sqlite_num_rows($this->query_link);
	}
	
	public function reset()
	{
		// reset properties
		$this->pointer = 0;
		$this->row_count = 0;
		$this->query = null;
	
		// release resource
		if ( $this->query_link ) $this->query_link = null;
	}
	
	public function rewind()
	{
		$this->pointer = 0;
	}
	
	public function get_row($pointer = 0)
	{
		// set pointer
		$this->pointer = $pointer;

		// fetch and return a result row as an associative array
		sqlite_seek($this->query_link, $pointer);
		
		// fetch and return a result row as an associative array
		return sqlite_fetch_array($this->query_link);
	}
	
	public function get_affected_rows()
	{
		return sqlite_num_rows($this->query_link);
	}
	
	public function get_insert_id()
	{
		return sqlite_last_insert_rowid($this->db_link);
	}

	public function all_tables()
	{
		$this->reset();
		
		$result = sqlite_query($this->db_link, "SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
		while ($row = sqlite_fetch_array($result)) $tables[] = $row['name'];

		return $tables;
	}

	public function field_breakdown($table_name)
	{
		// reset class properties
		$this->reset();

		// send a DESCRIBE query and set result on success
		$result = sqlite_query($this->db_link, "pragma table_info('{$table_name}');"); 

		while ($row = sqlite_fetch_array($result))
		{
			// set fields into an associative array
			foreach ($row as $key => $value) if ($key != 'cid') $temp_arr[strtolower($key)] = $value;
			// get default value for field
			$temp_arr['value'] = ( $row['dflt_value'] !== '' ? $row['dflt_value'] : null );
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
		$result = sqlite_query($this->db_link, "pragma index_list('{$table_name}');"); 

		while ($row = sqlite_fetch_array($result))
		{
			// set fields into an associative array
			foreach ($row as $key => $value) if ($key != 'cid') $temp_arr[strtolower($key)] = $value;
			// get default value for field
			$temp_arr['value'] = ( $row['value'] !== '' ? $row['value'] : null );
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
				return 'BEGIN TRANSACTION';
				break;
			case 'rollback':
				return 'ROLLBACK TRANSACTION';
				break;
			case 'commit':
				return 'COMMIT TRANSACTION';
				break;
		}
	}
	
	private function handle_error($message)
	{
		if ( $_ENV['mode'] == 'production' ) {
			die('error_handler');
		} else {
			$_ENV['error']->add($message);
		}
	}
}

?>
