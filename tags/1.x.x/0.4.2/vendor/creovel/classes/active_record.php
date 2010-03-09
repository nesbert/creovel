<?php
/**
 * ORM class for interfacing with relational databases and adding functionality
 * to each modeled table.
 *
 * @package     Creovel
 * @subpackage  Core
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.1.0
 * @author      Nesbert Hidalgo
 **/
abstract class ActiveRecord extends Object implements Iterator
{
    /**
     * Table name.
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
     * Array of object columns.
     *
     * @var array
     **/
    public $_columns_ = array();
    
    /**
     * Database object used for SELECTS.
     *
     * @var object
     **/
    public $_select_query_;
    
    /**
     * Database object used for INSERTS, UPDATES and DELETES.
     *
     * @var object
     **/
    public $_action_query_;
    
    /**
     * Load $data and initialize model.
     *
     * @param mixed $data ID, array of IDs or array of column name/value pair.
     * @param array $connection_properties
     * @return void
     **/
    public function __construct($data = null, $connection_properties = null)
    {
        // model table and set columns
        $this->columns();
        
        // call parent
        $this->initialize_parents();
        
        // load data if passed
        if ($data) {
            $this->load($data);
        }
        
        // initialize class vars if no record loaded
        if (!$this->id()) {
            $this->initialize_class_vars();
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
     * Load an array of parameters into object or an ID.
     *
     * @return void
     **/
    final public function load($data)
    {
        if (is_hash($data)) {
            if (isset($data[$this->primary_key()])) {
                $this->set_id($data[$this->primary_key()]);
            }
            $this->attributes($data);
        } else if ($data) {
            $this->set_id($data);
        }
    }
    
    /**
     * Find $id result and load into object.
     *
     * @return void
     **/
    final public function set_id($id)
    {
        $this->find('first', array(
                'conditions' => array("`{$this->primary_key()}` = ?", $id)
            ));
    }
    
    /**
     * Execute certain processes depending special properties being set. Used
     * to set "options_for_" and more to come.
     *
     * @return void
     **/
    final public function initialize_class_vars()
    {
        $vars = get_class_vars($this);
        
        if (count($vars)) foreach ($vars as $var => $vals) {
            switch (true) {
                case in_string('options_for_', $var):
                    $this->{$var}($vals);
                    break;
                    
                case 'belongs_to' == $var:
                case 'has_many' == $var:
                case 'has_one' == $var:
                    if (is_array($vals)) foreach ($vals as $val) {
                        if (is_array($val)) {
                            $this->{$var}($val['name'], $val);
                        } else {
                            $this->{$var}($val);
                        }
                    } else {
                        $this->{$var}($vals);
                    }
                    break;
            }
        }
    }
    
    /**
     * Get the current database connection settings based on Creovel mode.
     *
     * @return array
     **/
    final public function connection_properties()
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
     * return a database object.
     *
     * @param array $db_properties
     * @return object
     **/
    final public function establish_connection($db_properties = null)
    {
        try {
            
            if (!$db_properties || !is_array($db_properties)) {
                $db_properties = self::connection_properties();
            }
            
            $adapter_path = CREOVEL_PATH . 'adapters' . DS;
            
            // set adapter and include
            $adapter = strtolower($db_properties['adapter']);
            
            if (file_exists($adapter_path . $adapter .'.php')) {
                require_once $adapter_path . $adapter .'.php';
                $adapter = Inflector::classify($adapter);
                return new $adapter($db_properties);
            } else {
                throw new Exception("Unknown database adapter '{$adapter}'. " .
                    "Please check database configuration file.");
            }
            
        } catch (Exception $e) {
            CREO('error_code', 500);
            CREO('application_error', $e);
        }
    }
    
    /**
     * Stop the application and display/handle error.
     *
     * @return void
     **/
    public function throw_error($msg = null)
    {
        if (!$msg) {
            $msg = "An error occurred while executing a method " .
                    "in the <strong>{$this->class_name()}</strong>.";
        }
        CREO('error_code', 500);
        CREO('application_error', $msg);
    }
    
    /**
     * Loads/execute SQL query using the select_query object.
     *
     * @param string $sql
     * @param boolean $action_query Set to true to use action query
     * @return object
     **/
    final public function query($sql, $action_query = false)
    {
        if ($action_query) {
            return $this->action_query($sql);
        } else {
            return $this->select_query($sql);
        }
    }
    
    /**
     * Loads/execute SQL query. When $type is set to 'first' will
     * auto-load first record if found.
     *
     * @param string $sql
     * @param boolean $type 'all', 'first'
     * @return void
     **/
    final public function find_by_sql($sql, $type = 'all')
    {
        return $this->find($type, array('sql' => $sql));
    }
    
    /**
     * Execute a query by $type and $options. You can use "where" instead of
     * "conditions" as well.
     *
     * <code>
     * $sample_options = array(
     *   'select'       => "`column`, `column2`",
     *   'from'         => "`table`, `table2`",
     *   'join'         => "JOIN STATEMENT",
     *   'conditions'   => "WHERE CLAUSE OR ARRAY OF OPTIONS",
     *   'group'        => "`column`",
     *   'order'        => "`column` DESC, `column2` ASC",
     *   'limit'        => "100",
     *   'offset'       => "30"
     *    );
     *
     * $model->find();
     * SELECT * FROM `users`;
     *
     * $model->find('all', array('conditions' => array('state' => 'NV', 'zip' => 89107)));
     * SELECT * FROM `users` WHERE (`users`.`state` = 'NV' AND `users`.`zip` = '89107');
     *
     * $model->find('all', array('conditions' => array(
     *     '(state = ? OR other_state = ?) AND zip = ?',
     *     'NV', 'NV', 89107
     *     )));
     * SELECT * FROM `users` WHERE ((state = 'NV' OR other_state = 'NV') AND zip = '89107');
     *
     * $model->find('all', array('conditions' => array(
     *   '(state = :state OR other_state = :state) AND zip = :zip',
     *   array(
     *       'state' => 'NV',
     *       'zip' => 89107
     *       )
     *   )));
     * SELECT * FROM `users` WHERE ((state = 'NV' OR other_state = 'NV') AND zip = '89107');
     * </code>
     *
     * @param mixed $type 'all', 'first', ID or array of IDs
     * @param array $options
     * @return object
     **/
    final public function find($type = 'all', $options = array())
    {
        // before find call-back
        $this->before_find();
        
        if (isset($options['sql'])) {
            $sql = $options['sql'];
        } else {
            $sql = $this->build_query($options, $type);
        }
        $this->query($sql);
        
        if ($type == 'first') {
            $this->current();
        }
        
        // after find call-back
        $this->after_find();
        
        return clone $this;
    }
    
    /**
     * Alias for find('all', array());
     *
     * @param array $options
     * @see ActiveRecord::find()
     * @return object
     **/
    final public function all($options = array())
    {
        return $this->find('all', $options);
    }
    
    /**
     * Build query string from an $options array.
     *
     * @param array $options
     * @param mixed &$type 'all', 'first', ID or array of IDs
     * @return string
     **/
    final public function build_query($options, &$type)
    {
        $where = array();
        $limit = '';
        $regex = '/^[A-Za-z0-9_,.`\s\-\(\)]+$/';
        
        // set vaiables used to build query
        if (isset($options['select'])) {
            $select = $options['select'];
        } else {
            $select = '*';
        }
        
        if (isset($options['from'])) {
            $from = $options['from'];
        } else {
            $from = "`{$this->table_name()}`";
        }
        
        if (isset($options['where'])) {
            $options['conditions'] = $options['where'];
        } else if (isset($options['conditions'])) {
            $options['conditions'] = $options['conditions'];
        } else {
            $options['conditions'] = '';
        }
        
        if (isset($options['join'])) {
            $join = $options['join'];
        } else {
            $join = '';
        }
        
        if (isset($options['order']) &&
                preg_match($regex, $options['order'])) {
            $order = $options['order'];
        } else {
            $order = '';
        }
        
        if (isset($options['offset']) && is_numeric($options['offset'])) {
            $offset = $options['offset'];
        } else {
            $offset = '';
        }
        
        if (isset($options['limit']) && is_numeric($options['limit'])) {
            $limit = $options['limit'];
        } else {
            $limit = '';
        }
        
        if (isset($options['group']) && preg_match($regex, $options['group'])) {
            $group = $options['group'];
        } else {
            $group = '';
        }
        
        // set where
        switch (true) {
            case is_array($type):
                $id = array();
                foreach ($type as $v) {
                    $id[] = $this->quote_value($v);
                }
                $where[] = "`{$this->primary_key()}` IN (" .
                    implode(", ", $id) . ")";
                break;
                
            case strtolower($type) == 'all':
                break;
            
            case strtolower($type) == 'first':
                $limit = '1';
                break;
                
            default:
                $where[] = "`{$this->primary_key()}` = ".
                $this->quote_value($type);
                // update to auto first record
                $type = 'first';
                break;
        }
        
        // Prepare conditions array.
        if ($options['conditions']) {
            $where[] = $this->build_query_from_conditions($options['conditions']);
        }
        
        // create sql query
        $sql = "SELECT {$select} FROM {$from}";
        if ($join) $sql .= " " . $this->build_query_from_conditions($join, false);
        if (count($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        if ($group) $sql .= " GROUP BY {$group}";
        if ($order) $sql .= " ORDER BY {$order}";
        if ($limit) $sql .= " LIMIT {$limit}";
        if ($offset) $sql .= " OFFSET {$offset}";
        
        return $sql . ';';
    }
    
    /**
     * Builds where SQL string by the conditions array passed.
     *
     * @param mixed $conditions
     * @return void
     **/
    final public function build_query_from_conditions($conditions, $isolate = true)
    {
        $sql = '';
        
        // 1. hash condidtions
        if (is_hash($conditions)) {
            $cs = array();
            foreach ($conditions as $k => $v) {
                $cs[] = "`{$this->table_name()}`.`{$k}` = {$this->quote_value($v)}";
            }
            if ($isolate) {
                $sql = '(' . implode(' AND ', $cs) . ')';
            } else {
                $sql = implode(' AND ', $cs);
            }
            
        // 2. array condidtions
        } elseif (is_array($conditions) && in_string('?', $conditions[0])) {
            $str = array_shift($conditions);
            foreach ($conditions as $v) {
                $str = preg_replace('/\?/', $this->quote_value($v), $str, 1);
            }
            if ($isolate) {
                $sql = "({$str})";
            } else {
                $sql = "{$str}";
            }
        
        // 3. array with symbols
        } elseif (is_array($conditions) && in_string(':', $conditions[0])) {
            $str = $conditions[0];
            foreach ($conditions[1] as $k => $v) {
                $str = str_replace(':' . $k, $this->quote_value($v), $str);
            }
            if ($isolate) {
                $sql = "({$str})";
            } else {
                $sql = "{$str}";
            }
            
        // 4. string conditions UNSAFE!
        } elseif ($conditions) {
            if ($isolate) {
                $sql = "({$conditions})";
            } else {
                $sql = "{$conditions}";
            }
        }
        
        return $sql;
    }
    
    /**
     * Create select query object for SELECTS.
     *
     * @return object
     **/
    final public function select_query($query = '', $connection_properties = array())
    {
        if (!is_object($this->_select_query_)) {
            $this->_select_query_ =
                $this->establish_connection($connection_properties);
        }
        
        if ($query) {
            $this->_select_query_->query($query);
        }
        
        return $this->_select_query_;
    }
    
    /**
     * Create action query object for INSERT, UPDATE, DELETE, COUNT, etc.
     *
     * @return object
     **/
    final public function action_query($query = '', $connection_properties = array())
    {
        if (!is_object($this->_action_query_)) {
            $this->_action_query_ =
                $this->establish_connection($connection_properties);
        }
        
        if ($query) {
            $this->_action_query_->query($query);
        }
        
        return $this->_action_query_;
    }
    
    /**
     * Escape using current adapter and single quote $string.
     *
     * @param string $string Prep for SQL query
     * @return string
     **/
    final public function quote_value($string)
    {
        return "'" . $this->select_query()->escape((string) $string) . "'";
    }
    
    /**
     * Get class name of current model.
     *
     * @return void
     **/
    final public function class_name()
    {
        return $this->to_string();
    }
    
    /**
     * Set and get current table name in use.
     *
     * @param string $table_name If passed will set the default table name.
     * @return void
     **/
    final public function table_name($table_name = '')
    {
        return $this->_table_name_ = $table_name ? $table_name :
            ($this->_table_name_ ? $this->_table_name_ : Inflector::tableize($this->class_name()));
    }
    
    /**
     * Returns an array of column objects for the table associated with class.
     *
     * @return void
     **/
    final public function columns()
    {
        // only describe table once
        if (empty($this->_columns_)) {
            $this->_columns_ =
                $this->select_query()->columns($this->table_name());
        }
        
        return $this->_columns_;
    }
    
    /**
     * Returns an array of column names as strings.
     *
     * @return array
     **/
    final public function column_names()
    {
        return array_keys($this->columns());
    }
    
    /**
     * Returns an array of column objects for the table associated with class.
     *
     * @return void
     **/
    final public function columns_hash()
    {
        return (object) $this->columns();
    }
    
    /**
     * Execute $qry to get the total number of rows in current table. Must
     * pass a "SELECT COUNT(*)..." for query.
     *
     * @return void
     **/
    final public function count_by_sql($qry)
    {
        return (int) current($this->action_query($qry)->get_row());
    }
    
    /**
     * Execute a query to get the total number of rows in current table.
     *
     * @return void
     **/
    final public function count()
    {
        return $this->count_by_sql("SELECT COUNT(*) FROM `{$this->table_name()}`;");
    }
    
    
    /**
     * Checks if $attribute is a valid table column.
     *
     * @return void
     **/
    final public function attribute_exists($attribute)
    {
        return isset($this->_columns_[$attribute]);
    }
    
    /**
     * Return an array of column and values or sets the column and values
     * if an array is passed
     *
     * @param array $data
     * @return array
     **/
    final public function attributes($data = null)
    {
        // set column properties
        if (is_hash($data)) {
            // insert new vals
            foreach ($data as $k => $v) {
                $this->_columns_[$k]->value = $v;
            }
        } else {
            $attribites = array();
            // get column properties
            foreach($this->_columns_ as $k => $v) {
                $attribites[$k] = $v->value;
            }
            return $this->return_value($attribites);
        }
    }
    
    /**
     * Returns true if the passed attribute is a column of the class.
     *
     * @return void
     **/
    final public function has_attribute($attribute)
    {
        return array_key_exists($attribute, $this->columns());
    }
    
    /**
     * Get column name of the primary key.
     *
     * @return void
     **/
    final public function primary_key()
    {
        return $this->_primary_key_;
    }
    
    /**
     * Get value of the primary key.
     *
     * @return integer
     **/
    final public function id()
    {
        return $this->{$this->primary_key()};
    }
    
    /**
     * Save current object to database. If id set update else insert.
     *
     * @return void
     **/
    final public function save($validation_routine = 'validate')
    {
        // call back
        $this->before_save();
        
        // run validate routine
        $this->validate();
        
        // if error return false
        if ($this->has_errors()) return false;
        
        // if id update
        if ($this->id()) {
        
            // validate model on every update
            $this->validate_on_update();
            
            $ret_val = $this->update_row();
            
        } else {
            
            // call back
            $this->before_create();
            
            // validate model on every insert
            $this->validate_on_create();
            
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
     * Insert current model's values into database.
     *
     * @return integer Current result ID.
     **/
    final public function insert_row()
    {
        // set created at
        if ($this->attribute_exists('created_at')) {
            $this->created_at = datetime($this->created_at);
        }
        
        // sanitize values
        $values = array();
        foreach ($this->prepare_attributes() as $k => $v) {
            $values[$k] = $this->quote_value($v);
        }
        
        // build query
        $qry =    "INSERT INTO `{$this->table_name()}` " .
                "(`" . implode('`, `', array_keys($values)) . "`) " .
                "VALUES " .
                "(" . implode(', ', $values) . ");";
        
        return $this->id = $this->action_query($qry)->insert_id();
    }
    
    /**
     * Update database with the current model's values.
     *
     * @return integer Current result ID.
     **/
    final public function update_row()
    {
        // set updated at
        if ($this->attribute_exists('updated_at')) {
            $this->updated_at = datetime();
        }
        
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
     * Prepares the current columns and values for SQL.
     *
     * @return void
     **/
    final public function prepare_attributes()
    {
        $return = array();
        foreach ($this->_columns_ as $name => $field) {
            switch (true) {
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
     * Removes slashes from column value.
     *
     * @param string $attribute column name.
     * @return mixed
     **/
    final public function clean_column_value($attribute)
    {
        if (isset($this->_columns_[$attribute]->value)) {
            return strip_slashes($this->_columns_[$attribute]->value);
        } else {
            return '';
        }
    }
    
    /**
     * Deletes current object loaded or records that match the $conditions
     * passed. Returns the number of records affected.
     *
     * <code>
     * $model->destroy();
     * DELETE FROM `users` WHERE `users`.`id` = '2' LIMIT 1;
     *
     * $model->destroy(array('state' => 'NV', 'zip' => 89107));
     * DELETE FROM `users` WHERE (`users`.`state` = 'NV' AND `users`.`zip` = '89107');
     *
     * $model->destroy(array(
     *     '(state = ? OR other_state = ?) AND zip = ?',
     *     'NV', 'NV', 89107
     *     ));
     * DELETE FROM `users` WHERE ((state = 'NV' OR other_state = 'NV') AND zip = '89107');
     *
     * $model->destroy(array(
     *   '(state = :state OR other_state = :state) AND zip = :zip',
     *   array(
     *       'state' => 'NV',
     *       'zip' => 89107
     *       )
     *   ));
     * DELETE FROM `users` WHERE ((state = 'NV' OR other_state = 'NV') AND zip = '89107');
     * </code>
     *
     * @param mixed $conditions
     * @return integer
     **/
    final public function destroy($conditions = '')
    {
        if ($conditions) {
            $where = $this->build_query_from_conditions($conditions);
        } else {
            $where = "`{$this->table_name()}`.`{$this->primary_key()}` = '{$this->id()}' LIMIT 1";
        }
        
        // delete record(s) and return affected rows
        $this->action_query("DELETE FROM `{$this->table_name()}` WHERE {$where};");
        $affected_rows = $this->action_query()->affected_rows();
        
        return $affected_rows;
    }
    
    /**
     * Deletes current object loaded or records that match the $conditions
     * passed. Similar to destroy() except executes the before_delete() and
     * after_delete() call back functions. Returns the number of
     * records affected.
     *
     * @param mixed $conditions
     * @return integer
     * @see ActiveRecord::destroy()
     **/
    final public function delete($conditions = '')
    {
        // before delete call-back
        $this->before_delete();
        
        $affected_rows = $this->destroy($conditions);
        
        // after delete call-back
        $this->after_delete();
        
        return $affected_rows;
    }
    
    /**
     * Alias to find and sets the $_paging_ object. Default page limit is
     * 10 records.
     *
     * @see ActiveRecord::find()
     * @return void
     **/
    final public function paginate($args = null)
    {
        // search type
        $type = 'all';
        
        // create temp args
        $temp = $args;
        
        // set options for query count
        if (isset($temp['offset'])) unset($temp['offset']);
        if (isset($temp['order'])) unset($temp['order']);
        if (isset($temp['limit'])) unset($temp['limit']);
        $temp['select'] = "COUNT(*)";
        $qry = $this->build_query($temp, $type);
        
        // use select object to reduce connections
        $q = $this->select_query($qry);
        if ($q->total_rows() == 1) {
            $temp['total_records'] = current($q->get_row());
        } else {
            // return row count on joins
            $temp['total_records'] = $q->total_rows();
        }
        
        // set limt for paging
        if (isset($args['limit'])) $temp['limit'] = $args['limit'];
        
        // create page object
        $this->_paging_ = new ActivePager((object) $temp);
        
        // update agrs with paging data
        $args['offset'] = $this->_paging_->offset;
        $args['limit'] = $this->_paging_->limit;
        
        // execute query
        return $this->find($type, $args);
    }
    
    /**
     * Return a table object of current adapter set in database settings.
     *
     * @param string $table_name
     * @param array $db array of DB connecting settings
     * @return object
     **/
    final public function table_object($table_name = '', $db = null)
    {
        if (!is_array($db)) $db = self::connection_properties();
        if ($table_name) $db['table_name'] = $table_name;
        return self::establish_connection($db);
    }
    
    /**
     * Get a count of total rows from a query if paginating will return
     * total number of records found from query.
     *
     * @return integer
     **/
    final public function total_rows()
    {
        return isset($this->_paging_)
            ? $this->_paging_->total_records()
            : $this->select_query()->total_rows();
    }
    
    /**
     * Returns an associative array that corresponds to the fetched row
     * or NULL if there are no more rows.
     *
     * @return array
     **/
    final public function get_row()
    {
        return $this->select_query()->get_row();
    }
    
    // Section: Magic Functions
    
    /**
     * Magic function to get value of $attribute (column name).
     *
     * @param string $attribute
     * @return void
     **/
    public function __get($attribute)
    {
        try {
            
            switch (true) {
                case isset($this->_columns_[$attribute]):
                    return $this->return_value($this->clean_column_value($attribute));
                    break;
                    
                case isset($this->_associations_) && isset($this->_associations_[$attribute]):
                    return $this->return_value($this->association($this->_associations_[$attribute]));
                    break;
                    
                default:
                    throw new Exception("Attribute <em>{$attribute}</em> not" .
                    " found in <strong>{$this->class_name()}</strong> model.");
                    break;
            }
            
        } catch (Exception $e) {
            CREO('application_error', $e);
        }
    }
    
    /**
     * Magic function to set value of $attribute (column name).
     *
     * @param string $attribute
     * @param mixed $value
     * @return void
     **/
    public function __set($attribute, $value)
    {
        try {
            
            switch (true) {
                case $attribute == '_paging_':
                case $attribute == '_associations_':
                    return $this->{$attribute} = $value;
                    break;
                    
                case isset($this->_columns_[$attribute]):
                    return $this->_columns_[$attribute]->value = $value;
                    break;
                    
                case isset($this->_associations_) && isset($this->_associations_[$attribute]):
                    return $this->{$attribute} = $value;
                    break;
                    
                default:
                    throw new Exception("Attribute <em>{$attribute}</em> not" .
                    " found in <strong>{$this->class_name()}</strong> model.");
                    break;
            }
            
        } catch (Exception $e) {
            // add to errors
            CREO('application_error', $e);
        }
    }
    
    /**
     * Magic functions for form helpers, paging and validation.
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
                            'select_states_tag_for_',
                            'hidden_field_for_',
                            'password_field_for_',
                            'options_for_',
                            '_has_error',
                            'find_by_',
                            'date_',
                            'time_'
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
                case in_string('date_time_select_for_', $method):
                    $type = str_replace(
                                array(
                                    '_field_for_' . $name,
                                    '_for_' . $name
                                    ),
                                '',
                                $method);
                    return $this->html_field($type, $name, $this->{$name}, $arguments);
                    break;
                
                case in_string('options_for_', $method):
                    switch (true) {
                        // set options for ENUM field types
                        case isset($this->_columns_[$name]->type)
                            && in_string('enum(', $this->_columns_[$name]->type):
                        // if options for property is set
                        case isset($this->_columns_[$name]->options):
                            $type = 'select';
                            return $this->html_field($type, $name, $this->{$name}, $arguments);
                            break;
                        
                        // set options for TINYINT types
                        case !isset($this->_columns_[$name]->options)
                            && property_exists($this, 'options_for_' . $name)
                            && in_string('tinyint(1)', @$this->_columns_[$name]->type):
                            $type = 'select';
                            return $this->html_field($type, $name, $this->{$name}, array_merge($arguments, array('options' => array(1 => 'Yes', 0 => 'No'))));
                            break;
                        
                        // options_for_* called for existing field
                        case isset($this->_columns_[$name]) && is_array($arguments[0]):
                            return $this->_columns_[$name]->options = $arguments[0];
                            break;
                            
                        default:
                            throw new Exception("Unable to set options for {$name}." .
                                " Property <em>{$name}</em> not found in" .
                                " <strong>{$this->class_name()}</strong> model.");
                            break;
                    }
                    break;
                
                case in_string('_has_error', $method):
                    return $this->has_error($name, $arguments[0]);
                    break;
                
                case in_string('find_by_', $method):
                    $return = $this->find('all', array('conditions' => array(
                        $name => $arguments[0]
                        )));
                    
                    // if one record load the first
                    if ($this->total_rows() == 1) {
                        $this->current();
                    }
                    
                    return $return;
                    break;
                
                case in_string('validates_', $method):
                    return $this->validate_by_method($method, $arguments);
                    break;
                    
                /* Paging Links */
                case in_string('link_to_', $method):
                case in_string('paging_', $method):
                    if (method_exists($this->_paging_, $method)) {
                        return call_user_func_array(array($this->_paging_, $method), $arguments);
                    } else {
                        throw new Exception("Undefined method <em>{$method}</em> in <strong>ActivePager</strong> class.");
                    }
                    break;
            }
            
            if (!method_exists($this, $method)) {
                throw new Exception("Method <em>{$method}</em> not found in " .
                    "<strong>{$this->class_name()}</strong> model.");
            }
        } catch (Exception $e) {
            // add to errors
            CREO('application_error', $e);
        }
    }
    
    /**
     * Validate wrapper function to call validation routine for a particular
     * field and value.
     *
     * @return void
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
            unset($arguments[0]);    // remove field name
            unset($arguments[1]);    // remove field value
            
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
                        // check if a column with that value exists in the
                        // current table and is not the currentlly loaded row
                        $this->action_query(
                            "SELECT * FROM `{$this->table_name()}`
                            WHERE `{$params[0]}` = '{$params[1]}'
                                AND `{$this->primary_key()}` != '{$this->id}' AND (".$where_ext.")");
                        
                        // if record found add error
                        if ($this->action_query()->total_rows()) {
                            ActiveValidation::add_error($params[0],
                                ($params[2] ? $params[2] : humanize($params[0]).' is not unique.' ));
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
                            throw new Exception("Undefined validation method <em>{$method}</em> in " .
                                "<strong>{$this->class_name()}</strong> model.");
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
    final public function html_field($type, $name, $value, $arguments = array())
    {
        // set form vars
        $html = '';
        $field_name = strtolower($this->class_name() . "[{$name}]");
        $arguments[0] = isset($arguments[0]) ? $arguments[0] : null;
        @$html_options = $arguments[0];
        
        // get HTML
        switch ($type) {
            case 'text':
                $html = text_field($field_name, $value, $html_options);
                break;
            
            case 'password':
                $html = password_field($field_name, $value, $html_options);
                break;
            
            case 'hidden':
                $html = hidden_field($field_name, $value, $html_options);
                break;
            
            case 'check_box':
            case 'checkbox':
            case 'radio_button':
                $tag_value = isset($arguments[1]) ? $arguments[1] : $value;
                $text = isset($arguments[2]) ? $arguments[2] : '';
                $func = $type == 'radio_button' ? 'radio_button' : 'check_box';
                $html = $func($field_name, $value, $html_options, $tag_value, $text);
                break;
            
            case 'text_area':
            case 'textarea':
                $html = textarea($field_name, $value, $html_options);
                break;
            
            case 'select':
                if (isset($this->{'options_for_' . $name})) {
                    $options = $this->{'options_for_' . $name};
                } else if (isset($this->_columns_[$name]->options)) {
                    $options = $this->_columns_[$name]->options;
                } else if (isset($arguments['options'])) {
                    $options = $arguments['options'];
                    unset($arguments['options']);
                } else {
                    $options = $this->enum_options($name);
                }
                $html_options = $arguments[0];
                $arguments[1] = isset($arguments[1]) ? $arguments[1] : null;
                $html = select($field_name, $value, $options, $html_options, $arguments[1]);
                break;
            
            case 'select_countries_tag':
            case 'select_states_tag':
                $html_options = $arguments[0];
                $arguments[1] = isset($arguments[1]) ? $arguments[1] : null;
                $options = isset($this->{'options_for_' . $name})
                                ? $this->{'options_for_' . $name}
                                : array();
                
                if ($type == 'select_states_tag') {
                    $func = 'select_states_tag';
                } else {
                    $func = 'select_countries_tag';
                }
                
                $html = $func(
                            $field_name,
                            $value,
                            $options,
                            $html_options,
                            $arguments[1]
                            );
                break;
            
            case 'datetime_select':
            case 'date_time_select':
            case 'date_select':
            case 'time_select':
                $html_options = $arguments[0];
                $html = $type($field_name, $value, $html_options);
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
    final public function add_error($property, $msg)
    {
        $GLOBALS['CREOVEL']['VALIDATION_ERRORS'][$property] = $msg;
    }
    
    /**
     * Check if field is validation errors array.
     *
     * @param string $property
     * @param string $return_text_on_true
     * @return boolean
     **/
    final public function has_error($property, $return_text_on_true = '')
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
    final public function has_errors()
    {
        return @count($GLOBALS['CREOVEL']['VALIDATION_ERRORS']);
    }
    
    /**
     * Check if model passes validation routine.
     *
     * @return boolean
     **/
    final public function validates()
    {
        // run validate routine
        $this->validate();
        
        // if error return false
        return !$this->has_errors();
    }
    
    /**
     * Get options for ENUM field types.
     *
     * @return void
     **/
    final public function enum_options($property)
    {
        if (in_string('enum(', $this->_columns_[$property]->type)) {
            $options = explode("','", str_replace(
                                                array("enum('"),
                                                '',
                                                substr($this->_columns_[$property]->type, 0, -2)
                                                ));
            $return = array();
            foreach ($options as $value) {
                $return[$value] = humanize($value);
            }
            return $return;
        }
    }
    
    /**
     * Association methods.
     */
    
    /**
     * Build association rules.
     *
     * @param array $options
     * @return void
     **/
    final public function build_association($options)
    {
        // set associations property
        if (!isset($this->_associations_)) $this->_associations_ = array();
        
        // set foreign key
        if (empty($options['foreign_key'])) {
            if ($options['type'] == 'has_one' || $options['type'] == 'has_many') {
                 $options['foreign_key'] = strtolower($this->to_string()) . '_id';
            } else {
                $options['foreign_key'] = $options['association_id'] . '_id';
            }
        }
        
        $this->_associations_[$options['association_id']] = $options;
    }
    
    /**
     * Create association and returns a new object of the associated type.
     *
     * @param array $options
     * @return object
     **/
    final private function association($options)
    {
        // set class name
        $options['class_name'] = Inflector::classify(empty($options['class_name'])
                        ? $options['association_id'] : $options['class_name']);
        
        // set args
        $args = array();
        if (!empty($options['where'])) {
            $args['conditions'] = $options['where'];
        } elseif (!empty($options['conditions'])) {
            $args['conditions'] = $options['conditions'];
        } else {
            $args['conditions'] = '';
        }
        if (!empty($options['order'])) $args['order'] = $options['order'];
        if (!empty($options['limit'])) $args['limit'] = $options['limit'];
        
        switch ($options['type']) {
            case 'belongs_to':
                $args['conditions'] = array('id' => $this->{$options['foreign_key']});
                $obj = new $options['class_name'];
                $obj->find('first', $args);
                break;
                
            case 'has_one':
                $args['conditions'] = array($options['foreign_key'] => $this->id());
                $obj = new $options['class_name'];
                $obj->find('first', $args);
                break;
                
            case 'has_many':
                $args['conditions'] = array($options['foreign_key'] => $this->id());
                $obj = new $options['class_name'];
                $obj->find('all', $args);
                break;
        }
        
        // create association property
        $this->{$options['association_id']} = $obj;
        
        return $this->{$options['association_id']};
    }
    
    /**
     * Set a "belongs to" relationship for the current model.
     *
     * @param string $association_id
     * @param array $options
     * @return void
     **/
    final public function belongs_to($association_id, $options = array())
    {
        $options['type'] = 'belongs_to';
        $options['association_id'] = $association_id;
        $this->build_association($options);
    }
    
    /**
     * Set a "has one" relationship for the current model.
     *
     * @param string $association_id
     * @param array $options
     * @return void
     **/
    public function has_one($association_id, $options = array())
    {
        $options['type'] = 'has_one';
        $options['association_id'] = $association_id;
        $this->build_association($options);
    }
    
    /**
     * Set a "has many" relationship for the current model.
     *
     * @param string $association_id
     * @param array $options
     * @return void
     **/
    final public function has_many($association_id, $options = array())
    {
        $options['type'] = 'has_many';
        $options['association_id'] = $association_id;
        $this->build_association($options);
    }
    
    /**
     * Iterator methods.
     */
    
    /**
     * Resets DB properties and frees result resources.
     *
     * @return void
     **/
    final public function reset()
    {
        $this->select_query()->reset();
    }
    
    /**
     * Set the result object pointer to its first element.
     *
     * @return void
     **/
    final public function rewind()
    {
        return $this->select_query()->rewind();
    }
    
    /**
     * Return the current row in result object as clone Model Object.
     *
     * @return object
     **/
    final public function current()
    {
        // set attributes with current row
        $this->attributes($this->select_query()->current());
        
        // initialize class vars for each record
        $this->initialize_class_vars();
        
        return clone $this;
    }
    
    /**
     * Returns the index element of the current result object pointer.
     *
     * @return integer
     **/
    final public function key()
    {
        return $this->select_query()->key();
    }
    
    /**
     * Advance the result object pointer.
     *
     * @return object
     **/
    final public function next()
    {
        $this->select_query()->next();
        return $this->current();
    }
    
    /**
     * Rewind the result object pointer by one.
     *
     * @return object
     **/
    final public function prev()
    {
        $this->select_query()->prev();
        return $this->current();
    }
    
    /**
     * Adjusts the result pointer to an arbitrary row in the result and returns
     * TRUE on success or FALSE on failure.
     *
     * @return boolean
     **/
    final public function valid()
    {
        return $this->select_query()->valid();
    }
    
    /**
     * Check if this object's results is paged.
     *
     * @return boolean
     **/
    final function is_paged()
    {
        return isset($this->_paging_);
    }
    
    /**
     * Pass through function used to add Prototype functionality to
     * value if prototype is enabled.
     *
     * @param mixed $value
     * @return mixed
     **/
    public function return_value($value)
    {
        if ($this->prototype(1)) {
            return new Prototype($value);
        } else {
            return $value;
        }
    }
    
    /**
     * Enable prototype data extending. If $check true verify if
     * prototype has been enabled.
     *
     * @param boolean $check
     * @return boolean
     **/
    public function prototype($check = false)
    {
        static $enable;
        if (!$check) $enable = true;
        return $enable;
    }
    
    /**#@+
     * Callback Function.
     *
     * @access public
     * @return mixed
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
    /**#@-*/
} // END abstract class ActiveRecord extends Object implements Iterator