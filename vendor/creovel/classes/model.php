<?php
/*

Class: model
	Model class

Implments:
	Iterator

*/

class model implements Iterator
{
	/*
	
	Property: _db_name
		Database the table resides in 

	*/

	protected $_db_name;
	
	/*
	
	Property: _table_name
		Table name the model is representing 

	*/

	protected $_table_name;
	 
	/*
	
	Property: _primary_key
		The primary key column (underscore format).

	*/

	protected $_primary_key = 'id';
	
	/*
	
	Property: _fields
		Obj stores the current field value pairs loaded into the model

	*/
	
	protected $_fields;
	
	/*
	
	Property: _select_query
		Database adapter used for selection queries on the database

	*/
	
    public $_select_query;
	
	/*
	
	Property: _action_query
		Database adapter used for inserts updates deletion and find totals

	*/
    public $_action_query;
	
	/*
	
	Property: _links
		Stores the has_* relationshiop configuration

	*/
	
    public $_links = array();
	
	/*
	
	Property: _valid
		TODO

	*/
	
    public $_valid = array();
	
	/*
	
	Property: _page
		Paginate obj used to paginate select quries for this internal model

	*/
	
	public $page;
	
	/*
	
	Property: errors
		error obj used to store validation errors

	*/
	
	public $errors;
	
	/*
	
	Property: _validation
		validation obj used to run validation routines on values in model

	*/
	
	public $validation;
	
	/*
	
	Property: _select
		String that holds the select portion of a query

	*/
	
	private $_select;
	
	/*
	
	Property: _from
		String that holds the from portion of a query

	*/
	
	private $_from;
	
	/*
	
	Property: _where
		String that holds the where portion of a query

	*/
	
	private $_where;
	
	/*
	
	Property: _group
		String that holds the group portion of a query

	*/
	
	private $_group;
	
	/*
	
	Property: _order
		String that holds the order by portion of a query

	*/
	
	private $_order;
	
	/*
	
	Property: _limit
		String that holds the limit portion of a query

	*/
	
	private $_limit;
	
	/*
	
	Property: _offset
		String that holds the offset portion of a query

	*/
	
	private $_offset;
	
	/*
	
	Property: _query_str
		String that holds the entrire query built form _select, _from, _where, _group, _order, _limit, _offset

	*/
	private $_query_str;

	/*
	
	Property: _child_objects
		TODO

	*/
	
	private $child_objects;

	// Section: Public
	
	/*
	
	Function: __construct
		Constructor.

	Parameters	
		data - used to load the model with values
		connection_properties - overrides current creovel database defaults
			example array(
						'adapter' => 'mysql'
						
			
			
						)
	*/	 

	public function __construct($data = null, $connection_properties = null)
	{
		
		$this->errors = new error(get_class($this));
		$this->validation = new validation($this->errors);

		if ($connection_properties['table_name']) {
			$this->_table_name = $connection_properties['table_name'];
		}
		if ($connection_properties['database']) {
			$this->_db_name = $connection_properties['database'];
		}
		$this->_select_query = $this->establish_connection($connection_properties);
		$this->_action_query = $this->establish_connection($connection_properties);		
		
		if ($adapter) $this->_adapter = $adapter;
		
		$this->_set_table();
		$this->_set_data($data);

		$this->child_objects = array();
	}
	
	/*
			
	Function:	
		Choose the correct DB adapter to use and sets its properties.
		Returns an DB Layer object.

	Parameters:	
		db_properties - required

	Returns:
		object

	*/

	public function establish_connection($connection_properties = false)
	{
		if (!is_array($connection_properties)) {
			$connection_properties = $this->_get_connection_properties();
		}
		
		switch ( strtolower($connection_properties['adapter']) ) {
		
			case 'mysql':
				$adapter = 'mysql';
			break;

			case 'sqlite':
				$adapter = 'sqlite';
			break;
			
			default:
				die('<strong>Error:</strong> Unknown Database Adapter.');
			break;
			
		}
		
		$db_obj = new $adapter($connection_properties);
		
		return $db_obj;
	}
	
	/*
	
	Function: _set_data

	*/

	public function _set_data($data)
	{
		if ($data)
		{
			if (is_array($data)) {
				if (isset($data[$this->_primary_key])) {
					$function = 'set_' . $this->_primary_key;
					$this->$function($data[$this->_primary_key]);
					
				}
						
				foreach($data as $name => $value) {
					if ($name != $this->_primary_key) {
							
						$function = 'set_' . $name;
						
						$this->$function($value);
					}
				}
			} else {
				$function = 'set_' . $this->_primary_key;
				$this->$function($data);
			}
		}
	}
	
	public function get_fields_object()
	{
		return $this->_fields;
	}

	/*

	Function: update_field
		Updates a single field and saves the object.
		Bypasses validation.

	Parameters:
		name - field name
		value - value

	*/

	public function update_field($name, $value)
	{
		$this->update(array( 'id' => $this->key(), $name => $value ));
	}

	/*
	
	Function: validate_model
		Runs the validation on the model.

	Returns:
		bool

	*/	

