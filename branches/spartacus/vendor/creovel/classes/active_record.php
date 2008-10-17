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
abstract class ActiveRecord
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
			$this->load_data($data);
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
		switch (CREO('mode')) {
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
			$db_properties['table_name'] = $this->table_name();
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
		$count = $this->select_query($sql)->total_rows();
		
		if ($count == 1) {
			
			// if one record load object and return
			$this->attributes($this->_select_query_->get_row());
			
			return $this;
			
		} elseif ($count) {
			
			// if mulitple
			$return = array();
			
			while ($row = $this->_select_query_->get_row()) {
				$class = $this->class_name();
				$model = new $class($row);
				$return[] = $model;
			}
			
			return $return;
		}
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
		return $this->query($sql);
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
				$where[] = "`{$this->_primary_key_}` IN (" .
					implode(", ", $id) . ")";
				break;
				
			case strtolower($options['_type_']) == 'all':
				break;
			
			case strtolower($options['_type_']) == 'first':
				$limit = '1';
				break;
				
			default:
				$where[] = "`{$this->_primary_key_}` = ".
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
	public function columns($columns = array())
	{
		return $this->_columns_ = count($columns) ? $columns : (count($this->_columns_) ? $this->_columns_ : $this->select_query()->columns());
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
	 * @author Nesbert Hidalgo
	 **/
	public function count_by_sql($qry)
	{
		return (int) current($this->action_query($qry)->get_row());
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function load_data($data)
	{
		if (is_array($data)) {
			$this->attributes($data);
		} else {
			$this->find('first', array(
					'conditions' => array("`{$this->_primary_key_}` = ?", $data)
				));
		}
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function attributes($data = null)
	{
		// get column properties once
		$this->columns();
		
		// set column properties
		if (is_array($data)) {
			foreach ($data as $k => $v) {
				$this->_columns_[$k]->value = $v;
			}
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
	public function id()
	{
		return $this->{$this->_primary_key_};
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function save($validation_routine = 'validate')
	{
		$this->before_save();
		
		#if (!$this->validate_model($validation_routine)) return false;
		
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
			if ($this->_primary_key_ == $k) continue;
			$set[] = "`$k` = {$this->quote_value($v)}";
		}
		
		// build query
		$qry = "UPDATE `{$this->table_name()}` " .
				"SET " . implode(', ', $set) . " " .
				"WHERE `{$this->_primary_key_}` = '{$this->id()}'";
		
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
					
				case $field->null == 'YES':
					$return[$name] = $field->value === '' || $field->value === null ? 'NULL' : '';
					break;
					
				default:
					$return[$name] = $field->value;
					break;
			}
		}
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
			
			$this->columns();
			
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
	 * @author Nesbert Hidalgo
	 **/
	public function __call($method, $args)
	{
		try {
			
			if (!method_exists($this, $method)) {
				throw new Exception("Method <em>{$method}</em> not found in <strong>{$this->class_name()}</strong> model.");
			}
			
		} catch (Exception $e) {
			// add to errors
			CREO('application_error', $e);
		}
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
} // END abstract class ActiveRecord implements Interator