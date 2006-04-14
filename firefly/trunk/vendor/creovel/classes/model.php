<?php
// mysql_real_escape_string

class model implements Iterator
{
	private $select_query;
	private $action_query;
	private $table_name;
	private $fields;
	private $interator_valid;
	public $page;
	
	const ROW_ASSOC = 1;
	const ROW_NUM = 2;
	const ROW_BOTH = 3;
	const ROW_OBJECT = 4;
	
	public function __construct($data = null)
	{
		$this->select_query = $this->establish_connection($this->get_connection_properties());
		
		$this->set_table();
		$this->set_fields();
		$this->set_data($data);
	}
	
	/**
	 * Get connection properties for DB
	 *
	 * @author Nesbert Hidalgo
	 * @access private
	 * @return array
	 */

	private function get_connection_properties()
	{
	
		switch ( $_ENV['mode'] ) {
		
			case 'production':
				return $_ENV['production'];
			break;
		
			case 'test':
				return $_ENV['test'];
			break;
		
			case 'development':
			default:
				return $_ENV['development'];
			break;
		
		}

	}
	
	/**
	 * Choose the correct DB adapter to use and sets its properties.
	 * Returns an DB Layer object.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param array $db_properties required
	 * @return object
	 */

	public function establish_connection($db_properties)
	{
	
		switch ( strtolower($db_properties['adapter']) ) {
		
			case 'mysql':
				$adapter = 'mysql';
			break;
			
			default:
				die('<strong>Error:</strong> Unknown Database Adapter.');
			break;
			
		}
		
		$db_obj = new $adapter($db_properties);
		
		return $db_obj;
		
	}
	
	/**
	 * Sets the table_name to be used by the DB Adapter
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $table_name optional
	 */

	public function set_table($table_name = '')
	{
		
		// if the property set_table_name is set use property as table
		$this->table_name =  ( isset($this->set_table_name) ? $this->set_table_name : $table_name );
		
		// if no table_name set table_name from declared classes
		if ( !$this->table_name ) {
		
			$classes = get_declared_classes();
			$model_name = underscore(str_replace('_model', '', $classes[array_search(get_class(), $classes) + 1]));
			$this->table_name = pluralize($model_name);
			
		}
		
		// set which table to use
		switch ( $this->table_name ) {
		
			case 'mysql':
				return false;
			break;
		
			default:
				$this->select_query->set_table($this->table_name);
				return true;
			break;
		}
		
	}
	
	public function set_fields()
	{
		$this->fields = $this->select_query->get_fields_object();
	}
	
	public function set_data($data)
	{
		if ( is_array($data) ) {
			$this->find_first_by_id($data['id']);
			$this->load_values_into_fields($data);
		} else if ( $data ) {
			$this->find_first_by_id($data);
		}
	}
	
	/************************************************
	 * Desc:	allows possibility to do:
	 *				$model->column = 123
	 *			for any column in the table its modeling
	 *
	 *
	 *
	 * Return:	TRUE if column exists or triggers error
	 *			if column DNE
	 ************************************************/
	 
	private function __set($property, $value) {

		if ( isset($this->fields->$property ) ) {

			if ( $property == 'id' ) {

				return $this->find_first_by_id($value);

			} else {

				$this->fields->$property->value = $value;

				return true;
			}

		} else {
			 $x = debug_backtrace();
  			$funcname = $x[1]['function'];
			trigger_error('<b>$' . $property . '</b> is not a property in class <b>\'' . get_class($this) . '\'</b> Calling function: ' .$funcname, E_USER_ERROR);

		}

	}

	/************************************************
	 * Desc:	allows possibility to do:
	 *				$var = $model->column
	 *			for any column in the table its modeling
	 *
	 *
	 *
	 * Return:	TRUE if column exists or triggers error
	 *			if column DNE
	 ************************************************/
	 
	private function __get($property) {
	
		if ( isset($this->fields->$property) ) {
			
			return $this->fields->$property->value;

		/*} else if (array_key_exists($property, $this->links)) {

			return $this->get_link_object($property);
		*/
		} else {

			$x = debug_backtrace();
			$funcname = $x[1]['function'];
			
			trigger_error('<b>$' . $property . '</b> is not a property in class <b>\'' .  $x[1]['class'] . '\'</b> on line <b>' . $x[1]['line']  . '</b> Calling function: ' .$funcname, E_USER_ERROR);

		}

	}