	public function validate_model()
	{
		// validate model on every save
		$this->validate();
		// if error return false		
		if ( $this->errors->has_errors() ) return false;
		
		return true;
	}

	/*
	
	Function: save
		Saves the model to the database.
		Either calles <insert> or <update> depending on if the record exists.

	Returns:
		int or false

	*/	

	public function save()
	{
		$this->before_save();

		if (!$this->validate_model()) return false;
		
		if ( $key = $this->key() ) {
		
			// validate model on every update
			$this->validate_on_update();
			// if error return false
			if ( $this->errors->has_errors() ) return false;
			
			$ret_val = $this->update($this->values(), $this->_primary_key . " = '" . $this->key() . "'");
			
		} else {
		
			// validate model on every insert
			$this->validate_on_create();	
			// if error return false
			if ( $this->errors->has_errors() ) return false;
			
			$this->before_create();
			
			$ret_val = $this->insert($this->values());
			
		}

		foreach ($this->child_objects as $obj) $obj->save();
		
		if ( $ret_val ) {
			$this->after_save();
			return $this->key();
		} else {
			return false;
		}	
	}

	/*
	
	Function: insert
		Insert the model into the database.

	Parameters:
		data - array of key => values	

	Returns:
		int
	
	*/

	public function insert($data)
	{
		$qry = "INSERT INTO {$this->_table_name} (";
		
		foreach ($data as $name => $value) {
			
			if ($name == $this->_primary_key) {				
				continue;
			}

			$qry .= $name . ', ';

		}		
		
		$qry = substr($qry, 0, -2) . ') VALUES (';
		
		foreach ($data as $name => $value) {

			if ($name == $this->_primary_key) {
				continue;
			}
			$this->_fields->$name->value = $value;
			$obj = $this->_fields->$name;
			
			if ($name == 'created_at') {
				
				$qry .= "'" . date('Y-m-d H:i:s')  . "', ";

			} elseif ($name == 'updated_at') {
				
				$qry .= "'" . date('Y-m-d H:i:s')  . "', ";

			} elseif ($obj->null == 'YES' && ($obj->value === '' || $obj->value === null)) {
			
				$qry .= "NULL, ";
				
			} elseif (is_bool($obj->value)) {
				
				$qry .= "'" . ($obj->value ? 1 : 0) . "', ";
				
			} elseif (is_numeric($obj->value)) {
				
				$qry .= "'" . $obj->value . "', ";
				
			} elseif (is_string($obj->value)) {
				
				$qry .= "'" . addslashes(trim($obj->value)) . "', ";
				
			} elseif (is_array($obj->value)) {
			
				// if datetime save array
				if ( $this->_fields->$name->type == 'datetime' ) {
					$qry .= "'".datetime($obj->value)."', ";
				} else {				
					$qry .= "'" . addslashes(serialize($obj->value)) . "', ";					
				}
							
			} else {
				
				$qry .= "'" . $obj->default . "', ";
				
			}

		}
		
		$qry = substr($qry, 0, -2) ;
		
		$qry .= ')';
		$this->_action_query->query($qry);
		
		$key = $this->_primary_key;
		$this->_fields->$key->value =  $this->_action_query->get_insert_id();
		
		return $this->key(); 
	}

	/*
	
	Function: update
		Updates the model in the database.

	Parameters:
		data -  Array of key => values	

	Returns:
		int

	*/

	public function update($data)
	{
		$qry = "UPDATE {$this->_table_name} SET ";
		
		foreach ($data as $name => $value) {

			if ($name == $this->_primary_key) {
				continue;

			}
			$this->_fields->$name->value = $value;
			$obj = $this->_fields->$name;
			
			if ($name == 'created_at') {
				
				continue;			
	
			} elseif ($name == 'updated_at') {
				
				$qry .= $name . " = '" . date('Y-m-d H:i:s')  . "', ";

			} elseif ($obj->null == 'YES' && ($obj->value === '' || $obj->value === null)) {
			
				$qry .= $name . " = " . "NULL, ";
				
			} elseif (is_bool($obj->value)) {
				
				$qry .= $name . " = " . "'" . ($obj->value ? 1 : 0) . "', ";
				

			} elseif (is_numeric($obj->value)) {
				
				$qry .= $name . " = " . "'" . $obj->value . "', ";
				
			} elseif (is_string($obj->value)) {
				
				$qry .= $name . " = " . "'" . addslashes(trim($obj->value)) . "', ";
				
			} elseif (is_array($obj->value)) {
				
				// if datetime save array
				if ( $this->_fields->$name->type == 'datetime' ) {
					$qry .= $name . " = " . "'".datetime($obj->value)."', ";
				} else {				
					$qry .= $name . " = " . "'" . addslashes(serialize($obj->value)) . "', ";					
				}
							
			}else {
				
				$qry .= $name . " = " . "'" . $obj->default . "', ";
				
			}

			
		}

		$qry = substr($qry, 0, -2) ;
		$key = $data[$this->_primary_key];
		$qry .= " WHERE {$this->_primary_key} = '{$key}'";
		$this->_action_query->query($qry);
		
		//$key = $this->_primary_key;
		//$this->_fields->$key->value = $this->_action_query->insert_id;
		
		return $key; 
	}

