<?php
/**
 * ORM class for interfacing with relational databases and adding functionality
 * to each modeled table.
 *
 * @package Creovel
 * @subpackage Creovel.Classes
 * @copyright  2008 Creovel, creovel.org
 * @license    http://creovel.googlecode.com/svn/trunk/License   MIT License
 * @version    $Id:$
 * @since      Class available since Release 0.1.0
 **/
abstract class ActiveRecord implements Iterator
{
	/**
	 * Primary key column name.
	 *
	 * @var string
	 **/
	public $_table_name_ = '';
	
	/**
	 * Primary key column name.
	 *
	 * @var string
	 **/
	public $_primary_key_ = 'id';
	
	/**
	 * Primary key column name.
	 *
	 * @var string
	 **/
	public $_columns_ = array();
	
	/**
	 * Primary key column name.
	 *
	 * @var string
	 **/
	public $_select_query_;
	
	/**
	 * Primary key column name.
	 *
	 * @var string
	 **/
	public $_action_query_;
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function __construct($data = null, $connection_properties = null)
	{
		// load data if passed
		if ($data) {
			$this->attributes($data);
		}
		
		// load connection if passed
		if (is_array($connection_properties)) {
			$this->_select_query_ = $this->establish_connection($connection_properties);
			$this->_action_query_ = $this->establish_connection($connection_properties);
		}
		
		// set table name
		$this->table_name();
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function connection_properties()
	{
		switch (strtoupper(CREO('mode'))) {
			case 'PRODUCTION':
				return $GLOBALS['CREOVEL']['DATABASES']['PRODUCTION'];
				break;
			
			case 'TEST':
				return $GLOBALS['CREOVEL']['DATABASES']['TEST'];
				break;
			
			case 'DEVELOPMENT':
			default:
				return $GLOBALS['CREOVEL']['DATABASES']['DEVELOPMENT'];
				break;
		}
	}
	
	/**
	 * Choose the correct DB adapter to use and sets its properties and
	 * return an db object.
	 *
	 * @param array $db_properties
	 * @return object
	 **/
	public function establish_connection($db_properties = null)
	{
		if (!$db_properties || !is_array($db_properties)) {
			$db_properties = self::connection_properties();
		}
		
		if (@!$db_properties['table_name']) {
			#$db_properties['table_name'] = $this->table_name();
		}
		
		$adapter = isset($db_properties['adapter']) ? strtolower($db_properties['adapter']) : 'None';
		$adapter_path = dirname(dirname(__FILE__)) . DS . 'adapters' . DS;
		
		switch ($adapter) {
			case 'mysql':
				$adapter = Inflector::classify('mysql');
				break;
			
			case 'mysql_improved':
				require_once $adapter_path . 'mysql_improved.php';
				$adapter = Inflector::classify('mysql_improved');
				break;
			
			case 'sqlite':
				$adapter = Inflector::classify('sqlite');
				break;
			
			default:
				self::throw_error("Unknown database adapter '{$adapter}'. Please check database configuration file.");
				break;
		}
		
		return new $adapter($db_properties);
	}
	
	/**
	 * Stop the application and display/handle error.
	 *
	 * @return void
	 **/
	public function throw_error($msg = null)
	{
		if (!$msg) {
			$msg = 'An error occurred while executing the method ' .
			"<em>{$this->_action}</em> in the <strong> " . get_class($this) .
			'</strong>.';
		}
		CREO('application_error', $msg);
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function query($sql)
	{
		$this->select_query($sql);
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function find_by_sql($sql)
	{
		return $this->query($sql);
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function find($type, $options = array())
	{
		$sql = $this->build_query_from_options(array('_type_' => $type) + (array) $options);
		$this->query($sql);
		if ($type == 'first') {
			$this->current();
		}
		return clone $this;
	}
	
	/**
	 * Alias for find('all', array());
	 *
	 * @return void
	 **/
	public function all($options = array())
	{
		return $this->find('all', $options);
	}
	
	/**
	 * undocumented function
	 *
	 * @param array
	 * @return string
	 **/
	public function build_query_from_options($options = array())
	{
		$select = '*';
		$where = array();
		$limit = '';
		$regex = '/^[A-Za-z0-9_,\s\-\(\)]+$/';
		
		// set defaults and validate options
		if (@$options['select']) {
			$select = $options['select'];
		}
		if (@!preg_match($regex, $options['order'])){
			$options['order'] = '';
		}
		if (@!is_numeric($options['offset'])) {
			$options['offset'] = '';
		}
		if (@!preg_match($regex, $options['limit'])) {
			$options['limit'] = '';
		}
		if (@!preg_match($regex, $options['group'])) {
			$options['group'] = '';
		}
		if (@$options['offset']) {
			$offset = $options['offset'];
		} else {
			$offset = '';
		}
		if (@$options['limit']) {
			$limit = $options['limit'];
		} else {
			$limit = '';
		}
		
		// set where
		switch (true) {
			case is_array($options['_type_']):
				$id = array();
				foreach ($options['_type_'] as $v) {
					$id[] = $this->quote_value($v);
				}
				$where[] = "`{$this->primary_key()}` IN (" .
					implode(", ", $id) . ")";
				break;
				
			case strtolower($options['_type_']) == 'all':
				break;
			
			case strtolower($options['_type_']) == 'first':
				$limit = '1';
				break;
				
			default:
				$where[] = "`{$this->primary_key()}` = ".
					$this->quote_value($options['_type_']);
				break;
		}
		if (@$options['conditions']) {
			// hash condidtions
			if (is_assoc($options['conditions'])) {
				$conditions = array();
				foreach ($options['conditions'] as $k => $v) {
					$conditions[] = "`{$this->table_name()}`.`{$k}` = {$this->quote_value($v)}";
				}
				$where[] = '(' . implode(' AND ', $conditions) . ')';
				
			// array condidtions
			} elseif (is_array($options['conditions']) && in_string('?', $options['conditions'][0])) {
				$str = array_shift($options['conditions']);
				foreach ($options['conditions'] as $v) {
					$str = preg_replace('/\?/', $this->quote_value($v), $str, 1);
				}
				$where[] = "({$str})";
			
			// array with symbols
			} elseif (is_array($options['conditions']) && in_string(':', $options['conditions'][0])) {
				$str = $options['conditions'][0];
				foreach ($options['conditions'][1] as $k => $v) {
					$str = str_replace(':' . $k, $this->quote_value($v), $str);
				}
				$where[] = "({$str})";
				
			// string conditions UNSAFE!
			} else {
				$where[] = "({$options['conditions']})";
			}
		}
		
		$sql  = "SELECT $select FROM `{$this->table_name()}`";
		$sql .= count($where) ? " WHERE " . implode(' AND ', $where) : "";
		$sql .= $options['group'] ? " GROUP BY {$options['group']}" : "";
		$sql .= $options['order'] ? " ORDER BY {$options['order']}" : "";
		$sql .= $limit ? " LIMIT {$limit}" : "";
		return $sql .= $offset ? " OFFSET {$offset}" : "";
	}
	
	/**
	 * Create select query object for SELECTS.
	 *
	 * @return object
	 **/
	public function select_query($query = '', $connection_properties = array())
	{
		if (!is_object($this->_select_query_)) {
			$this->_select_query_ = $this->establish_connection($connection_properties);
		}
		
		if ($query) {
			$this->_select_query_->query($query);
		}
		
		return $this->_select_query_;
	}
	
	/**
	 * Create action query object for INSERTS, UPDATES, DELETES, Counts, etc.
	 *
	 * @return object
	 **/
	public function action_query($query = '', $connection_properties = array())
	{
		if (!is_object($this->_action_query_)) {
			$this->_action_query_ = $this->establish_connection($connection_properties);
		}
		
		if ($query) {
			$this->_action_query_->query($query);
		}
		
		return $this->_action_query_;
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function quote_value($string)
	{
		return "'" . $this->select_query()->escape($string) . "'";
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function class_name()
	{
		return (string) get_class($this);
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function table_name($table_name = '')
	{
		return $this->_table_name_ = $table_name ? $table_name : ($this->_table_name_ ? $this->_table_name_ : Inflector::tableize($this->class_name()));
	}
	
	/**
	 * Returns an array of column objects for the table associated with class.
	 *
	 * @return void
	 **/
	public function columns($columns = array())
	{
		return $this->_columns_ = count($columns) ? $columns : (count($this->_columns_) ? $this->_columns_ : $this->table_columns());
	}
	
	/**
	 * Describe column details with DB object.
	 *
	 * @return void
	 **/
	public function table_columns()
	{
		return $this->_columns_ = $this->select_query()->columns($this->table_name());
	}
	
	/**
	 * Returns an array of column names as strings.
	 *
	 * @return array
	 **/
	public function column_names()
	{
		return array_keys($this->columns());
	}
	
	/**
	 * Returns an array of column objects for the table associated with class.
	 *
	 * @return void
	 **/
	public function columns_hash()
	{
		return (object) $this->columns();
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function count_by_sql($qry)
	{
		return (int) current($this->action_query($qry)->get_row());
	}
	
	/**
	 * Checks if $attribute is a valid table column.
	 *
	 * @return void
	 **/
	public function attribute_exists($attribute)
	{
		return isset($this->_columns_[$attribute]);
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function attributes($data = null)
	{
		// set column properties
		if (is_array($data)) {
			foreach ($data as $k => $v) {
				$this->_columns_[$k]->value = $v;
			}
		} else if ($data) {
			$this->find('first', array(
					'conditions' => array("`{$this->primary_key()}` = ?", $data)
				));
			$this->attributes($this->select_query()->get_row());
		} else {
			$attribites = array();
			
			// get column properties
			foreach($this->_columns_ as $k => $v) {
				$attribites[$k] = $v->value;
			}
			
			return $attribites;
		}
	}
	
	/**
	 * Returns true if the passed attribute is a column of the class.
	 *
	 * @return void
	 **/
	public function has_attribute($attribute)
	{
		return array_key_exists($attribute, $this->columns());
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function primary_key()
	{
		return $this->primary_key();
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function id()
	{
		return $this->{$this->primary_key()};
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function save($validation_routine = 'validate')
	{
		$this->before_save();
		
		$this->validate();
		
		// if error return false
		if ($this->has_errors()) return false;
		
		if ($this->id()) {
		
			// validate model on every update
			$this->validate_on_update();
			
			// if error return false
			#if ($this->errors->has_errors()) return false;
			
			$ret_val = $this->update_row();
			
		} else {
		
			// validate model on every insert
			$this->validate_on_create();
			
			// if error return false
			#if ($this->errors->has_errors()) return false;
			
			$this->before_create();
			
			$ret_val = $this->insert_row();
		}
		
		#foreach ($this->child_objects as $obj) $obj->save();
		
		if ($ret_val) {
			$this->after_save();
			return $ret_val;
		} else {
			return false;
		}
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function insert_row()
	{
		// sanitize values
		$values = array();
		foreach ($this->prepare_attributes() as $k => $v) {
			$values[$k] = $this->quote_value($v);
		}
		
		// build query
		$qry =	"INSERT INTO `{$this->table_name()}` " .
				"(`" . implode('`, `', array_keys($values)) . "`) " .
				"VALUES " .
				"(" . implode(', ', $values) . ");";
		
		return $this->id = $this->action_query($qry)->insert_id();
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function update_row()
	{
		// sanitize values and prep set string
		$set = array();
		foreach ($this->prepare_attributes() as $k => $v) {
			if ($this->primary_key() == $k) continue;
			$set[] = "`$k` = {$this->quote_value($v)}";
		}
		
		// build query
		$qry = "UPDATE `{$this->table_name()}` " .
				"SET " . implode(', ', $set) . " " .
				"WHERE `{$this->primary_key()}` = '{$this->id()}'";
		
		$this->action_query($qry);
		
		return $this->id();
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function prepare_attributes()
	{
		$return = array();
		foreach ($this->_columns_ as $name => $field) {
			switch (true) {
				case $name == 'created_at' && !$this->id():
					$return[$name] = datetime($field->value);
					break;
				
				case $name == 'updated_at':
					$return[$name] = datetime();
					break;
				
				case is_array($field->value):
					if ($field->type == 'datetime') {
						$return[$name] = datetime($field->value);
					} else {
						$return[$name] = serialize($field->value);
					}
					break;
					
				case isset($field->null) && $field->null == 'YES':
					$return[$name] = $field->value === '' || $field->value === null ? 'NULL' : '';
					break;
					
				default:
					$return[$name] = $field->value;
					break;
			}
		}
		
		// update current values
		$this->attributes($return);
		
		return $return;
	}
	
	/**
	 * Alias to find and sets the $_paging_ object. Default page limit is
	 * 10 records.
	 *
	 * @return void
	 **/
	public function paginate($args = null)
	{
		// create temp args
		$temp = $args;
		unset($temp['offset']);
		unset($temp['order']);
		$temp['_type_'] = "all";
		$temp['select'] = "count(*)";
		$total = $this->count_by_sql($this->build_query_from_options($temp));
		
		// create page object
		$this->_paging_ = new ActivePager($temp);
		
		// update agrs with paging data
		$args['offset'] = $this->_paging_->offset;
		$args['limit'] = $this->_paging_->limit;
		
		// execute query
		return $this->find('all', $args);
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Nesbert Hidalgo
	 **/
	public function table_object($table_name, $db = null)
	{
		if (@!$db) {
			$db = self::connection_properties();
		}
		$db['table_name'] = $table_name;
		return self::establish_connection($db)->db;
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function total_rows()
	{
		return $this->select_query()->total_rows();
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function get_row()
	{
		return $this->select_query()->get_row();
	}
	
	// Section: Magic Functions
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function __get($attribute)
	{
		try {
			
			$this->columns();
			
			switch (true) {
				case isset($this->_columns_[$attribute]):
					return $this->_columns_[$attribute]->value;
					break;
					
				default:
					throw new Exception("Attribute <em>{$attribute}</em> not found in <strong>{$this->class_name()}</strong> model.");
					break;
			}
			
		} catch (Exception $e) {
			CREO('application_error', $e);
		}
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function __set($attribute, $value)
	{
		try {
			// get table columns and set
			$vals = $this->attributes();
			$this->table_columns();
			$this->attributes($vals);
			
			switch (true) {
				case isset($this->_columns_[$attribute]):
					return $this->_columns_[$attribute]->value = $value;
					break;
					
				case $attribute == '_paging_':
					$this->$attribute = $value;
					break;
					
				default:
					throw new Exception("Attribute <em>{$attribute}</em> not found in <strong>{$this->class_name()}</strong> model.");
					break;
			}
			
		} catch (Exception $e) {
			// add to errors
			CREO('application_error', $e);
		}
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function __call($method, $arguments)
	{
		try {
			// get property name
			$name = str_replace(array(
							'text_field_for_',
							'select_for_',
							'text_area_for_',
							'textarea_for_',
							'radio_button_for_',
							'check_box_for_',
							'checkbox_for_',
							'select_countries_tag_for_',
							'select_countries_tag_for_',
							'hidden_field_for_',
							'password_field_for_'
							), '', $method);
			$arguments[0] = isset($arguments[0]) ? $arguments[0] : '';
							
			switch (true) {
				case in_string('field_for_', $method):
				case in_string('select_for_', $method):
				case in_string('text_area_for_', $method):
				case in_string('textarea_for_', $method):
				case in_string('check_box_for_', $method):
				case in_string('checkbox_for_', $method):
				case in_string('radio_button_for_', $method):
				case in_string('select_countries_tag_for_', $method):
				case in_string('select_states_tag_for_', $method):
					$type = str_replace(array('_field_for_' . $name, '_for_' . $name), '', $method);
					return $this->html_field($type, $name, $this->$name, $arguments);
					break;
				
				case in_string('options_for_', $method):
					if ($this->attribute_exists($name)) {
						$this->_columns_->$name->options = $arguments[0];
					} else {
						throw new Exception("Can set options for {$name}. Property <em>{$name}</em> not found in <strong>{$this->class_name()}</strong> model.");
					}
					break;
					
				case in_string('_has_error', $method):
					return $this->has_error($name, $arguments[0]);
					break;
					
				case in_string('validates_', $method):
					return $this->validate_by_method($method, $arguments);
					break;
			}
			
			if (!method_exists($this, $method)) {
				throw new Exception("Method <em>{$method}</em> not found in <strong>{$this->class_name()}</strong> model.");
			}
			
		} catch (Exception $e) {
			// add to errors
			CREO('application_error', $e);
		}
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Nesbert Hidalgo
	 **/
	private function validate_by_method($method, $arguments = null)
	{
		try {
			// set up array to handle multi fields
			if (is_array($arguments[0])) {
				$fields = $arguments[0];
			} else {
				$fields[] = $arguments[0];
			}
			
			// set params order for validation
			$params = array('field', 'value');
			if (isset($arguments[1])) $params[] = $arguments[1];
			unset($arguments[0]);	// remove field name
			unset($arguments[1]);	// remove field value
			
			// append optional args to params
			$params = array_merge($params, $arguments);
			
			foreach ($fields as $field) {
				// set field, value, message and extras
				$params[0] = $field;
				$params[1] = $this->$field;
				$params[2] = isset($params[2]) ? $params[2] : '';
				
				switch ($method) {
					case 'validates_uniqueness_of':
						ActiveValidation::validates_presence_of($params[0], $params[1], $params[2]);
						if (!$params[1]) return;
						
						if (isset($params[3])) {
							$where_ext = $params[3];
						} else {
							$where_ext = '1';
						}
						// check if a column with that value exists in the current table and is not the currentlly loaded row
						$this->action_query("SELECT * FROM `{$this->table_name()}` WHERE `{$params[0]}` = '{$params[1]}' AND `{$this->primary_key()}` != '{$this->id}' AND (".$where_ext.")");
						
						// if record found add error
						if ($this->action_query()->total_rows()) {
							ActiveValidation::add_error($params[0], ( $params[2] ? $params[2] : humanize($params[0]).' is not unique.' ));
						} else {
							return true;
						}
						break;
					
					default:
						if (method_exists('ActiveValidation', $method) ) {
							if (count($fields) > 1) {
								call_user_func_array(array('ActiveValidation', $method), $params);
							} else {
								return call_user_func_array(array('ActiveValidation', $method), $params);
							}
						} else {
							throw new Exception("Undefined validation method <em>{$method}</em> in <strong>{$this->class_name()}</strong> model.");
						}
						break;
				}
			
			}
		
		} catch (Exception $e) {
			// add to errors
			CREO('application_error', $e);
		}
	}
	
	/**
	 * Create HTML for field.
	 *
	 * @param string $type
	 * @param string $name
	 * @param mixed $value
	 * @param array $arguments
	 * @return string
	 **/
	public function html_field($type, $name, $value, $arguments = array())
	{
		// set form vars
		$arguments[0] = isset($arguments[0]) ? $arguments[0] : null;
		@$html_options = $arguments[0];
		
		// get HTML
		switch ($type) {
			case 'text':
				$html = text_field($name, $value, $html_options);
				break;
			
			case 'password':
				$html = password_field($name, $value, $html_options);
				break;
			
			case 'hidden':
				$html = hidden_field($name, $value, $html_options);
				break;
			
			case 'check_box':
			case 'checkbox':
				$tag_value = $arguments[0];
				$text = $arguments[1];
				$html_options = $arguments[2];
				$html = check_box($name, $value, $html_options, $tag_value, $text);
				break;
			
			case 'radio_button':
				$tag_value = $arguments[0];
				$text = $arguments[1];
				$html_options = $arguments[2];
				$html = radio_button($name, $value, $html_options, $tag_value, $text);
				break;
			
			case 'text_area':
			case 'textarea':
				$html = textarea($name, $value, $html_options);
				break;
			
			case 'select':
			case 'select_countries_tag':
			case 'select_states_tag':
				$options = $arguments[0] ? $arguments[0] : $this->enum_options($name);
				$html_options = isset($arguments[1]) ? $arguments[1] : null;
				$arguments[2] = isset($arguments[2]) ? $arguments[2] : null;
			
			case 'select':
				$html = select($name, $value, $options, $html_options);
				break;
			
			case 'select_countries_tag':
				$html = select_countries_tag($name, $value, $options, $html_options, $arguments[2]);
				break;
			
			case 'select_states_tag':
				$html = select_states_tag($name, $value, $options, $html_options, $arguments[2]);
				break;
		}
		
		return $html;
	}
	
	/**
	 * Check if field is validation errors array.
	 *
	 * @param string $property
	 * @param string $return_text_on_true
	 * @return boolean
	 **/
	public function has_error($property, $return_text_on_true = '')
	{
		if (isset($GLOBALS['CREOVEL']['VALIDATION_ERRORS'][$property])) {
			return $return_text_on_true ? $return_text_on_true : true;
		} else {
			return false;
		}
	}
	
	/**
	 * Check if this any validation errors.
	 *
	 * @param string $property
	 * @return boolean
	 **/
	public function has_errors()
	{
		return @count($GLOBALS['CREOVEL']['VALIDATION_ERRORS']);
	}
	
	/**
	 * Get options for ENUM field types.
	 *
	 * @return void
	 **/
	public function enum_options($property)
	{
		if (in_string('enum(', $this->_columns_[$property]->type)) {
			$options = explode("','", str_replace(array("enum('"), '', substr($this->_columns_[$property]->type, 0, -2)));
			$return = array();
			foreach ($options as $value) {
				$return[$value] = humanize($value);
			}
			return $return;
		}
	}
	
	/**
	 * Iterator methods.
	 */
	
	/**
	 * Resets DB properties and frees result resources.
	 *
	 * @return void
	 **/
	public function reset()
	{
		$this->select_query()->reset();
	}
	
	/**
	 * Set the result object pointer to its first element.
	 *
	 * @return void
	 **/
	public function rewind()
	{
		#echo $this->select_query()->key() . '-> rewind<br/>';
		return $this->select_query()->rewind();
	}
	
	/**
	 * Return the current row in result object as clone Model Object.
	 *
	 * @return object
	 **/
	public function current()
	{
		#echo $this->select_query()->key() . '-> current<br/>';
		$this->attributes($this->select_query()->current());
		return clone $this;
	}
	
	/**
	 * Returns the index element of the current result object pointer.
	 *
	 * @return integer
	 **/
	public function key()
	{
		#echo $this->select_query()->key() . '-> key<br/>';
		return $this->select_query()->key();
	}
	
	/**
	 * Advance the result object pointer.
	 *
	 * @return object
	 **/
	public function next()
	{
		#echo $this->select_query()->key() . '-> next<br/>';
		$this->select_query()->next();
		return $this->current();
	}
	
	/**
	 * Rewind the result object pointer by one.
	 *
	 * @return object
	 **/
	public function prev()
	{
		#echo $this->select_query()->key() . '-> prev<br/>';
		$this->select_query()->prev();
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
		#echo $this->select_query()->key() . '-> valid<br/>';
		return $this->select_query()->valid();
	}
	
	/**
	 * Callback Functions
	 **/
	
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
} // END abstract class ActiveRecord implements Interator