	/************************************************
	 * Desc:	allows possibility to do:
	 *				$model->get_column()
	 *				$model->set_column('abc')
	 *
	 *			for any column in the table its modeling
	 *
	 *
	 *
	 * Return:	TRUE if column exists or triggers error
	 *			if column DNE
	 ************************************************/
	
	private function __call($method, $arguments)
	{
	
		switch ( true ) {
		
			case ( preg_match('/^get_(.+)$/', $method, $matches) && isset($this->fields->$matches[1]) ):
			
				if ( !count($arguments) ) {					
					return $this->fields->$matches[1]->value;
				} else {					
					$this->trigger_error('Wrong argument count given for method: <b>' . $method . '()</b> in class <b>\'' . get_class($this) . '\'</b>. None Expected!', E_USER_ERROR);					
				}
				
			break;
		
			case ( preg_match('/^set_(.+)$/', $method, $matches) && isset($this->fields->$matches[1]) ):
			
				if ( count($arguments) == 1 ) {					
					return $this->fields->$matches[1]->value = $arguments[0];
				} else {					
					$this->trigger_error('Wrong argument count given for method: <b>' . $method . '()</b> in class <b>\'' . get_class($this) . '\'</b>. One Expected, ' . 1 . ' Given!', E_USER_ERROR);					
				}
				
			break;
			
			case ( preg_match('/^find_by_(.+)$/', $method, $matches) ):
				$args = $arguments[1];
				$args['conditions'] = "{$matches[1]} = '{$arguments[0]}'";
				$this->find($args);
			break;

			case preg_match('/^find_first_by_(.+)$/', $method, $matches):
				$args = $arguments[1];
				$args['conditions'] = "{$matches[1]} = '{$arguments[0]}'";
				$args['limit'] = 1;
				$this->find($args);
				$this->get_next();
			break;			
		
			default:
				$this->trigger_error('Undefined method <strong>$' . $method . '</strong> in class <strong>\'' . get_class($this) . '\'</strong>', E_USER_ERROR);
			break;
				
		}
	
	
		
	}
	
	private function load_values_into_fields($data, $type = self::ROW_ASSOC)
	{
	
		switch ( $type ) {
		
			default:
				foreach ( $data as $field => $value ) $this->fields->$field->value = $value;
			break;
			
		}
		
	}
	
	private function load_default_values()
	{
		foreach ( $this->fields as $field => $attributes ) $this->fields->$field->value = $attributes->default;
	}
	
	public function get_pointer()
	{
		return $this->select_query->pointer ? $this->select_query->pointer : 0;
	}
	
	public function get_row_count()
	{
		return $this->select_query->row_count ? $this->select_query->row_count : 0;
	}
	
	public function get_insert_id()
	{
		return $this->insert_id;
	}
	
	public function get_next($type = self::ROW_ASSOC)
	{
	
		if ( $this->get_pointer() <= ( $this->get_row_count() - 1 ) ) {
		
			$row = $this->select_query->get_row($this->get_pointer(), $type);
			$this->load_values_into_fields($row, $type);
			$this->select_query->pointer++;
			return $row;
			
		} else {
		
			$this->select_query->pointer--;
			return false;
			
		}
		
	}
	
	public function get_prev($type = self::ROW_ASSOC)
	{

		if ( $this->get_pointer() >= 0 ) {
		
			$row = $this->select_query->get_row($this->get_pointer(), $type);
			$this->load_values_into_fields($row, $type);
			$this->select_query->pointer--;
			return $row;
			
		} else {
		
			$this->select_query->pointer++;
			return false;
			
		}
		
	}
	
	public function get_previous($type = self::ROW_ASSOC)
	{
		$this->get_prev($type);
	}
	
