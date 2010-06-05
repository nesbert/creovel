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
class ActiveRecord extends CObject
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
     * @var array
     **/
    public $_primary_key_ = array();
    
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
     * @param boolean $initialize Model table and initialize magic properties
     * @return void
     **/
    public function __construct($data = null, $connection_properties = null, $initialize = true)
    {
        // load connection if passed
        if (is_array($connection_properties)) {
            $this->connection_properties = $connection_properties;
        }
        
        if ($initialize) {
            // model table and set columns
            $this->columns();
            
            // call parent
            $this->initialize_parents();
            
            // load data if passed
            if ($data) {
                $this->load_model_data($data);
            }
            
            // initialize class vars if no record loaded
            if (!$this->is_primary_keys_and_values_set()) {
                $this->initialize_class_vars();
            }
        }
        
        if ($initialize) {
            // set table name
            $this->table_name();
        }
    }
    
    /**
     * Load an array of parameters OR an ID OR an array of IDs into
     * the model and update attributes if an array of params is passed.
     *
     * @param mixed $data
     * @see mixed ActiveRecord::load_by_primary_key()
     * @return void
     **/
    final public function load_model_data($data)
    {
        // $data hash load
        if (CValidate::hash($data) || is_object($data)) {
            // load data into object
            $this->attributes($data);
        }
        
        // if data contains primary key data load record first
        $this->load_by_primary_key($data);
    }
    
    /**
     * Load an array of parameters OR an ID OR an array of IDs into
     * the model.
     * 
     * @return void
     **/
    final public function load_by_primary_key($data = null)
    {
        $keys = $this->primary_key();
        $data = $data ? $data : $this->primary_keys_and_values();
        $search_type = 'first';
        if (is_object($data)) $data = (array) $data;
        
        // if assc array
        if (CValidate::hash($data)) {
            // do nothing $data already in correct format
        } elseif (count($keys) == 1) {
            if (is_array($data)) {
                $search_type = 'all';
            } else {
                $data = array($keys[0] => $data);
            }
        } else {
            $keys = implode(', ', $keys);
            throw new Exception("Primary keys <em>{$keys}</em> are not" .
            " set in <strong>{$this->class_name()}</strong> model.");
            break;
        }
        
        $this->find($search_type, array(
                'conditions' => $data
            ));
        
        return $this->total_rows() ? true : false;
    }
    
    /**
     * Load a record by primary keys and values if all primary keys
     * are set with values. Returns true if it was able to load a record.
     *
     * @return boolean
     **/
    final public function load_by_primary_keys_and_values()
    {
        if ($this->is_primary_keys_and_values_set()) {
            return $this->load_by_primary_key();
        } else {
            return false;
        }
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
                case CValidate::in_string('options_for_', $var):
                    $this->{'set_' . $var}($vals);
                    break;
                    
                case 'belongs_to' == $var:
                case 'has_many' == $var:
                case 'has_one' == $var:
                    if (is_array($vals)) foreach ($vals as $val) {
                        if (is_array($val)) {
                            if (isset($val['association_id'])) {
                                $association_id = $val['association_id'];
                            } elseif (isset($val['name'])) {
                                $association_id = $val['name'];
                            } else {
                                $association_id = $var;
                            }
                            $this->{$var}($association_id, $val);
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
        CREO('application_error_code', 500);
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
        // check query to see which resource to use
        if (preg_match('/^(UPDATE|INSERT|DELETE|CREATE|DROP) */i', trim($sql))) {
            $action_query = true;
        }
        
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
     * @return object
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
            $options['table'] = $this->table_name();
            $options['columns'] = &$this->_columns_;
            
            if ($type == 'first') {
                $options['limit'] = 1;
            }
            
            $sql = $this->select_query()->build_query($options);
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
     * Prepare a SQL statement for execution. Accepts a conditions array
     * argument array.
     *
     * @param string $sql
     * @param array $params
     * @return string
     **/
    public function prepare()
    {
        return $this->_prepared_query_ = $this->selec_query()->build_query_from_conditions($this->table_name(), func_get_args(), false);
    }
    
    /**
     * Execute prepared SQL statement.
     *
     * @param boolean $load_first
     * @return object/false
     **/
    public function execute($load_first = false)
    {
        if (empty($this->_prepared_query_)) return false;
        return $this->find_by_sql($this->_prepared_query_, $load_first ? 'first' : 'all');
    }
    
    /**
     * Create select query object for SELECTS.
     *
     * @param string $query
     * @param array $connection_properties
     * @return object
     **/
    final public function select_query($query = '', $connection_properties = array())
    {
        if (!is_object($this->_select_query_)) {
            if (empty($connection_properties) &&
                !empty($this->connection_properties)) {
                $connection_properties = $this->connection_properties;
            }
            
            // set which settings mode to use
            if (isset($this) && !empty($this->_mode_)) {
                $connection_properties = $GLOBALS['CREOVEL']['DATABASES'][strtoupper($this->_mode_)];
            }
            
            $this->_select_query_ = new ActiveQuery($connection_properties);
        }
        
        if ($query) {
            $this->_select_query_->query($query);
        }
        
        return $this->_select_query_;
    }
    
    /**
     * Create action query object for INSERT, UPDATE, DELETE, COUNT, etc.
     *
     * @param string $query
     * @param array $connection_properties
     * @return object
     **/
    final public function action_query($query = '', $connection_properties = array())
    {
        if (!is_object($this->_action_query_)) {
            if (empty($connection_properties) &&
                !empty($this->connection_properties)) {
                $connection_properties = $this->connection_properties;
            }
            
            // set which settings mode to use
            if (isset($this) && !empty($this->_mode_)) {
                $connection_properties = $GLOBALS['CREOVEL']['DATABASES'][strtoupper($this->_mode_)];
            }
            
            $this->_action_query_ = new ActiveQuery($connection_properties);
        }
        
        if ($query) {
            $this->_action_query_->query($query);
        }
        
        return $this->_action_query_;
    }
    
    /**
     * Escape using current adapter.
     *
     * @param string $string Prep for SQL query
     * @return string
     **/
    final public function escape($string)
    {
        return $this->select_query()->escape((string) $string);
    }
    
    /**
     * Escape using current adapter and single quote $string.
     *
     * @param string $string Prep for SQL query
     * @return string
     **/
    final public function quote_value($string)
    {
        return $this->select_query()->quote_value($string);
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
        $this->_table_name_ = $table_name ? $table_name : $this->_table_name_;
        $this->_table_name_ = $this->_table_name_ ? $this->_table_name_ : Inflector::tableize($this->class_name());
        if (0) $this->_table_name_ = strtoupper($this->_table_name_);
        return $this->_table_name_;
    }
    
    /**
     * Returns an array of column objects for the table associated with class.
     *
     * @param boolean $force_table_look_up
     * @return void
     **/
    final public function columns($force_table_look_up = false)
    {
        // only describe table once
        if (empty($this->_columns_)) {
            
            if ($this->has_schema && !$force_table_look_up) {
                $db2xml = new DatabaseXML($this);
                $db2xml->load_file();
                $this->_columns_ = $db2xml->columns();
            } else {
                $this->_columns_ = $this->select_query()->columns($this->table_name());
            }
            
            // do some magic
            foreach ($this->_columns_ as $k => $v) {
                // set default options for enum types
                if (!isset($this->{'options_for_' . $k}) && CValidate::in_string('enum(', $v->type)) {
                    $v->options = $this->field_options($k);
                }
            }
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
     * @param string $qry
     * @return integer
     **/
    final public function count_by_sql($qry)
    {
        return (int) current($this->action_query($qry)->get_row());
    }
    
    /**
     * Execute a query to get the total number of rows in current table.
     *
     * @param array $options
     * @return integer
     **/
    final public function count($options = array())
    {
        $options['select'] = "COUNT(*)";
        return $this->count_by_sql($this->build_query($options, $type = 'all'));
    }
    
    
    /**
     * Checks if $attribute is a valid table column.
     *
     * @param string $attribute
     * @see ActiveRecord::has_attribute();
     * @return void
     **/
    final public function attribute_exists($attribute)
    {
        return $this->has_attribute($attribute);
    }
    
    /**
     * Return an array of column and values or sets the column and values
     * if an array|object is passed
     *
     * @param array/object $data
     * @return array
     **/
    final public function attributes($data = null)
    {
        // set column properties
        if (CValidate::hash($data) || is_object($data)) {
            // insert new vals
            foreach ($data as $k => $v) {
                $this->{$k} = $v;
            }
        // get column properties
        } else {
            $attribites = array();
            foreach($this->_columns_ as $k => $v) {
                $attribites[$k] = $v->value;
            }
            return $this->return_value($attribites);
        }
    }
    
    /**
     * Set column values from a query result by passing the __set method.
     * 
     * @param array/object $data
     * @return void
     **/
    final private function set_attributes_from_query($data)
    {
        if (CValidate::hash($data) || is_object($data)) {
            // insert new vals
            foreach ($data as $k => $v) {
                $this->_columns_[$k]->value = $v;
            }
        }
    }
    
    /**
     * Return an object attributes.
     *
     * @return object
     **/
    final public function attributes_object()
    {
        return (object) $this->attributes();
    }
    
    /**
     * Returns true if the passed attribute is a column of the class.
     *
     * @param string $attribute
     * @return void
     **/
    final public function has_attribute($attribute)
    {
        return array_key_exists($attribute, $this->columns());
    }
    
    /**
     * Check if an attribute is a primary key.
     *
     * @param string $attribute
     * @return boolean
     **/
    final public function is_primary_key($attribute)
    {
        return in_array($attribute, $this->primary_key());
    }
    
    /**
     * Check if primary key(s) are set and have values. If using multiple
     * columns as primary key check all keys are set and have values.
     *
     * @return boolean
     **/
    final public function is_primary_keys_and_values_set()
    {
        foreach ($this->primary_key() as $key) {
            if (empty($this->_columns_[$key]->value)) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Get column name of the primary key(s).
     *
     * @return array()
     **/
    final public function primary_key()
    {
        if (empty($this->_primary_key_)) {
            // find primary keys
            foreach ($this->columns() as $column => $attr) {
                if (isset($attr->key) && $attr->key == 'PK') {
                    $this->_primary_key_[] = $column;
                }
            }
        }
        
        if (!is_array($this->_primary_key_)) {
            throw new Exception("Primary key <em>{$this->_primary_key_}</em> not" .
            " is not an array in  <strong>{$this->class_name()}</strong> model.");
            break;
        }
        
        return $this->_primary_key_;
    }
    
    /**
     * Get an associative array or primary keys and values.
     *
     * @return array
     **/
    final public function primary_keys_and_values()
    {
        $keys  = array();
        foreach ($this->primary_key() as $key) {
            $keys[$key] = $this->_columns_[$key]->value;
        }
        return $keys;
    }
    
    /**
     * Save current object to database. If id set update else insert.
     *
     * @param boolean $reload_on_success
     * @return void
     **/
    public function save($reload_on_success = true)
    {
        // call back
        $this->before_save();
        
        // run validate routine
        $this->validate();
        
        // if error return false
        if ($this->has_errors()) return false;
        
        // conditions array for record reload
        $conditions = array();
        
        // if record found update
        if ($this->total_rows() || $this->_was_inserted_) {
        
            // validate model on every update
            $this->validate_on_update();
            
            $ret_val = $this->action_query()->update_row($this->table_name(), $this->_columns_, $this->primary_keys_and_values());
            
            if (!empty($ret_val)) {
                $conditions = $this->primary_keys_and_values();
            }
            
        } else {
            
            // call back
            $this->before_create();
            
            // validate model on every insert
            $this->validate_on_create();
            
            $ret_val = $this->action_query()->insert_row($this->table_name(), $this->_columns_);
            
            if (!empty($ret_val)) {
                // set inserted flag
                $this->_was_inserted_ = true;
                
                // if insert id... loaded identy increament field
                if ($id = $this->action_query()->insert_id()) {
                    // find auto increment field and set ID
                    foreach ($this->columns() as $col => $field) {
                        if ($field->is_identity) {
                            $conditions[$col] = $id;
                            break;
                        }
                    }
                } else {
                    foreach ($this->_columns_ as $col => $field) {
                        if ($field->has_changed) {
                            $conditions[$col] = $field->value;
                        }
                    }
                }
            }
        }
        
        #foreach ($this->child_objects as $obj) $obj->save();
        
        if ($ret_val) {
            // load record to make sure to get most recent info
            // from DB for all the columns
            if ($reload_on_success) {
                $this->find('first', array('conditions' => $conditions));
            }
            
            // call back
            $this->after_save();
            
            return $ret_val;
        } else {
            return false;
        }
    }
    
    /**
     * Get the last insert id.
     *
     * @return integer
     **/
    public function insert_id()
    {
        return $this->action_query()->insert_id();
    }
    
    /**
     * Get the number of affected rows.
     *
     * @return integer
     **/
    public function affected_rows()
    {
        return $this->action_query()->affected_rows();
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
    final public function destroy($conditions = null)
    {
        if (empty($conditions)) {
            $conditions = $this->primary_keys_and_values(); 
        }
        
        // delete record(s) and return affected rows
        return $this->action_query()->delete($this->table_name(), $conditions);
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
    public function delete($conditions = '')
    {
        // before delete call-back
        $this->before_delete();
        
        $affected_rows = $this->destroy($conditions);
        
        // after delete call-back
        $this->after_delete();
        
        return $affected_rows;
    }
    
    /**
     * Updates current object loaded or records that match the $conditions
     * passed. Similar to find() except expects $options['update'] to set
     * field and values.
     *
     * @param mixed $type 'all', 'first', ID or array of IDs
     * @param array $options 'update 
     * @return integer
     * @see ActiveRecord::find()
     **/
    public function update($type = 'all', $options = array())
    {
        if (!isset($options['update'])) {
            throw new Exception('Missing argument <em>update</em> not set' .
            " in options array for <strong>{$this->class_name()}::update</strong>.");
        } elseif (!CValidate::hash($options['update']) && !is_object($options['update'])) {
            throw new Exception('Argument <em>update</em> must be an array' .
            " or object of attributes for <strong>{$this->class_name()}::update</strong>.");
        }
        
        // before update call-back
        $this->before_update();
        
        $options['table'] = $this->table_name();
        
        foreach ($options['update'] as $k => $v) {
            // if primary and attr doesn exits skip
            if ($this->is_primary_key($k) || !$this->attribute_exists($k)) {
                unset($options['update'][$k]);
            }
        }        
        
        $sql = $this->action_query()->build_query($options);
        
        $affected_rows = $this->action_query($sql)->affected_rows();
        
        // after update call-back
        $this->after_update();
        
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
        // create temp args
        $temp = $args;
        
        // set options for query count
        if (isset($temp['offset'])) unset($temp['offset']);
        if (isset($temp['order'])) unset($temp['order']);
        if (isset($temp['limit'])) unset($temp['limit']);
        
        $key = array();
        foreach ($this->primary_key() as $k) {
            $key[] = $this->select_query()->build_identifier(array(
                        $this->table_name(),
                        $k));
        }
        $key = implode(",", $key);
        $temp['select'] = "COUNT(*)";
        $temp['table'] = $this->table_name();
        $temp['group'] = $key; 
        $qry = $this->select_query()->build_query($temp);
        
        // use select object to reduce connections
        $q = $this->select_query($qry);
        
        if ($q->total_rows() == 1) {
            $temp['total_records'] = current($q->current());
        } else {
            $temp['total_records'] = $q->total_rows();
        }
        
        // set limt for paging
        if (isset($args['limit'])) $temp['limit'] = $args['limit'];
        
        // create page object
        $this->_paging_ = new Paginator((object) $temp);
        
        // update agrs with paging data
        $args['offset'] = $this->_paging_->offset;
        $args['limit'] = $this->_paging_->limit;
        
        // execute query
        return $this->find('all', $args);
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
        if (!is_array($db)) $db = ActiveDatabase::connection_properties();
        if ($table_name) $db['table_name'] = $table_name;
        return self::establish_connection($db);
    }
    
    /**
     * Return an ActiveRecord object. Useful accessing tables without creating
     * models, like for standalone scripts.
     *
     * @param string $table_name
     * @param mixed $data
     * @param array $db array of DB connecting settings
     * @return object
     **/
    final public function object($table_name = '', $data = '', $db = null)
    {
        if (!is_array($db)) $db = ActiveDatabase::connection_properties();
        $o = new ActiveRecord(null, $db, false);
        if ($table_name) $o->table_name($table_name);
        if ($data) $o->load_model_data($data);
        return $o;
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
     * Returns an object that corresponds to the fetched row
     * or NULL if there are no more rows.
     *
     * @return object
     **/
    final public function get_row()
    {
        return $this->select_query()->get_row();
    }
    
    /**
     * Reload object from current select query.
     *
     * @return void
     **/
    final public function reload()
    {
        return $this->query($this->select_query()->query);
    }
        
    /**
     * Reload object from current select query.
     *
     * @return void
     **/
    final public function reload_record()
    {
        return  $this->load_by_primary_key($this->primary_keys_and_values());
    }    
    
    /**
     * Reset values to there defaults.
     *
     * @return void
     **/
    final public function reset_values()
    {
        foreach($this->_columns_ as $k => $v) {
        	$this->_columns_[$k]->has_changed = false;
            $this->_columns_[$k]->value = $v->default;
        }
    }
    
    /**
     * Free result object.
     *
     * @return void
     **/
    public function free_result()
    {
        if (!empty($this->_select_query_->result)) {
            return $this->_select_query_->free_result();
        }
        
        return false;
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
                // skip these special cases
                case $attribute == 'has_schema':
                case $attribute == '_was_inserted_':
                    break;
                case isset($this->_columns_[$attribute]):
                    return $this->return_value($this->clean_column_value($attribute));
                    break;
                    
                case isset($this->_associations_) && isset($this->_associations_[$attribute]):
                    return $this->return_value($this->association($this->_associations_[$attribute]));
                    break;
                    
                case CValidate::in_string('options_for_', $attribute) && isset($this->_columns_[$this->field_name_from_str($attribute)]->options):
                    return $this->return_value($this->_columns_[$this->field_name_from_str($attribute)]->options);
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
                    
                // set column value
                case isset($this->_columns_[$attribute]):
                    if ($this->_columns_[$attribute]->value == $value) {
                        $this->_columns_[$attribute]->has_changed = false;
                    } else {
                        $this->_columns_[$attribute]->has_changed = true;                        
                    }
                    return $this->_columns_[$attribute]->value = $value;
                    break;
                    
                // allow hidden properties
                case $attribute == '_prepared_query_':
                case $attribute == '_mode_':
                case $attribute == '_host_':
                case $attribute == '_port_':
                case $attribute == '_socket_':
                case $attribute == '_database_':
                case $attribute == '_paging_':
                case $attribute == '_associations_':
                case $attribute == '_was_inserted_':
                case $attribute == 'connection_properties':
                    return $this->{$attribute} = $value;
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
            $name = $this->field_name_from_str($method);
            $arguments[0] = isset($arguments[0]) ? $arguments[0] : '';
            
            switch (true) {
                case CValidate::in_string('field_for_', $method):
                case CValidate::in_string('select_for_', $method):
                case CValidate::in_string('text_area_for_', $method):
                case CValidate::in_string('textarea_for_', $method):
                case CValidate::in_string('check_box_for_', $method):
                case CValidate::in_string('checkbox_for_', $method):
                case CValidate::in_string('radio_button_for_', $method):
                case CValidate::in_string('select_countries_tag_for_', $method):
                case CValidate::in_string('select_states_tag_for_', $method):
                case CValidate::in_string('date_time_select_for_', $method):
                    $type = str_replace(
                                array(
                                    '_field_for_' . $name,
                                    '_for_' . $name
                                    ),
                                '',
                                $method);
                    return $this->html_field($type, $name, $this->{$name}, $arguments);
                    break;
                
                // options_for_* called for existing field
                case CValidate::in_string('set_options_for_', $method)
                    && isset($this->_columns_[$name])
                    && is_array($arguments[0]):
                    return $this->_columns_[$name]->options = $arguments[0];
                    break;
                
                case CValidate::in_string('options_for_', $method):
                    switch (true) {
                        // set options for ENUM field types
                        case isset($this->_columns_[$name]->type)
                            && CValidate::in_string('enum(', $this->_columns_[$name]->type):
                        // set options for ENUM field types
                        case isset($this->_columns_[$name]->type)
                            && CValidate::in_string('tinyint(1)', $this->_columns_[$name]->type):
                        // if options for property is set
                        case isset($this->_columns_[$name]->options):
                            $type = 'select';
                            return $this->html_field($type, $name, $this->{$name}, $arguments);
                            break;
                        
                        default:
                            throw new Exception("Unable to create options for {$name}." .
                                " Options for <em>{$name}</em> not set" .
                                " <strong>{$this->class_name()}</strong> model.");
                            break;
                    }
                    break;
                
                case CValidate::in_string('_has_error', $method):
                    return $this->has_error($name, $arguments[0]);
                    break;
                
                case CValidate::in_string('find_by_', $method):
                    $args = isset($arguments[1]) && CValidate::hash($arguments[1]) ? $arguments[1] : array();
                    $args['conditions'] = array($name => $arguments[0]);
                    $return = $this->find('all', $args);
                    
                    // if one record load the first
                    if ($this->select_query()->total_rows() == 1) {
                        $this->current();
                    }
                    
                    return $return;
                    break;
                
                case CValidate::in_string('validates_', $method):
                    return $this->validate_by_method($method, $arguments);
                    break;
                    
                /* Paging Links */
                case CValidate::in_string('link_to_', $method):
                case CValidate::in_string('paging_', $method):
                    if (method_exists($this->_paging_, $method)) {
                        return call_user_func_array(array($this->_paging_, $method), $arguments);
                    } else {
                        throw new Exception("Undefined method <em>{$method}</em> in <strong>Paginator</strong> class.");
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
     * Return field name from string for magic calls.
     *
     * @return string
     **/
    public function field_name_from_str($str)
    {
        return str_replace(array(
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
                        'set_options_for_',
                        'options_for_',
                        '_has_error',
                        'find_by_',
                        'date_',
                        'time_'
                        ), '', $str);
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
                $params[1] = $this->{$field};
                $params[2] = isset($params[2]) ? $params[2] : '';
                
                // process validation by method name                
                switch ($method) {
                    // check if a column with that value exists in the
                    // current table and is not the currentlly loaded row
                    case 'validates_uniqueness_of':
                        // no value return false
                        if (!$params[1]) return false;
                        
                        // build key to support multipule rows
                        $ops = array();
                        $ops['select'] = "COUNT(*)";
                        //$ops['conditions'] = $this->primary_keys_and_values();
                        $ops['conditions'][$params[0]] = $params[1];
                        $ops['table'] = $this->table_name();
                        // extend WHERE statement
                        if (!empty($params[3])) $ops['where_append'] = $params[3];
                        
                        // build query
                        $q = $this->action_query()->build_query($ops);
                        
                        // if record found add error
                        $this->action_query($q);
                        
                        if (current($this->action_query()->current())) {
                            ActiveValidation::add_error(
                                $params[0],
                                ($params[2] 
                                    ? $params[2]
                                    : CString::humanize($params[0]).' is not unique.')
                                );
                            return false;
                        } else {
                            return true;
                        }
                        break;
                    
                    // check that validation method exists
                    default:
                        // if validation method esits in validation class
                        $c = new ActiveValidation; // inistanciate cause of bug accessing it staticly
                        if (method_exists($c, $method)) {
                            return call_user_func_array(array('ActiveValidation', $method), $params);
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
        $field_name = CString::underscore($this->class_name()) . "[{$name}]";
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
                    $options = $this->field_options($name);
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
        @$GLOBALS['CREOVEL']['VALIDATION_ERRORS'][$property] = $msg;
    }
    
    /**
     * Get property error from $GLOBALS['CREOVEL']['VALIDATION_ERRORS'].
     *
     * @param string $property
     * @return string
     **/
    final public function get_error($property)
    {
        return @$GLOBALS['CREOVEL']['VALIDATION_ERRORS'][$property];
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
     * Reset validation errors.
     *
     * @return void
     **/
    final public function reset_errors()
    {
        $GLOBALS['CREOVEL']['VALIDATION_ERRORS'] = array();
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
    final public function field_options($property)
    {
        if (CValidate::in_string('enum(', $this->_columns_[$property]->type)) {
            $options = explode("','", str_replace(
                                                array("enum('"),
                                                '',
                                                substr($this->_columns_[$property]->type, 0, -2)
                                                ));
            $return = array();
            foreach ($options as $value) {
                $return[$value] = CString::humanize($value);
            }
            return $return;
        }
        if (CValidate::in_string('tinyint(1)', $this->_columns_[$property]->type)) {
            return array('1' => 'Yes', '0' => 'No');
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
        $options['class_name'] = empty($options['class_name'])
                        ? CString::classify($options['association_id']) : $options['class_name'];

        // set for linking class
        $obj = new $options['class_name'];
        
        $table_str = $this->action_query()->build_identifier(array($this->table_name()));
        $id_str = 'id';
        $fk = Inflector::singularize($obj->table_name()) . '_' . $id_str;
        
        if ($this->action_query()->get_adapter_type() == 'ibmdb2') {
            $id_str = strtoupper($id_str);
            $fk = strtoupper($fk);
        }
        
        // set args
        $args = array('select' => "`{$obj->table_name()}`.*");
        if (!empty($options['where'])) {
            $args['conditions'] = $options['where'];
        } elseif (!empty($options['conditions'])) {
            $args['conditions'] = $options['conditions'];
        } else {
            $args['conditions'] = '';
        }
        if (!$args['conditions']) {
            
            switch (true) {
                case !empty($options['foreign_key']):
                    $args['conditions'] = $this->foreign_keys_and_values($options['foreign_key']);
                    break;
                
                case $options['type'] == 'belongs_to':
                    $link1 = $this->action_query()->build_identifier(array($this->table_name(), $fk));
                    $link2 = $this->action_query()->build_identifier(array($this->table_name(), $id_str));
                    $args['join'] = "INNER JOIN {$table_str} ON {$link1} = {$link2}";
                    $args['conditions'] = $this->primary_keys_and_values();
                    break;
                
                case $options['type'] == 'has_many':
                case $options['type'] == 'has_one':
                    $link1 = $this->action_query()->build_identifier(array($this->table_name(), $id_str));
                    $link2 = $this->action_query()->build_identifier(array($this->table_name(), $fk));
                    $args['join'] = "INNER JOIN {$table_str} ON {$link1} = {$link2}";
                    $args['conditions'] = $this->primary_keys_and_values();
                    break;
            }
            
        }
        
        if (!empty($options['order'])) $args['order'] = $options['order'];
        if (!empty($options['limit'])) $args['limit'] = $options['limit'];
        
        switch ($options['type']) {
            case 'belongs_to':
            case 'has_one':
                $obj->find('first', $args);
                break;
                
            case 'has_many':
                $obj->find('all', $args);
                break;
        }
        
        // create association property
        $this->{$options['association_id']} = $obj;
        
        return $this->{$options['association_id']};
    }
    
    /**
     * Build an array of foreign keys and values from keys passed.
     *
     * @param array/string $foreign_key
     * @return array
     **/
    final public function foreign_keys_and_values($foreign_key)
    {
        if (is_array($foreign_key)) {
            $vals = array();
            foreach ($foreign_key as $key) {
                $vals[$key] = $this->{$key};
            }
        } else {
            $vals = array($foreign_key => $this->{$foreign_key});
        }
        return $vals;
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
    	$attibutes = $this->select_query()->current();
    	if ($attibutes) {
	        // set attributes with current row
	        $this->set_attributes_from_query($attibutes);
        } else {
        	$this->reset_values();
        }
        // initialize class vars foreach record
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
     * Transaction methods.
     */
    
    /**
     * START transaction.
     *
     * @return void
     **/
    public function start_tran()
    {
        return $this->action_query()->start_tran();
    }
    
    /**
     * ROLLBACK transaction.
     *
     * @return void
     **/
    public function rollback()
    {
        return $this->action_query()->rollback();
    }
    
    /**
     * COMMIT transaction.
     *
     * @return void
     **/
    public function commit()
    {
        return $this->action_query()->commit();
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
     * Pass through function used to add CData functionality to
     * value if prototype is enabled.
     *
     * @param mixed $value
     * @return mixed
     **/
    public function return_value($value)
    {
        if ($this->prototype(1)) {
            return new CData($value);
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
    public function after_update() {}
    public function before_update() {}
    public function validate() {}
    public function validate_on_create() {}
    public function validate_on_update() {}
    /**#@-*/
} // END class ActiveRecord extends CObject

