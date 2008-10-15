<?php
/**
 * undocumented class
 *
 * @package default
 * @author Nesbert Hidalgo
 **/
class ActiveRecord
{
	/**
	 * Primary key column name.
	 *
	 * @var string
	 **/
	public $_database_name_ = '';
	
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
		if (is_array($data)) {
			$this->load_data($data);
		}
		
		if (is_array($connection_properties)) {
			$this->_select_query_ = $this->establish_connection($connection_properties);
			$this->_action_query_ = $this->establish_connection($connection_properties);
		}
		
		$this->set_table_name();
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
		$sq = $this->selectQuery($sql);
		
		if ($sq->totalRows() == 1) {
			$this->loadAttributes($sq->getRow());
			return clone $this;
		} elseif ($sq->totalRows()) {
		
			while ($row = $sq->getRow()) {
				print_obj($row);
			}
			
		}
		
		print_obj($this);
		
		return ;
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
	 * undocumented function
	 *
	 * @return void
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
					$conditions[] = "`{$this->tableName() }`.`{$k}` = {$this->quoteValue($v)}";
				}
				$where[] = '(' . implode(' AND ', $conditions) . ')';
				
			// array condidtions
			} elseif (is_array($options['conditions']) && in_string('?', $options['conditions'][0])) {
				$str = array_shift($options['conditions']);
				foreach ($options['conditions'] as $v) {
					$str = preg_replace('/\?/', $this->quote_value($v), $str, 1);
				}
				$where[] = "({$str})";
			
			// arraty with symbold
			} elseif (is_array($options['conditions']) && in_string(':', $options['conditions'][0])) {
				$str = $options['conditions'][0];
				foreach ($options['conditions'][1] as $k => $v) {
					$str = str_replace($k, $this->quote_value($v), $str);
				}
				$where[] = "({$str})";
				
			// string conditions UNSAFE!
			} else {
				$where[] = "({$options['conditions']})";
			}
		}
		
		$sql  = "SELECT $select FROM `{$this->tableName()}`";
		$sql .= count($where) ? " WHERE " . implode(' AND ', $where) : "";
		$sql .= $options['group'] ? " GROUP BY {$options['group']}" : "";
		$sql .= $options['order'] ? " ORDER BY {$options['order']}" : "";
		$sql .= $limit ? " LIMIT {$limit}" : "";
		return $sql .= $offset ? " OFFSET {$offset}" : "";
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
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
	 * undocumented function
	 *
	 * @return void
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
	public function table_name()
	{
		return $this->_table_name_ ? $this->_table_name_ : Inflector::tableize($this->class_name());
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function set_table_name($table_name = '')
	{
		$this->_table_name_  = $table_name ? $table_name : $this->table_name();
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function set_columns()
	{
		$this->_columns_ = $this->_columns_ ? $this->_columns_ : $this->select_query()->columns();
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function load_data($data)
	{
		if (is_array($data)) {
			$this->load_attributes($data);
		} else {
			$this->id($data);
		}
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function load_attributes($data)
	{
		// get column properties once
		if (!count($this->_columns_)) {
			$this->set_columns();
		}
		
		// set column properties
		if (is_array($data)) foreach($data as $k => $v) {
			$this->_columns_->$k->value = $v;
		}
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function attributes($data = null)
	{
		// if data passed set $data else get $data
		if ($data) {
			$this->load_attributes($data);
			return;
		}
		
		// get column propties once
		if (!count($this->_columns_)) {
			$this->set_columns();
		}
		
		$attribites = array();
		
		// get column properties
		foreach($this->_columns_ as $k => $v) {
			$attribites[$k] = $v->value;
		}
		
		return (object) $attribites;
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function id($id)
	{
		die("Search and Load by _primary_key_ = {id}.");
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
		
		//if ($key = $this->key()) {
		if (0) {
		
			// validate model on every update
			$this->validate_on_update();
			
			// if error return false
			#if ($this->errors->has_errors()) return false;
			
			$ret_val = $this->_execute_update($this->values(), $this->_primary_key_ . " = '" . $this->id() . "'");
			
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
		foreach ($this->_columns_ as $name => $field) {
			switch (true) {
				case $name == 'created_at':
				case $name == 'updated_at':
					$field->value = datetime($field->value);
					break;
				
				case is_array($field->value):
					if ($field->type == 'datetime') {
						$field->value = datetime($field->value);
					} else {
						$field->value = serialize($field->value);
					}
					break;
					
				case $field->null == 'YES':
					$field->value = $field->value === '' || $field->value === null ? 'NULL' : '';
					break;
			}
		}
		
		// sanitize values
		$values = array();
		foreach ($this->attributes() as $val) {
			$values[] = $this->quote_value($val);
		}
		
		// build query
		$qry = "INSERT INTO `{$this->tableName()}` ";
		$qry .= "(`" . implode('`, `', array_keys((array) $this->attributes())) . "`)";
		$qry .= " VALUES ";
		$qry .= "(" . implode(', ', $values) . ");";
		
		return $this->id = $this->action_query($qry)->insert_id();
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
			
			if (isset($this->_columns_->$attribute)) {
				return $this->_columns_->$attribute->value;
			}  else {
				throw new Exception("Attribute <em>{$attribute}</em> not found in <strong>{$this->class_name()}</strong> model.");
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
			
			// get column properties once
			if (!count($this->_columns_)) {
				$this->set_columns();
			}
			
			if (isset($this->_columns_->$attribute)) {
				return $this->_columns_->$attribute->value = $value;
			} else {
				throw new Exception("Attribute <em>{$attribute}</em> not found in <strong>{$this->class_name()}</strong> model.");
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