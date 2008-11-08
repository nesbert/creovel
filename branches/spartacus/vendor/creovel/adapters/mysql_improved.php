<?php
/**
 * ORM MySQL Adapter.
 *
 * @package Creovel
 * @subpackage Creovel.Classes
 * @copyright  2008 Creovel, creovel.org
 * @license    http://creovel.googlecode.com/svn/trunk/License   MIT License
 * @version    $Id:$
 * @since      Class available since Release 0.4.0
 */

/**
 * Include base and interface classes.
 */
require_once 'adapter_interface.php';
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
	 * Result row offset. Must be between zero and the total number of
	 * rows minus one.
	 *
	 * @var integer
	 **/
	public $offset = 0;
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function  __construct($db_properties = null)
	{
		// if properties passed connect to database
		if (is_array($db_properties)) $this->connect($db_properties);
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function __destruct()
	{
		// free memory and close database connection
		$this->disconnect();
	}
	
	/**
	 * undocumented function
	 *
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
			self::throw_error('Could not connect to database (' .
				$db_properties['host'] . '.' . $db_properties['default'] .
				'). ' . mysqli_connect_error() . '.');
			exit();
		}
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function disconnect()
	{
		//
		if (@isset($this->result) && is_object($this->result)) {
			@$this->result->close();
		}
		// close MySQL connection
		if (@is_resource($this->db)) {
			@$this->db->close();
		}
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function query($query = null)
	{
		// reset class properties
		$this->reset();
		
		// set database property
		$this->query = $query;
		
		// send a MySQL query and set query_link resource on success
		$this->result = $this->db->query($query);
		
		if (!$this->result) {
			self::throw_error($this->db->error . ". Query \"" .
				str_replace(', ', ",\n", $this->query) . "\" failed.");
			exit();
		}
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
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
		$result = $this->db->query("DESCRIBE `{$table_name}`;");
		
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
			$temp_arr['value'] = $row['Default'] !== 'NULL' ? $row['Default'] : null;
			
			// set property in fields object
			$fields[$row['Field']] = (object) $temp_arr;
		}
		
		$result->close();
		
		return $fields;
	}
	
	/**
	 * Returns the number of row(s) found after a query.
	 *
	 * @return int
	 */
	public function total_rows()
	{
		return $this->result->num_rows;
	}
	
	/**
	 * Returns the number of row(s) affect by a query (eg. UPDATE, DELETE).
	 *
	 * @return int
	 */
	public function affected_rows()
	{
		return $this->db->affected_rows;
	}
	
	/**
	 * Returns the id of the row just inserted.
	 *
	 * @return int
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
		#echo "rewind: $this->offset<br/>";
	}
	
	/**
	 * Return the current row in result object.
	 *
	 * @return array
	 **/
	public function current()
	{
		#echo "current: $this->offset<br/>";
		return $this->get_row();
	}
	
	/**
	 * Returns the index element of the current result object pointer.
	 *
	 * @return integer
	 **/
	public function key()
	{
		#echo "key: $this->offset<br/>";
		return $this->offset;
	}
	
	/**
	 * Advance the result object pointer.
	 *
	 * @return array
	 **/
	public function next()
	{
		$this->offset++;
		#echo "next: $this->offset<br/>";
		return $this->current();
	}
	
	/**
	 * Rewind the result object pointer by one.
	 *
	 * @return array
	 **/
	public function prev()
	{
		$this->offset--;
		#echo "prev: $this->offset<br/>";
		return $this->current();
	}
	
	/**
	 * Adjusts the result pointer to an arbitrary row in the result and returns
	 * TRUE on success or FALSE on failure.
	 *
	 * @return boolean
	 **/
	public function valid()
	{
		return $this->result->data_seek($this->offset);
	}
}