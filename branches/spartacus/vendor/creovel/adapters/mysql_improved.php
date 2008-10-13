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

class MysqlImproved extends AdapterBase implements AdapterInterface
{
	/**
	 * SQL query string.
	 *
	 * @var string
	 **/
	public $query = '';
	
	public function  __construct($db_properties = null)
	{
		// if properties passed connect to database
		if (is_array($db_properties)) $this->connect($db_properties);
	}
	
	public function __destruct()
	{
		// free memory and close database connection
		$this->disconnect();
	}
	
	public function connect($db_properties)
	{
		// open a connection to a MySQL Server and set db_link
		$this->mysqli = @new mysqli(
			$db_properties['host'],
			$db_properties['username'],
			$db_properties['password'],
			$db_properties['default'],
			isset($db_properties['port']) ? $db_properties['port'] : null,
			isset($db_properties['socket']) ? $db_properties['socket'] : null
			);
		
		if (mysqli_connect_error()) {
			self::throwError('Could not connect to database (' .
				$db_properties['host'] . '.' . $db_properties['default'] .
				'). ' . mysqli_connect_error() . '.');
			exit();
		}
	}
	
	public function disconnect()
	{
		//
		if (isset($this->result)) {
			$this->result->close();
		}
		// close MySQL connection
		if (is_resource($this->mysqli)) {
			$this->mysqli->close();
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
		$this->result = $this->mysqli->query($query);
		
		if (!$this->result) {
			self::throwError($this->mysqli->error . ". Query \"" .
				str_replace(', ', ",\n", $this->query) . "\" failed.");
			exit();
		}
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Nesbert Hidalgo
	 **/
	public function getRow()
	{
		return $this->result->fetch_assoc();
	}
	
	/**
	 * Set the table to model.
	 *
	 * @param string $table name of table
	 * @return void
	 */
	public function setTable($table)
	{}
	
	/**
	 * Returns an object modeled by the current table structure.
	 *
	 * @return object
	 */    
	public function columns()
	{}
	
	/**
	 * Resets the row pointer (index) to zero and reintialize all class varibles.
	 *
	 */
	 public function reset()
	{}
	
	/**
	 * Returns the number of row(s) found after a query.
	 *
	 * @return int
	 */
	public function totalRows()
	{
		return $this->result->num_rows;
	}
	
	/**
	 * Returns the number of row(s) affect by a query (eg. UPDATE, DELETE).
	 *
	 * @return int
	 */
	public function affectedRows()
	{
		return $this->result->num_rows;
	}
	
	/**
	 * Returns the id of the row just inserted.
	 *
	 * @return int
	 */
	public function insertId()
	{}
	
	/**
	 * Quotes a string '$string' and escapes any bad characters.
	 *
	 * @param string $string name of table
	 * @return string
	 */
	public function quote($string)
	{
		return "'" . $string . "'";
	}
	
	/**
	 * Check if a table exists. Returns false if table not found.
	 *
	 * @param string $table name of table
	 * @return bool
	 */    
	public function tableExists($table)
	{}
}