	/*

	Function: delete
		Deletes the model from the database.

	Parameters:
		where - Argument to the SQL query.

	Returns:
		int

	*/

	public function delete($where = null)
	{
		// if no $where	delete current load record
		if ( !$where ) {
			$property = $this->_primary_key;
			$where = "{$this->_primary_key} = '{$this->$property}' LIMIT 1";
		}
		
		$this->before_delete();
		$this->_action_query->query("DELETE FROM {$this->_table_name} WHERE $where");
		$this->after_delete();
		
		return $this->_action_query->row_count;
	}

	/*
	
	Function: values
		The values of the current model.

	Returns:
		array
	*/

	public function values()
	{
		$ret = array();
		
		foreach ( $this->_fields as $field => $obj ) {
			$function = 'get_'.$field;
			$ret[$field] = $this->$function();
		}
		
		return $ret;
	}

	/*

	Function: find
		Find macthing records in the database.

	Parameters:
		args - Array of options.

	Returns:
		Iterator

	*/

	public function find($args = false)
	{
		if (!isset($args['total'])) $this->reset();

		$this->before_find();	
		
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
		
		if (isset($args['total'])) {
			$result = $this->query_action();
		} else {
			$result = $this->query();

			$this->after_find();
		}

		return $result;
	}

	/*

	Function: find_all
		Find macthing records in the database.

	Parameters:
		args - Array of options.

	Returns:
		Iterator

	*/

	public function find_all($args = null)
	{
		unset($args['limit']);
		unset($args['offset']);
		$this->find($args);
	}

	/*

	Function: find_first
		Find first matching record in the database.

	Parameters:
		args - Array of options.

	Returns:
		object

	*/
	
	public function find_first($args = null)
	{
		$args['limit'] = 1;
		$this->find($args);
		return $this->next();
	}

	/*

	Function: find_total
		Find total number of matching records in the database.

	Parameters:
		args - Array of options.

	Returns:
		Iterator

	*/
	
	public function find_total($args = null)
	{
		$args['total'] = true;
		unset($args['limit']);
		unset($args['offset']);
		unset($args['select']);
		$this->find($args);
		
		$row = $this->_action_query->get_row();
		//$this->reset();
		return $row['total'];
	}

	/*

	Function: select
		Set the Select argument for the query.

	Parameters:
		select - Select argument.

	*/

	public function select($select)
	{
		$this->_select = $select;
	}

	/*

	Function: from
		Set the From argument for the query.

	Parameters:
		from - From argument.

	*/
	
	public function from($from)
	{
		$this->_from = $from;
	}

	/*

	Function: where
		Set the Where argument for the query.

	Parameters:
		where - Where argument.

	*/
	
	public function where($where) {
		$this->_where = $where;
	}

	/*

	Function: select
		Set the Select argument for the query.

	Parameters:
		select - Select argument.

	*/
	
	public function order($order) {
		$this->_order = $order;
	}

	/*

	Function: group
		Set the Group argument for the query.

	Parameters:
		group - Group argument.

	*/
	
	public function group($group) {
		$this->_group = $group;
	}

	/*

	Function: offset
		Set the Offset argument for the query.

	Parameters:
		offset - Query offset

	*/

	public function offset($offset)
	{
		$this->_offset = $offset;
	}
	
	/*

	Function: limit
		Set the Limit argument for the query.

	Parameters:
		limit - Limit number of records
		offset - Query offset

	*/
	
	public function limit($limit, $offset = null) {
		$this->_limit = $limit;
		$this->_offset = $offset;
	}
	
	/*

	Function: query

	Parameters:
		str - 

	Returns:

	*/
	
	public function query($str = null)
	{
		if ($str) {
			$this->_query_str = $str; 
		} else {
			$this->_build_qry();
		}
		
		return $this->_select_query->query($this->_query_str);
	}
	
	/*

	Function: query_action

	Parameters:
		str - mysql query string

	Returns:
		mysql result set

	*/
	
	public function query_action($str = null)
	{
		if ($str) {
			$this->_query_str = $str; 
		} else {
			$this->_build_qry();
		}
		
		return $this->_action_query->query($this->_query_str);
	}
	
	/*

	Function: query_str
		Query string

	Parameters:
		no_breaks - Turn new lines into breaks

	Retruns:
		string

	*/

	public function query_str($no_breaks = false)
	{
		if (! $this->_query_str) $this->_build_qry();
		
		if ($no_breaks) {
			return $this->_query_str;
		} else {
			return nl2br($this->_query_str);
		}
	
	}

	/*

	Function: reset
		Reset model object.

	*/

	public function reset()
	{
		$this->_select = null;
		$this->_from = null;
		$this->_where = null;
		$this->_group = null;
		$this->_order = null;
		$this->_limit = null;
		$this->_offset = null;
		$this->_query_str = null;
		$this->_select_query->reset();
	}
	
	/*

	Function: key
		The value of the primary key.

	Returns:
		mixed

	*/

