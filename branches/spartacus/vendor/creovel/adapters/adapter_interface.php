<?php
/**
 * Database adapter interface.
 *
 * @package     Creovel
 * @subpackage  Adapters
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.0
 * @author      Nesbert Hidalgo
 */
interface AdapterInterface
{
    /**
     * Connect to database and create resources.
     *
     * <code>
     * $db_properties['host']       = 'localhost';
     * $db_properties['default']    = 'database';
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
     **/
    public function disconnect();
    
    /**
     * Performs a query on the database and sets result resources.
     *
     * @param string $query SQL string
     * @return void
     **/
    public function query($query);
    
    /**
     * Free result resource.
     *
     * @return void
     **/
    public function close();
    
    /**
     * Returns an associative array that corresponds to the fetched row
     * or NULL if there are no more rows.
     *
     * @return array
     **/
    public function get_row();
    
    /**
     * Returns an object modeled by the current table structure.
     *
     * @param string $table_name
     * @return object
     */
    public function columns($table_name);
    
    /**
     * Returns the number of row(s) from a result set after a query.
     *
     * @return integer
     */
    public function total_rows();
    
    /**
     * Returns the number of row(s) affect by a query (eg. UPDATE, DELETE).
     *
     * @return integer
     */
    public function affected_rows();
    
    /**
     * Returns the id of the row just inserted.
     *
     * @return integer
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
} // END interface AdapterInterface