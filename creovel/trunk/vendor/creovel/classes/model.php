<?

/**
 * Model class
 * 
 */ 

class model implements Iterator {

	
	/**
	* Database the table resides in 
	*
	* @author John Faircloth
	* @access protected
	* @var string
	*/
	static protected $_db_name;
	
	/**
	* Table name the model is representing 
	*
	* @author John Faircloth
	* @access protected
	* @var string 
	*/
	static protected $_table_name;
	
	/**
	* Table columns
	*
	* @author John Faircloth
	* @access private
	* @var object
	*/
	 static protected $_fields;
	 
	/**
	* The primary key column (underscore format).
	* @author John Faircloth
	* @access private
	* @var object
	*/
    protected $_primary_key = 'id';
	
	/**
	* Adapter
	* @author Nesbert Hidalgo
	* @access public
	* @var object
	*/
    public $select_query;
	
	/**
	* Constructor.
	*
	* @author John Faircloth
	* @access public
	* @params string array $data used to load the model with values
	 */
	 
	private $_select;
	private $_from;
	private $_where;
	private $_group;
	private $_order;
	private $_limit;
	private $_offset;
	private $_query_str;
	
	
	public function __construct($data = null, $connection_properties = null) {
		
		$this->select_query = $this->_establish_connection($connection_properties);
		
		if ($adapter) {
			$this->_adpater = $adapter;
		}
		
		$this->_set_table();
		$this->_set_data($data);
		
	}
	