	public function get_first($type = self::ROW_ASSOC)
	{
	
		$this->select_query->pointer = 0;
		$row = $this->select_query->get_row($this->pointer, $type);
		$this->load_values_into_fields($row, $type);
		
		if ( $row ) {
		
			return $row;
			
		} else {
		
			return false;
		
		}
	
	}
	
	public function get_last($type = self::ROW_ASSOC)
	{
	
		$this->select_query->pointer =  $this->row_count - 1;
		$row = $this->select_query->get_row($this->pointer, $type);
		$this->load_values_into_fields($row, $type);
		
		if ( $row ) {

			return $row;
			
		} else {
		
			return false;
			
		}

	}
	
	public function get_values()
	{
		$values = array();
		foreach ($this->fields as $field => $attributes ) $values[$field] = $attributes->value;
		return $values;
	}
	
	/************************************************
	 * Desc:	Queries the database by the qry
	 *			string passed in
	 *
	 * Return:	none
	************************************************/
	
	public function query_records($sql = null)
	{
		if ( !$sql ) $sql = "SELECT * FROM {$this->table}";

		//global $logger;
		//$logger->write('SQL', $sql);

		$this->select_query->reset();
		$this->select_query->query($sql);
	}
	
	
	public function save()
	{

		// if id empty insert new record
		if ( empty($this->fields->id->value) ) {
			$sql = $this->insert_query();
		} else {
			$sql = $this->update_query();
		}
		
		// validate model
		if ( 1 ) {
		
			$this->initialize_action_query();
			$this->action_query->query($sql);
			
			return ( $this->action_query->insert_id ? $this->action_query->insert_id : $this->id );
			
		} else {
		
			return false;
			
		}
		
	}
	
	private function initialize_action_query()
	{
		if ( empty($this->action_query) ) {		
			$this->action_query = $this->establish_connection($this->get_connection_properties());
		}	
	}
	
	private function insert_query()
	{
	
		foreach ( $this->fields as $field => $obj ) {
		
			if ( $obj->extra == 'auto_increment' ) continue;
			
			switch ( true ) {
			
				case ( $field == 'created_at' ):
				case ( $field == 'updated_at' ):
					$fields[$field] = date('Y-m-d H:i:s');
				break;
				
				default:
					$fields[$field] = addslashes($obj->value);
				break;
				
			}
			
		}

		$sql = "INSERT INTO {$this->table_name} (". implode(', ', array_keys($fields)) .") VALUES ('" . implode("', '", array_values($fields)) . "')";
		
		return $sql;
	
	}
	
	private function update_query()
	{
	
		foreach ( $this->fields as $field => $obj ) {
		
			if ( $obj->extra == 'auto_increment' ) continue;
			
			switch ( true ) {
			
				case ( $field == 'updated_at' ):
					$fields[] = $field . " = '" . date('Y-m-d H:i:s') . "'";
				break;
				
				default:
					$fields[] = $field . " = '" . addslashes($obj->value) . "'";
				break;
				
			}
			
		}

		$sql = "UPDATE {$this->table_name} SET " . implode(', ', $fields) . " WHERE id = '{$this->id}'";
	
		return $sql;
	
	}
	
	public function delete()
	{
	
		$this->initialize_action_query();
		$this->action_query->query("DELETE FROM {$this->table_name} WHERE id = '{$this->id}' LIMIT 1");
		
		return $this->id;
	
	}
	
	public function destroy()
	{
		$this->delete();	
	}
	
	/************************************************
	 * Desc:	resets object to its initial state
	 *
	 * Return:	none
	************************************************/
	public function reset()
	{
		$this->load_default_values();
		$this->select_query->reset();
		if ( is_object($this->action_query) )$this->action_query->reset();
	}
	
	/************************************************
	 * Builds and executes a query based on args array
	 * argas array keys that are accepted:
	 *	- total 		-> Builds qry using "select count(id) as total "
	 *  - selected		-> String to use when building SELECT clause
	 *  - conditions	-> String to use when building WHERE clause
	 *  - from			-> String to use when building FROM clause
	 *  - order			-> String to use when building ORDER BY clause
	 *  - limit			-> Number to use when building LIMIT clause
	 *  - offset		-> Number to use when building OFFSET clause, limit must be defined aswell
	 ************************************************/