	public function key()
	{
		$function = 'get_' . $this->_primary_key;

		return $this->$function();
	}
	
	/*

	Function: get_pointer

	Returns:
		int

	*/

	public function get_pointer()
	{
		return $this->_select_query->pointer ? $this->_select_query->pointer : 0;
	}
	
	/*

	Function: row_count
		Number of rows the query returned.

	Returns:
		int

	*/

	public function row_count() {
		
		return $this->_select_query->row_count;
	
	}

	/*
	
	Function: total
		Alias to row_count
	
	Returns:
		int
	
	*/

	public function total()
	{
		return $this->row_count();
	}
	
	/*

	Function: get_affected_rows
		Number of rows affected by the query.

	Returns:
		int

	*/

	public function get_affected_rows()
	{
		return $this->_select_query->get_affected_rows();
	}

	/*
	
	Function: __call
		Magic Functions
	 	Yeah it doesn't get any bigger than this.
		We need some examples.

	Parameters:
		method - name of function being called
		arguments - passed to the function

	Returns:
		mixed

	*/

	public function &__call($method, $arguments)
	{
	
		try {
		
			switch ( true ) {
	
				case preg_match('/^get_(.+)$/', $method, $regs):
					
					if ( isset($this->_fields->$regs[1]) ) {
						
						if ( is_string($this->_fields->$regs[1]->value) ) {
	
							$data = unserialize($this->_fields->$regs[1]->value);
							if ($data !== false) {
								return $data;
							} else {
								return stripslashes($this->_fields->$regs[1]->value);
							}
	
						} else {
	
							return $this->_fields->$regs[1]->value;
	
						}
	
					} else {
	
						throw new Exception("Property '{$property}' not found in <strong>".get_class($this)."</strong> model.");
						
					}
					
				break;
				
				case preg_match('/^set_(.+)$/', $method, $regs):
	
					if (isset($this->_fields->$regs[1])) {
	
						if ( count($arguments) != 1) {
	
							throw new Exception("Too many parameters for method '{$method}' in <strong>".get_class($this)."</strong> model. One expected, ".count($arguments)." given.");
	
						} else {
	
							if ( $regs[1] == $this->_primary_key ) {
							
								$this->reset();
								
								$this->_select_query->query("SELECT * FROM {$this->_table_name} WHERE {$this->_primary_key} = '{$arguments[0]}'");
								
								if ( $this->_select_query->row_count ) {
										
									$row = $this->_select_query->get_row();
					
									$this->load_values_into_fields($row, $type);
					
									return true;
					
								} else {
					
									return false;
								
								}
	
							
							} else {
							
								$this->_fields->$regs[1]->value = $arguments[0];
							
							}
							
							return true;
	
						}
	
					} else {
					
						throw new Exception("Property '{$property}' not found in <strong>".get_class($this)."</strong> model.");
		
					}
				
				break;
	
				case preg_match('/^find_by_(.+)$/', $method, $regs):
					$args['where'] = $this->_conditions_str_from_method($method, $arguments);
					//$args['limit'] = 1;
					$this->find($args);
					break;
	
				case preg_match('/^find_first_by_(.+)$/', $method, $regs):
					$args['where'] = $this->_conditions_str_from_method($method, $arguments);
					$args['limit'] = 1;
					$this->find($args);
					$this->next();
				break;
	
				case preg_match('/^find_total_by_(.+)$/', $method, $regs):
					$args['where'] = $this->_conditions_str_from_method($method, $arguments);
					return $this->find_total($args);
					break;
					
				case ( preg_match('/^validates_(.+)$/', $method, $regs) ):
					$this->_validate_by_method($method, $arguments);
				break;
				
				/* Paging Links */
				case ( preg_match('/^link_to_(.+)$/', $method, $regs) ):
				case ( preg_match('/^paging_(.+)$/', $method, $regs) ):
					if ( method_exists($this->page, $method) ) {
						return call_user_func_array(array($this->page, $method), $arguments);								
					} else {
						throw new Exception("Undefined method '{$method}' in <strong>".get_class($this)."</strong> model.");
					}
				break;
				
				case preg_match('/^text_field_for_(.+)$/', $method, $regs):
					$html_options = $arguments[0];
					$get_function = 'get_' . $regs[1];
					$error_function = 'error_for_' . $regs[1];
					
					$html_str = text_field($this->_class(). '[' . $regs[1] . ']', $this->$get_function(), $html_options);
					
					return $html_str;
				
				break;
				
				case preg_match('/^password_field_for_(.+)$/', $method, $regs):
					$html_options = $arguments[0];
					$function = 'get_' . $regs[1];
					return password_field($this->_class(). '[' . $regs[1] . ']', $this->$function(), $html_options);
				break;
				
				case preg_match('/^textarea_for_(.+)$/', $method, $regs):
					$html_options = $arguments[0];
					$function = 'get_' . $regs[1];
					
					return textarea($this->_class(). '[' . $regs[1] . ']', $this->$function(), $html_options);
				break;
				
				case preg_match('/^hidden_field_for_(.+)$/', $method, $regs):
					$html_options = $arguments[0];
					$function = 'get_' . $regs[1];
					return hidden_field($this->_class(). '[' . $regs[1] . ']', $this->$function(), $html_options);
				break;
				
				
				case preg_match('/^select_for_(.+)$/', $method, $regs):
					$options = $arguments[0];
					$html_options = $arguments[1];
					$function = 'get_' . $regs[1];
					return select($this->_class(). '[' . $regs[1] . ']', $this->$function(), $options, $html_options);
				break;
				
				case preg_match('/^label_for_(.+)$/', $method, $regs):
					$title = $arguments[0];
					$html_options = $arguments[1];
					return label($this->_class(). '[' . $regs[1] . ']', $title, $html_options);
				break;
			
				case preg_match('/^error_for_(.+)$/', $method, $regs):
					if ($this->errors->$regs[1]) {
						return true;
					} else {
						return false;
					}
				break;
	
				case preg_match('/^radio_button_for_(.+)$/', $method, $regs):
					$value = $arguments[0];
					$text = $arguments[1] ? $arguments[1] : humanize($value);
					$html_options = $arguments[2];
					$function = 'get_' . $regs[1];
					
					return radio_button($this->_class(). '[' . $regs[1] . ']', $value, $html_options, $this->$function(), $text);
				break;
				
				case preg_match('/^check_box_for_(.+)$/', $method, $regs):
					$value = $arguments[0];
					$text = $arguments[1] ? $arguments[1] : humanize($value);
					$html_options = $arguments[2];
					$function = 'get_' . $regs[1];
					
					return check_box($this->_class(). '[' . $regs[1] . ']', $value, $html_options, $this->$function(), $text);
				break;
				
				case preg_match('/^multi_check_box_for_(.+)$/', $method, $regs):
					$value = $arguments[0];
					   
					$text = $arguments[1] ? $arguments[1] : humanize($value);
					$html_options = $arguments[2];
					$function = 'get_' . $regs[1];
					
					return check_box($this->_class(). '[' . $regs[1] . '][]', $value, $html_options, $this->$function(), $text);
				break;
				
				case preg_match('/^new_(.+)$/', $method, $regs):
					$object = new $regs[1]();
					$foreign_key = singularize($this->_table_name)."_id";
					$object->$foreign_key = $this->id;
					$this->child_objects[] = $object;
					return $this->child_objects[count($this->child_objects) - 1];
					break;

				case preg_match('/^add_(.+)$/', $method, $regs):
					$foreign_key = singularize($this->_table_name)."_id";

					foreach ($arguments as $object)
					{
						$object->$foreign_key = $this->id;
						$this->child_objects[] = $object;
					}
				break;
	
				default:
					throw new Exception("Undefined method '{$method}' in <strong>".get_class($this)."</strong> model.");
				break;
					
			}

		} catch ( Exception $e ) {
		
			// add to errors
			$_ENV['error']->add($e->getMessage(), $e);
		
		}
	}