	/**
	* Set Table up
	*
	* @author John Faircloth
	* @access private
	*/
	private function _set_table() {
		
		if (!$this->_table_name) {
			
			$model_name =  str_replace('_model', '', get_class($this));
			$this->_table_name = pluralize($model_name);
		
		}
		
		$this->_db_name = $this->select_query->get_database();
		$this->select_query->set_table($this->_table_name);
		$this->_fields = $this->select_query->get_fields_object();
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
	public function _establish_connection($connection_properties = false)
	{
		if (!is_array($connection_properties)) {
			
			$connection_properties = $this->_get_connection_properties();
		}
		
		switch ( strtolower($connection_properties['adapter']) ) {
		
			case 'mysql':
				$adapter = 'mysql';
			break;
			
			default:
				die('<strong>Error:</strong> Unknown Database Adapter.');
			break;
			
		}
		
		$db_obj = new $adapter($connection_properties);
		
		return $db_obj;
		
	}
	
	/**
	 * Get connection properties for DB as defined in the ENV
	 *
	 * @author Nesbert Hidalgo
	 * @access private
	 * @return array
	 */

	private function _get_connection_properties()
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

	protected function _set_data($data) {
	
	}
	
	/**
	 * Does a describe on the table
	 *
	 * @author Nesbert Hidalgo
	 * @access private
	 */
	 
	public function _get_fields_object()
	{
		// reset class properties
		$this->reset();
		
		// send a DESCRIBE query and set result on success
		$this->select_query->query('DESCRIBE ' . $this->_table_name);
		
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
	
	
	
	
	public function save() { 
		$this->before_save();
	
		$key = $this->key();
		if ($key) {
			$ret_val = $this->update($this->values, $this->_primary_key . " = '" . mysql_real_escape_string($this->key()) . "'");
		} else {
			$ret_val = $this->insert($this->values);
		}
		
		if ($ret_val) {
			$this->after_save();
			return $this->key();
		} else {
			return false;
		}	
		
		
	
	}
	
	public function insert($data) {
		
		foreach ( $this->_fields as $field => $obj ) {
		
			switch ( true ) {
				case ( $field == $this->_primary_key );
					break;			
				case ( $field == 'created_at' ):
				case ( $field == 'updated_at' ):
					$fields[$field] = date('Y-m-d H:i:s');
				break;
				
				default:
					$fields[$field] = addslashes($obj->value);
				break;
				
			}
			
		}

		$sql = "INSERT INTO {$this->_db_name}.{$this->_table_name} (". implode(', ', array_keys($fields)) .") VALUES ('" . implode("', '", array_values($fields)) . "')";
		echo $sql;
	}
	
	public function update($data, $where) {
	
	
	}
	
	public function delete($where) {
		
	}
	
	public function values() {
		$ret = array();
		
		foreach ( $this->_fields as $field => $obj ) {
		
			$ret[$field] = $obj->value;
		}
		
		return $ret;
		
	}
	
	public function find($args = false) {
		$this->reset();
		
	
		switch (true) {
			case isset($args['total']):
				$this->select('count(*) as total ');
				break;
			case isset($args['selected']):
				$this->select($args['selected']);
				break;
			default:
				break;
		}
		
		if (isset($args['from'])) {
			$this->from($args['from']);	
		}
		
		if (isset($args['where'])) {
			$this->where($args['where']);	
		}
		
		if (isset($args['group'])) {
			$this->group($args['group']);	
		}
		
		if (isset($args['order'])) {
			$this->order($args['order']);	
		}

		if (isset($args['limit'])) {
			$this->limit($args['limit']);	
		}
		
		if (isset($args['offset'])) {
			$this->offset($args['offset']);	
		}
		
		return $this->query();
	}
	
	public function find_all($args = null) {
		
		unset($args['limit']);
		unset($args['offset']);
		$this->find($args);
	
	}
	
	public function find_first($args = null) {
		$args['limit'] = 1;
		$this->find($args);
		return $this->next();
	}
	
	public function find_total($args = null) {
	
		$args['total'] = true;
		unset($args['limit']);
		unset($args['offset']);
		unset($args['select']);
		$this->find($args);
		
		$row = $this->select_query->get_row();
		$this->reset();
		return $row['total'];
		
		
		
	}
	
	public function select($select) {
		$this->_select = $select;
	}
	
	public function from($from) {
		$this->_from = $from;
	}
	
	public function where($where) {
		$this->_where = $where;
	}
	
	public function order($order) {
		$this->_order = $order;
	}
	
	public function group($group) {
		$this->_group = $group;
	}
	
	public function limit($limit, $offset = null) {
		$this->_limit = $limit;
		$this->_offset = $offset;
	}
	
	public function query($str = null) {
		if ($str) {
			$this->_query_str = $str; 
		} else {
			$this->_build_qry();
		}
		
		return $this->select_query->query($this->_query_str);
	}
	
	public function query_str($no_breaks = false) {
		if (! $this->_query_str) $this->_build_qry();
		
		if ($no_breaks) {
			return $this->_query_str;
		} else {
			return nl2br($this->_query_str);
		}
	
	}
	
	
	private function _build_qry() {
		
		$str = '';
		
		if ($this->_select) {
			$str .= "SELECT " . $this->_select . "\n";
		} else {
			$str .= "SELECT * \n";
		}
		
		if ($this->_from) {
			$str .= "FROM " . $this->_from . "\n";
		} else {
			$str .= "FROM " . $this->_db_name . "." . $this->_table_name . "\n";
		}
		
		if ($this->_where) {
			$str .= "WHERE " . $this->_where . "\n";
		} else {
			$str .= "WHERE 1 \n";
		}
		
		if ($this->_group) {
			$str .= "GROUP BY " . $this->_group . "\n";
		}
		
		if ($this->_order) {
			$str .= "ORDER BY " . $this->_order . "\n";
		}
		
		if ($this->_offset) {
			$str .= "LIMIT " . $this->_offset . ', ' . $this->_limit . "\n";
		} else {
			if ($this->_limit) {
				$str .= "LIMIT " . $this->_limit . "\n";
			}
		}
		
		$this->_query_str = $str;
	}
	
	public function reset() {
		$this->_select = null;
		$this->_from = null;
		$this->_where = null;
		$this->_group = null;
		$this->_order = null;
		$this->_limit = null;
		$this->_offset = null;
		$this->_query_str = null;
		$this->select_query->reset();
	
	}
	
	
	//iterator interface
	
	    /**
   	* Return the array "pointer" to the first element
   	* PHP's reset() returns false if the array has no elements
   	*/
 	function rewind(){
		
		$this->select_query->rewind();
		$this->next();
	}

   /**
   * Return the current array element
   */
	 function current(){
		return $this->_create_child();
	 }

   /**
   * Return the key of the current array element
   */
 	function key(){
		$function = 'get_' . $this->_primary_key;
		
		return $this->$function();
	}

   /**
   * Move forward by one
   * PHP's next() returns false if there are no more elements
   */
	function next(){
		
  		if ( $this->get_pointer() <= ( $this->row_count() - 1 ) ) {
		
			$row = $this->select_query->get_row($this->get_pointer(), $type);
			$this->load_values_into_fields($row, $type);
			$this->select_query->pointer++;
			$this->_valid = true;
			return $row;
			
		} else {
			$this->_valid = true;
			$this->select_query->pointer--;
			return false;
			
		}
	}

   /**
   * Is the current element valid?
   */
 	function valid(){
		return $this->_valid;
   	}
	
	function _create_child() {
		$item = new $this;
		$item->load_values_into_fields($this->values());
		return $item;
	}
	
	public function get_pointer()
	{
		return $this->select_query->pointer ? $this->select_query->pointer : 0;
	}
	
	public function row_count() {
		
		return $this->select_query->row_count;
	
	}
	
	private function load_values_into_fields($data, $type = self::ROW_ASSOC)
	{
	
		switch ( $type ) {
		
			default:
				foreach ( $data as $field => $value ) $this->_fields->$field->value = $value;
			break;
			
		}
		
	}
	
	function __call($method, $arguments) {
		
		switch (true) {

			case preg_match('/^get_(.+)$/', $method, $regs):
				
				if (isset($this->_fields->$regs[1])) {
					
					if (is_string($this->fields->$regs[1]->value)) {

						return stripslashes($this->fields->$regs[1]->value);

					} else {

						return $this->fields->$regs[1]->value;

					}


				} else {

				echo '<h1>Undefined method: <b>' . $method . '</b> in class <b>\'' . get_class($this) . '\'</b></h1>';
				foreach (debug_backtrace() as $path) {
					echo "<b>File:</b> {$path['file']}<br />";
					echo "<b>Line:</b> {$path['line']}<br />";
					echo "<b>Function:</b> {$path['function']}<br />";
					echo "<hr />";
				}
				die();
			}
			case 	preg_match('/^set_(.+)$/', $method, $regs):

				if (isset($this->fields[$regs[1]])) {

					if ( count($arguments) != 1) {

						$x = debug_backtrace();
							//$funcname = $x[1]['function'];

						trigger_error('Wrong parameter count given for method: <b>' . $method . '()</b> in page <b>\'' . $x[0]['file'] . '\'</b> on line <b>' . $x[0]['line'] .' </b>.<br> One Expected, ' . count($arguments) . ' Given!', E_USER_ERROR);

					} else {

						$this->fields[$regs[1]]['value'] = $arguments[0];

						return true;

					}

				} else {
					//$x = debug_backtrace();
					//trigger_error('Undefined method <b>$' . $method . '</b> in class <b>\'' . get_class($this) . '\'</b> in page <b>\'' . $x[0]['file'] . '\'</b> on line <b>' . $x[0]['line'] .' </b>.', E_USER_ERROR);
					echo '<h1>Undefined method: <b>' . $method . '</b> in class <b>\'' . get_class($this) . '\'</b></h1>';
	 				foreach (debug_backtrace() as $path) {
						echo "<b>File:</b> {$path['file']}<br />";
						echo "<b>Line:</b> {$path['line']}<br />";
						echo "<b>Function:</b> {$path['function']}<br />";
						echo "<hr />";
					}
					die();
	
				}
				break;

			case preg_match('/^find_by_(.+)$/', $method, $regs):
				$args = $arguments[1];
				$args['conditions'] = "{$regs[1]} = '{$arguments[0]}'";
				$args['limit'] = 1;
				$this->find($args);
				break;

			case preg_match('/^find_first_by_(.+)$/', $method, $regs):
				$args = $arguments[1];
				$args['conditions'] = "{$regs[1]} = '{$arguments[0]}'";
				$args['limit'] = 1;
				$this->find($args);
				$this->get_next();
				break;

			case preg_match('/^find_total_by_(.+)$/', $method, $regs):

				$args = $arguments[1];
				$args['conditions'] .= (isset($args['conditions'])) ? " AND {$regs[1]} = '{$arguments[0]}'" : "{$regs[1]} = '{$arguments[0]}'";
				return $this->find_total($args);
				break;
			
			default:
				$x = debug_backtrace();
				trigger_error('Undefined method <b>$' . $method . '</b> in class <b>\'' . get_class($this) . '\'</b>  in page <b>\'' . $x[0]['file'] . '\'</b> on line <b>' . $x[0]['line'] .' </b>.', E_USER_ERROR);
				break;
		}


	}

}


?>