	function find($args) {
	
		$qry = '';
		
		switch ( true ) {
			case isset($args['total']):
				$qry .= 'SELECT count(*) as total ';
			break;
			case isset($args['selected']):
				$qry .= 'SELECT ' . $args['selected'] . ' ';
			break;
			default:
				$qry .= 'SELECT * ';
			break;
		}
		
		if (isset($args['from'])) {
			$qry .= 'FROM ' . $args['from'] . ' WHERE ';
			$allow_save === false;	
		} else {
			$qry .= 'FROM ' . $this->table_name . ' WHERE ';
		}

		if ( isset($args['conditions']) ) {
			$qry .=  $args['conditions'] . ' ';
		} else {
			$qry .= '1 ';
		}

		if ( isset($args['order']) ) {
			$qry .= 'ORDER BY ' . $args['order'] . ' ';
		}
		
		if (isset($args['limit'])) {

			$qry .= 'LIMIT ';
			
 			if ( isset($args['offset']) ) {
				$qry .= $args['offset'] . ', ';
			}
			
			$qry .=  $args['limit'];
			
		}
		
		return $this->query_records($qry);
		
	}
	
	/************************************************
	 * Desc:	Forces $args['limit'] = 1, then calls $this->find and
	 * 			loads the reacord into the class
	 * Return:	TRUE or FALSE depending on if it found anything
	************************************************/

	function find_first($args = array()) {
		$args['limit'] = 1;
		$this->find($args);
		return $this->get_next();
	}

	/************************************************
	 * Desc:	Forces $args['limit'] = false, then calls $this->find
	 *
	 * Return:	None
	************************************************/

	function find_all($args = array()) {
		unset($args['limit']);
		$this->find($args);
	}

	/************************************************
	 * Desc:	Forces $args['total'] = 1, then calls $this->find
	 *
	 * Return:	Number of records found
	************************************************/
	function find_total($args) {
		$args['total'] = 1;
		unset($args['limit']);
		$this->find($args);

		if ( $row = $this->get_next() ) {
			$this->reset();
			return $row['total'];
		} else {
			$this->reset();
			return false;
		}
	}
	
	/************************************************
	 * Builds and executes a query based on args array
	 * argas array keys that are accepted:
	 *	- total 		-> Builds qry using "select count(id) as total "
	 *  - selected		-> String to use when building SELECT clause
	 *  - conditions	-> String to use when building WHERE clause
	 *  - from			-> String to use when building FROM clause
	 *  - order			-> String to use when building ORDER BY clause
	 *  - limit			-> Number to use when building LIMIT clause
	 *  - offset		-> Number to use when building OFFSET clause, limit must be defined aswell
	 ************************************************/

	function paginate($args = null) {
	
		// create temp args
		$temp = $args;
		$temp['total_records'] = $this->find_total($temp);
		$temp = (object) $temp;
		
		// create page object
		$this->page = new page($temp);
		
		// update agrs with paging data
		$args['offset'] = $this->page->offset;
		$args['limit'] = $this->page->limit;
		
		// execute query
		$this->find($args);
		
	}
	
	// Iterator Interface
	
	function create_child() {
		$item = new $this;
		$item->load_values_into_fields($this->get_values());
		return $item;
	}
	
	
    /**
   	* Return the array "pointer" to the first element
   	* PHP's reset() returns false if the array has no elements
   	*/
 	function rewind(){
		$this->select_query->pointer = 0;
		if ($this->get_next()) {
			$this->interator_valid = true;
		} else {
			$this->interator_valid = false;
		}
 	}

	/**
	* Return the current array element
	*/
	function current() {	 	
		return $this->create_child();
	}

   /**
   * Return the key of the current array element
   */
 	function key() {		
 		return $this->get_id();
 	}

	/**
	* Move forward by one
	* PHP's next() returns false if there are no more elements
	*/
	function next() {		
  		if ($this->get_next()) {		
			return $this->create_child();
		} else {
			$this->interator_valid = false;		
			return false;
		}
	}

	/**
	* Is the current element valid?
	*/
 	function valid() {		
   		return $this->interator_valid;
 	}
	
}
?>