	/*
	
	Function: __set
		Magic function to set values.

	Parameters:
		property - field name	
		value - value

	Returns:
		mixed

	*/

	public function __set($property, $value)
	{
		try {
				
			if ( isset($this->_fields->$property) ) {
	
				$function = 'set_' . $property;
				return $this->$function($value);
			
			} else {
			
				throw new Exception("Property '{$property}' not found in <strong>".get_class($this)."</strong> model.");
	
			}
			
		} catch ( Exception $e ) {
		
			// add to errors
			$_ENV['error']->add($e->getMessage(), $e);
		
		}		
	}

	/*
	
	Function: __get
		Magic function to get values.

	Parameters:
		property - field name	

	Returns:
		mixed

	*/
	
	public function __get($property)
	{
		try {
				
			if ( isset($this->_fields->$property) ) {
			
				$function = 'get_' . $property;
				return $this->$function($value);
			
			} else if (array_key_exists($property, $this->_links)) {
	
				return $this->get_link_object($property);
			
			} else {
			
				throw new Exception("Property '{$property}' not found in <strong>".get_class($this)."</strong> model.");
				
			}
			
		} catch ( Exception $e ) {
		
			// add to errors
			$_ENV['error']->add($e->getMessage(), $e);
		
		}		
	}

	/*
	
	Function: paginate
		Alias to find and set the $page object. default page limit is 10 records

	Parameters:	
		args - optional 

	*/

	public function paginate($args = null)
	{
		// create temp args
		$temp = $args;
		unset($temp['offset']);
		$temp['total_records'] = $this->find_total($temp);
		$temp = (object) $temp;
		
		// create page object
		$this->page = new pager($temp);
		
		// update agrs with paging data
		$args['offset'] = $this->page->offset;
		$args['limit'] = $this->page->limit;
		
		// execute query
		$this->find($args);
	}
	
	/*
	
	Function: is_valid
		Validate model object.

	Returns:
		bool

	*/

    public function is_valid()
    {
        // validate model on every save
        $this->validate();
        
        // if error return false        
        if ( $this->errors->has_errors() ) {
            return false;
        } else {
            return true;
        }    
    }

    /*

	Function: table_exists
		Check if a table exists in the current database.

	Returns:
		bool

	*/

