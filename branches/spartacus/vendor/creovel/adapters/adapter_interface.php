<?php
/**
 * Database adapter inferface.
 *
 * @package Creovel
 * @subpackage Creovel.Classes
 * @copyright  2008 Creovel, creovel.org
 * @license    http://creovel.googlecode.com/svn/trunk/License   MIT License
 * @version    $Id:$
 * @since      Class available since Release 0.4.0
 */
interface AdapterInterface
{
	/**
	 * Connect to database and create resources.
	 *
	 * <code>
	 * $db_properties['host']       = 'localhost';
	 * $db_properties['database']   = 'database';
	 * $db_properties['username']   = 'username';
	 * $db_properties['password']   = 'password';
	 * $db_properties['port']       = 'port';
	 * $db_properties['socket']     = 'socket';
	 * </code>
	 *
	 * @param array $db_properties array of DB connecting settings
	 * @return void
	 */
	public function connect($db_properties);
	
	/**
	 * Disconnect from database and free all resources used.
	 *
	 * @return void
	 */
	public function disconnect();
	
	/**
	 * Query database and set result resources.
	 *
	 * @param string $query SQL string
	 * @return void
	 */    
	public function query();
	
	/**
	 * Returns an object modeled by the current table structure.
	 *
	 * @param string $table_name
	 * @return object
	 */    
	public function columns($table_name);
	
	/**
	 * Returns the number of row(s) found after a query.
	 *
	 * @return int
	 */
	public function total_rows();
	
	/**
	 * Returns the number of row(s) affect by a query (eg. UPDATE, DELETE).
	 *
	 * @return int
	 */
	public function affected_rows();
	
	/**
	 * Returns the id of the row just inserted.
	 *
	 * @return int
	 */
	public function insert_id();
	
	/**
	 * Escapes any bad characters for query string.
	 *
	 * @param string $string
	 * @return string
	 */
	public function escape($string);
	
	/**
	 * Resets DB properties and frees result resources.
	 *
	 * @return void
	 **/
	public function reset();
	
	/**
	 * Rewind the result object pointer.
	 *
	 * @return array
	 **/
	public function prev();
}