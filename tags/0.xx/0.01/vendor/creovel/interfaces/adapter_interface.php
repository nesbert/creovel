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
 * Database adapter inferface.
 *
 * @package creovel
 * @subpackage interfaces
 */
interface adapter_interface
{

	/**
	 * Connect to database and create resources.
	 *
	 * <code>
	 * $db_properties['host']		= 'localhost';
	 * $db_properties['database']	= 'database';
	 * $db_properties['username']	= 'username';
	 * $db_properties['password']	= 'password';
	 * </code>
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param array $db_properties array of DB connecting settings
	 */
	public function connect($db_properties);
	
	/**
	 * Disconnect from database and free all resources used.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 */
	public function disconnect();
	
	/**
	 * Set the table to model.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 */
	public function set_table($table);
	
	/**
	 * Check if a table exists. Returns false if table not found.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $table name of table
	 * @return bool
	 */	
	public function table_exits($table);	
	
	/**
	 * Returns an object modelled by the current table strucure.
	 *
	 * <code>	
		[_fields:protected] => stdClass Object
			(
				[id] => stdClass Object
					(
						[type] => int(11)
						[null] => 
						[key] => PRI
						[default] => 
						[extra] => auto_increment
						[value] => 
					)
				[created_at] => stdClass Object....
			)
	 * </code>
	 * @author Nesbert Hidalgo
	 * @access public
	 * @return object
	 */	
	public function get_fields_object();
	
	/**
	 * Query database and set result resources.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $query SQL string
	 */	
	public function query();
	
	/**
	 * Resets the row pointer (index) to zero and reintialize all class varibles.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 */
	 public function reset();
	
	/**
	 * Returns the number of row(s) found after a query.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @return int
	 */
	public function get_row();
	
	/**
	 * Returns the number of row(s) affect by a query (eg. UPDATE, DELETE).
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @return int
	 */
	public function get_affected_rows();
	
	/**
	 * Returns the id of the row just intserted.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @return int
	 */	
	public function get_insert_id();
	
}
?>