    public function table_exists($table_name)
    {
        $db_obj = self::establish_connection( self::_get_connection_properties() );
        return $db_obj->table_exists($table_name);
    }

    /*

	Function: all_tables
		Get all the tables in a database.

	Returns:
		bool

	*/

	public function all_tables()
	{
        $db_obj = self::establish_connection( self::_get_connection_properties() );
		return $db_obj->all_tables();
	}

    /*

	Function: field_breakdown
		Metadata about the fields in a table.

	Returns:
		bool

	*/

	public function field_breakdown($table_name)
	{
        $db_obj = self::establish_connection( self::_get_connection_properties() );
        return $db_obj->field_breakdown($table_name);
	}

    /*

	Function: key_breakdown
		Metadata about the fields in a table.

	Returns:
		bool

	*/

	public function key_breakdown($table_name)
	{
        $db_obj = self::establish_connection( self::_get_connection_properties() );
        return $db_obj->key_breakdown($table_name);
	}

	/*
	
	Function: has_many
		Creates a link between objects.

	Parameters:
		name - link_name
		options - array of options

	*/

	public function has_many($name, $options = array())
	{
		$this->_links[$name]['type'] = 'has_many';
		$this->_links[$name]['options'] = $options;
		$this->_links[$name]['linked_to'] = false;
		$this->_links[$name]['object'] = false;
	}

	/*
	
	Function: belongs_to
		Creates a link between objects.

	Parameters:
		name - link_name
		options - array of options

	*/
	
	public function belongs_to($name, $options = array())
	{
		$this->_links[$name]['type'] = 'belongs_to';
		$this->_links[$name]['options'] = $options;
		$this->_links[$name]['linked_to'] = false;
		$this->_links[$name]['object'] = false;
	}

	/*
	
	Function: has_many_link
		Creates a link between objects.

	Parameters:
		name - link_name
		options - array of options

	*/
	
	public function has_many_link($name, $options = array())
	{
		$this->_links[$name]['type'] = 'has_many_link';
		$this->_links[$name]['options'] = $options;
		$this->_links[$name]['linked_to'] = false;
		$this->_links[$name]['object'] = false;
	}

	/*
	
	Function: has_one
		Creates a link between objects.

	Parameters:
		name - link_name
		options - array of options

	*/
	
	public function has_one($name, $options = array())
	{
		$this->_links[$name]['type'] = 'has_one';
		$this->_links[$name]['options'] = $options;
		$this->_links[$name]['linked_to'] = false;
		$this->_links[$name]['object'] = false;
	}


	public function transaction_start()
	{
		$this->query_action($this->_action_query->transaction_string('start'));
	}

	public function transaction_commit()
	{
		$this->query_action($this->_action_query->transaction_string('commit'));
	}

	public function transaction_rollback()
	{
		$this->query_action($this->_action_query->transaction_string('rollback'));
	}

	// Section: Private	

	/*
	
	Function: _set_table
		Set Table up

	*/

	private function _set_table()
	{
		if (!$this->_table_name) {
			$model_name =  $this->_class();
			$this->_table_name = pluralize($model_name);
		}
		if ($this->_db_name) {
			$this->_table_name = $this->_db_name.'.'.$this->_table_name;
		}
		//$this->_db_name = $this->_select_query->get_database();
		$this->_select_query->set_table($this->_table_name);
		$this->_fields = $this->_select_query->get_fields_object();
	}

	/*

	Function: _class

	*/

	private function _class()
	{
		return get_class($this);			
	}
	
	/*
	
	Function:
		Get connection properties for DB as defined in the ENV

	Returns:	
		array

	*/

	private function _get_connection_properties()
	{
		switch ( $_ENV['mode'] )
		{
			case 'production':
				$_ENV['production']['mode'] = 'production';
				return $_ENV['production'];
			break;
		
			case 'test':
				$_ENV['test']['mode'] = 'test';
				return $_ENV['test'];
			break;
		
			case 'development':
			default:
				$_ENV['development']['mode'] = 'development';
				return $_ENV['development'];
			break;
		
		}
	}

	/*
	
	Function:
		Creates an object mapped to the current table's structure

	Returns:
		array

	*/	 

