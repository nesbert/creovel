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
	 * undocumented function
	 *
	 * @return void
	 **/
	public function __construct($data = null, $connection_properties = null)
	{
		if (is_array($data)) {
			$this->loadData($data);
		}
		
		if (is_array($connection_properties)) {
			$this->_select_query = $this->establishConnection($connection_properties);
			$this->_action_query = $this->establishConnection($connection_properties);
		}
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function connectionProperties()
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
	public function establishConnection($db_properties = null)
	{
		if (!$db_properties || !is_array($db_properties)) {
			$db_properties = self::connectionProperties();
		}
		
		if (@!$db_properties['table_name']) {
			$db_properties['table_name'] = $this->getTableName();
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
				self::throwError("Unknown database adapter '{$adapter}'. Please check database configuration file.");
				break;
		}
		
		return new $adapter($db_properties);
	}
	
	/**
	 * Stop the application and display/handle error.
	 *
	 * @return void
	 **/
	public function throwError($msg = null)
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
		$sq = &$this->selectQuery($sql);
		
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
	public function findBySql($sql)
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
		$sql = $this->buildQueryFromOptions(array('_type_' => $type) + (array) $options);
		return $this->query($sql);
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function buildQueryFromOptions($options = array())
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
					$id[] = $this->quoteValue($v);
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
					$this->quoteValue($options['_type_']);
				break;
		}
		if (@$options['conditions']) {
			$where[] = "({$options['conditions']})";
		}
		
		$sql  = "SELECT $select FROM `{$this->getTableName()}`";
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
	public function selectQuery($query = '', $connection_properties = array())
	{
		if (!isset($this->_select_query_)) {
			$this->_select_query_ = $this->establishConnection($connection_properties);
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
	public function actionQuery($query = '', $connection_properties = array())
	{
		if (!isset($this->_action_query_)) {
			$this->_action_query_ = $this->establishConnection($connection_properties);
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
	public function quoteValue($string)
	{
		return "'" . $this->selectQuery()->escape($string) . "'";
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function getClassName()
	{
		return (string) get_class($this);
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function getTableName()
	{
		return Inflector::tableize($this->getClassName());
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function loadData($data)
	{
		if (is_array($data)) {
			$this->loadAttributes($data);
		} else {
			$this->id($data);
		}
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function loadAttributes($data)
	{
		// get column propties once
		if (!count($this->_columns_)) {
			$this->_columns_ = $this->selectQuery()->columns();
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
			$this->loadAttributes($data);
			return;
		}
		
		// get column propties once
		if (!count($this->_columns_)) {
			$this->_columns_ = $this->selectQuery()->columns();
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
				throw new Exception("Attribute <em>{$attribute}</em> not found in <strong>{$this->getClassName()}</strong> model.");
			}
			
		} catch ( Exception $e ) {
			CREO('application_error', $e);
		}
	}
} // END abstract class ActiveRecord implements Interator