	private function _get_fields_object()
	{
		// reset class properties
		$this->reset();
		
		// send a DESCRIBE query and set result on success
		$this->_select_query->query('DESCRIBE ' . $this->_table_name);
		
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

	/*

	Function: _build_qry
		Builds the query string from the settings provided.

	Returns:
		string

	*/

	private function _build_qry()
	{
		
		$str = '';
		
		if ($this->_select) {
			$str .= "SELECT " . $this->_select . "\n";
		} else {
			$str .= "SELECT * \n";
		}
		
		if ($this->_from) {
			$str .= "FROM " . $this->_from . "\n";
		} else {
			$str .= "FROM " . $this->_table_name . "\n";
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

	/*

	Function: _build_qry
		Builds the query string from the settings provided.

	Returns:
		string

	*/
	
	private function load_values_into_fields($data, $type = self::ROW_ASSOC)
	{
	
		switch ( $type ) {
		
			default:
				foreach ( $data as $field => $value ) $this->_fields->$field->value = $value;
			break;
			
		}
		
	}

	/*

	Function: get_link_object
		Returns the link associated with this name.	

	Parameters:
		name - Link Name.

	Returns:
		object

	*/
	
	private function get_link_object($name)
	{
		if (!$this->is_linked($name)) {
	
			if(!$this->create_link($name)) {
				return false;
			}
		} 
	
		return $this->_links[$name]['object'];
	}

	/*

	Function: is_linked
		Is the name passed linked to another object.

	Returns:
		bool

	*/
	
	private function is_linked($name)
	{
		if ($this->key()) {
			if ($this->_links[$name]['linked_to'] == $this->key()) {
				return true;
			} else {
				
				return false;
			}	
		} else {
			return false;
		}
	}

	/*

	Function: create_link
		Establish a link between objects.

	Returns:
		bool

	*/
	
	private function create_link($name)
	{
		if ($this->_links[$name]['options']['class_name']) {
			
			$model_name = $this->_links[$name]['options']['class_name'];
			
		} else {

			$model_name = singularize($name);
		
		}
		
		$model_obj = new $model_name();
		$args = $this->_links[$name]['options'];
	
		if (!$this->_links[$name]['options']['foreign_key']) {
			if ($this->_links[$name]['type'] == 'belongs_to') {
				$this->_links[$name]['options']['foreign_key'] = $model_name . '_id';
			} else {
				$this->_links[$name]['options']['foreign_key'] = $this->_class() . '_id';
			}
		}		

		if ($this->get_id()) {
			
			switch($this->_links[$name]['type']) {
				case 'has_many':
					if ($args['where']) {
						$args['where'] = ' ' . $this->_links[$name]['options']['foreign_key'] . " = '" . $this->key() . "' and (" . $args['where'] . ")" ; 
					} else {
						$args['where'] = ' ' . $this->_links[$name]['options']['foreign_key'] . " = '" . $this->key() . "'";
					}
					
					$model_obj->find($args);
					
					break;
				case 'has_many_link':

					if ($this->table_exists("{$name}_{$this->_table_name}")) {

						$this->_links[$name]['link_table_name']	= "{$name}_{$this->_table_name}";
						$this->_links[$name]['this_table_id']	= singularize($this->_table_name).'_id';
						$this->_links[$name]['other_table_id']	= singularize($name).'_id';

						if ($args['where']) {
							$args['where'] = "{$this->_links[$name]['other_table_id']} = {$model_obj->_table_name}.id AND {$this->_links[$name]['this_table_id']} = {$this->id} AND ({$args['where']})";
						} else {
							$args['where'] = "{$this->_links[$name]['other_table_id']} = {$model_obj->_table_name}.id AND {$this->_links[$name]['this_table_id']} = {$this->id}";
						}

					} else {

						$this->_links[$name]['link_table_name']	= "{$this->_table_name}_{$name}";
						$this->_links[$name]['this_table_id']	= singularize($name).'_id';
						$this->_links[$name]['other_table_id']	= singularize($this->_table_name).'_id';

						if ($args['where']) {
							$args['where'] = "{$this->_links[$name]['this_table_id']} = {$model_obj->_table_name}.id AND {$this->_links[$name]['other_table_id']} = {$this->id} AND ({$args['where']})";
						} else {
							$args['where'] = "{$this->_links[$name]['this_table_id']} = {$model_obj->_table_name}.id AND {$this->_links[$name]['other_table_id']} = {$this->id}";
						}

					}

					$args['from'] = "{$this->_links[$name]['link_table_name']}, {$name}";

					$model_obj->find($args);
				  
					break;
				case 'has_one':
					if ($args['where']) {
						$args['where'] = ' ' . $this->_links[$name]['options']['foreign_key'] . " = '" . $this->key() . "' and (" . $args['where'] . ")" ; 
					} else {
						$args['where'] = ' ' . $this->_links[$name]['options']['foreign_key'] . " = '" . $this->key() . "'";
					}

					$model_obj->find_first($args);
					
					if ($model_obj->_select_query->row_count == 0) $model_obj = false;

					break;

				case 'belongs_to':
					case 'belongs_to':
					$function = 'get_'.$this->_links[$name]['options']['foreign_key'];
					
					$args['where'] .= " id = '".$this->$function()."' "; 
					
					$model_obj->find_first($args);

					break;
				
			}

		} else {

			switch($this->_links[$name]['type'])
			{
				case 'belongs_to':
					$function = 'get_'.$this->_links[$name]['options']['foreign_key'];
					
					$args['where'] .= " id = '".$this->$function()."' "; 
					
					$model_obj->find_first($args);
					break;
			}

		}
				
		$this->_links[$name]['object'] = $model_obj;
		$this->_links[$name]['linked_to'] = $this->key();
		
		return true;
	}
	
	/*

	Function: _validate_by_method
		Validate by method name to validation object.

	Parameters:	
		method - required
		args - optional

	*/

	private function _validate_by_method($method, $args = null)
	{
		// if no value pasted use model's value
		if ( !$args[1] ) $args[1] = $this->$args[0];
		
		switch ( $method ) {
		
			case 'validates_uniqueness_of':
			
				if ($args[3]) {
					$where_ext = $args[3];	
				} else {
					$where_ext = '1';	
				}
				// check if a column with that value exists in the current table and is not the currentlly loaded row
				$this->_action_query->query("SELECT * FROM {$this->_table_name} WHERE {$args[0]} = '{$args[1]}' AND {$this->_primary_key} != '{$this->id}' and (".$where_ext.")");


				// if record found add error
				if ( $this->_action_query->row_count ) {
					$this->errors->add($args[0], ( $args[2] ? $args[2] : humanize($args[0]).' is not unique.' ));
				} else {
					return true;				
				}
			break;
			
			default:
				if ( method_exists($this->validation, $method) ) {
					return call_user_func_array(array($this->validation, $method), $args);
				} else {
					$_ENV['error']->add("Undefined validation '{$method->_action}' in <strong>{get_class()}</strong>");
				}
			break;
			
		}
	}
	
	/*
	
	Function: _condition_str_from_method
		Creates SQL string for conditons used for find_by... magic funtions

	Parameters:	
		method - required
		args - required

	Returns:
		string

	*/

	private function _conditions_str_from_method($method, $args)
	{
		// remove find_by... from method name
		$method = str_replace(array('find_by_','find_first_by_', 'find_total_by_'), '', $method);
		$args_index = 0;
		$return = '';
		
		// if no "AND" or "OR" return single column sql
		if ( !strstr($method, '_and_') && !strstr($method, '_or_') ) {
			return $this->_conditions_str_helper($method, $args[0]);
		}
		
		// hande "OR" and create/return sql string
		if ( strstr($method, '_or_') ) {
			$ors = explode('_or_', $method);
			$or_count = 1;
			foreach ( $ors as $or ) {
				// hande "AND" and append to sql string
				if ( strstr($or, '_and_') ) {				
					$ands = explode('_and_', $or);
					$return .= '(';
					foreach ( $ands as $field ) {
						$return .= $this->_conditions_str_helper($field, $args[$args_index], ' AND ');
						$args_index++;			
					}
					$return = substr($return, 0, -4).')';
				} else {
					$return .= $this->_conditions_str_helper($or, $args[$args_index]);
					$args_index++;			
				}
				
				// and or if not last $or 
				if ( count($ors) != $or_count ) $return .= ' OR ';
				$or_count++;		
			}
			
			// clean return string
			if ( substr($return, strlen($return) -3, 3) == 'OR ' ) {
				$return = substr($return, 0, -3);
			}
			
			return str_replace('OR OR ', 'OR ', $return);
		}
		
		// handle "AND" and create/return sql string
		if ( strstr($method, '_and_') ) {
			$ands = explode('_and_', $method);
			foreach ( $ands as $field ) {
				$return .= $this->_conditions_str_helper($field, $args[$args_index], ' AND ');
				$args_index++;			
			}
			$return = substr($return, 0, -4);
		}
		
		return $return;
	}
	
	/*
	
	Function: _conditions_str_helper
		Helps creates SQL conditon string.

	Parameters:	
		field - required
		value - required
		append - optional

	Returns:
		string

	*/

	private function _conditions_str_helper($field, $value, $append = '')
	{
		$return = '';
		switch ( true )
		{
			
			case ( strstr($field, '_like') ):
				if ( !strstr($value, '%') ) $value = '%' . $value . '%';
				$return = str_replace('_like', '', $field) . " LIKE '{$value}'";
			break;
			
			case ( strstr($field, '_not') ):
				$return = str_replace('_not', '', $field) . " != '{$value}'";
			break;
			
			default:
				$return = "{$field} = '{$value}'";
			break;
			
		}
		return $return . $append;
	}
	
    /*

	Function: _create_child


	Returns:
		object

	*/

	private function _create_child()
	{
		return clone $this;
	}

	/*
	
	Section: Callback Functions

		* after_save
		* before_save
		* after_find
		* before_find
		* before_create
		* after_delete
		* before_delete
		* validate
		* validate_on_create
		* validate_on_update

	*/

	public function after_save() {}
	public function before_save() {}
	public function after_find() {}
	public function before_find() {}
	public function before_create() {}
	public function after_delete() {}
	public function before_delete() {}
	public function validate() {}
	public function validate_on_create() {}
	public function validate_on_update() {}

	// Section: Iterator Interface
	
	public function rewind()
	{
		$this->_select_query->rewind();
		$this->next();
	}

	public function current()
	{
		return $this->_create_child();
	}

	public function next()
	{

		if ( $this->get_pointer() <= ( $this->row_count() - 1 ) ) {

			$row = $this->_select_query->get_row($this->get_pointer(), $type);
			$this->load_values_into_fields($row, $type);
			$this->_select_query->pointer++;
			$this->_valid = true;
			return $row;

		} else {

			$this->_valid = false;
			$this->_select_query->pointer--;
			return false;

		}
	}

	public function valid()
	{
		return $this->_valid;
	}
	
}
?>
