<?php
/**
 * Query class layer for ActiveRecord.
 *
 * @package     Creovel
 * @subpackage  Core
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.x
 * @author      Nesbert Hidalgo
 **/
class ActiveQuery extends CObject implements Iterator
{
    
    /**#@+
     * Class member.
     *
     * @access private
     */
    private $__db;
    /**#@-*/
    
    /**
     * Pass an associative array of database settings to connect
     * to database on construction of class.
     *
     * @return void
     **/
    public function __construct($connection_properties = null)
    {
        if (!empty($connection_properties)) {
            $this->__db = new ActiveDatabase($connection_properties);
        }
    }
    
    /**
     * Pass through method to access the ActiveDatabase object. Checks for
     * db connection if not present will a new establish connection.
     *
     * @param string $table
     * @return object ActiveDatabase;
     **/
    public function db()
    {
        if (empty($this->__db)) {
            $this->__db = new ActiveDatabase;
            $this->__db->connect();
        }
        return $this->__db;
    }
    
    /**
     * Pass through function to adapter.
     *
     * @return void
     **/
    public function connect()
    {
        return $this->db();
    }
    
    /**
     * Pass through function to adapter.
     *
     * @return void
     **/
    public function disconnect()
    {
        return $this->db()->disconnect();
    }
    
    /**
     * Pass through function to adapter.
     *
     * @return void
     **/
    public function columns($table)
    {
        return $this->db()->columns($table);
    }
    
    /**
     * Alias to query function.
     *
     * @return void
     **/
    public function execute($sql)
    {
        return $this->query($sql);
    }
    
    /**
     * Pass through function to adapter.
     *
     * @return void
     **/
    public function query($sql)
    {
        return $this->db()->adapter()->query($sql);
    }
    
    /**
     * Pass through function to adapter.
     *
     * @return void
     **/
    public function escape($string)
    {
        return $this->db()->adapter()->escape($string);
    }
    
    /**
     * Escape using current adapter and single quote $string.
     *
     * @param string $string Prep for SQL query
     * @return string
     **/
    final public function quote_value($string)
    {
        return "'" . $this->escape($string) . "'";
    }
    
    
    /**
     * Pass through function to adapter.
     *
     * @return void
     **/
    public function total_rows()
    {
        return $this->db()->adapter()->total_rows();
    }
    
    /**
     * Pass through function to adapter.
     *
     * @return void
     **/
    public function get_row()
    {
        return $this->db()->adapter()->get_row();
    }
    
    /**
     * Iterator methods.
     */
    
    /**
     * Pass through function to adapter.
     *
     * @return void
     **/
    public function rewind()
    {
        return $this->db()->adapter()->rewind();
    }
    
    /**
     * Pass through function to adapter.
     * 
     * @return array
     * @see function get_row
     **/
    public function current()
    {
        return $this->db()->adapter()->current();
    }
    
    /**
     * Pass through function to adapter.
     *
     * @return integer
     **/
    public function key()
    {
        return $this->db()->adapter()->key();
    }
    
    /**
     * Pass through function to adapter.
     * 
     * @return array
     * @see function current
     **/
    public function next()
    {
        return $this->db()->adapter()->next();
    }
    
    /**
     * Pass through function to adapter.
     *
     * @return array
     * @see function current
     **/
    public function prev()
    {
        return $this->db()->adapter()->prev();
    }
    
    /**
     * Pass through function to adapter.
     *
     * @return array
     * @see function current
     **/
    public function valid()
    {
        return $this->db()->adapter()->valid();
    }
    
    /**
     * Pass through function to adapter.
     *
     * @return void
     **/
    public function reset()
    {
        return $this->db()->adapter()->reset();
    }
    
    
    /**
     * Transaction methods.
     */
    
    /**
     * Pass through function to adapter.
     *
     * @return void
     **/
    public function start_tran()
    {
        return $this->db()->adapter()->start_tran();
    }
    
    /**
     * Pass through function to adapter.
     *
     * @return void
     **/
    public function rollback()
    {
        return $this->db()->adapter()->rollback();
    }
    
    /**
     * Pass through function to adapter.
     *
     * @return void
     **/
    public function commit()
    {
        return $this->db()->adapter()->commit();
    }
    
    /////////
    
    /**
     * Build query string from an $options array.
     *
     * @param array $options
     * @return string
     **/
    final public function build_query($options)
    {
        $q = new CObject;
        $q->select = null;
        $q->from = null;
        $q->join = null;
        $q->where = array();
        $q->limit = null;
        $q->offset = null;
        $q->group = null;
        $q->order = null;
        
        if (empty($options['table'])) {
            throw new Exception('Missing argument <em>table</em> not set' .
            " in options array for <strong>ActiveQuery::build_query</strong>.");
        }        
        
        $regex = '/^[A-Za-z0-9_,.`\s\-\(\)]+$/';
        
        // set vaiables used to build query
        if (isset($options['select'])) {
            $q->select = $options['select'];
        } else {
            $q->select = '*';
        }
        
        if (isset($options['from'])) {
            $q->from = $options['from'];
        } else {
            $q->from = $this->build_table_name($options['table']);
        }
        
        if (isset($options['where'])) {
            $options['conditions'] = $options['where'];
        } else if (isset($options['conditions'])) {
            $options['conditions'] = $options['conditions'];
        } else {
            $options['conditions'] = '';
        }
        
        if (isset($options['join'])) {
            $q->join = $options['join'];
        }
        
        if (isset($options['order'])
            && preg_match($regex, $options['order'])) {
            $q->order = $options['order'];
        }
        
        if (isset($options['offset']) && is_numeric($options['offset'])) {
            $q->offset = $options['offset'];
        }
        
        if (isset($options['limit']) && is_numeric($options['limit'])) {
            $q->limit = $options['limit'];
        }
        
        if (isset($options['group'])
            && preg_match($regex, $options['group'])) {
            $q->group = $options['group'];
        }
        
        // Prepare conditions array.
        if ($options['conditions']) {
            $q->where[] = $this->build_query_from_conditions($options['table'], $options['conditions']);
        }
        
        // create sql query
        if (!empty($options['update'])) {
            $sql = "UPDATE {$q->from}";
        } else {
            $sql = "SELECT {$q->select} FROM {$q->from}";
        }
        
        if ($q->join) $sql .= " " . $this->build_query_from_conditions($options['table'], $q->join, false);
        
        if (!empty($options['update'])) {
            $set = array();
            foreach ($options['update'] as $k => $v) {
                $set[] = $this->build_identifier(array($this->__db->__schema, $options['table'], $k)) . " = {$this->quote_value($v)}"; 
            }
            $sql .= ' SET ' . implode(', ', $set);
        }
        
        if (!empty($options['where_append'])) {
            $q->where[] = $options['where_append'];
        }
        
        // pre WHERE DB2 stuff
        if ($this->get_adapter_type() == 'ibmdb2') {
            if (!empty($q->offset)) {
                $q->where[] = sprintf("ROWNUM BETWEEN %d AND %d", $q->offset+1, $q->offset + $q->limit);
                unset($q->offset);
            } else if (!empty($q->limit)) {
                $q->where[] = sprintf("ROWNUM BETWEEN %d AND %d", 1, $q->limit);
            }
        } 
        
        if (count($q->where)) {
            $sql .= " WHERE " . implode(' AND ', $q->where);
        }
        if ($q->group) $sql .= " GROUP BY {$q->group}";
        if ($q->order) $sql .= " ORDER BY {$q->order}";
        
        if ($q->limit) {
            switch ($this->get_adapter_type()) {
                case 'ibmdb2':
                    $sql .= sprintf(" OPTIMIZE FOR %d ROWS", $q->limit);
                    break;
                default:
                    $sql .= " LIMIT {$q->limit}";
                    break;
            }
        }
        
        if ($q->offset) $sql .= " OFFSET {$q->offset}";
        
        return $sql . ';';
    }
    
    /**
     * Build full table name with adapter specific identifier
     * quote character.
     *
     * @return string
     **/
    public function build_table_name($table)
    {
        return $this->build_identifier(
                    array($this->db()->__schema, $table),
                    $this->get_identifier_quote_character());
    }
    
    /**
     * Get adapter specific identifier quote character.
     *
     * @return string
     **/
    public function get_identifier_quote_character()
    {
        switch ($this->get_adapter_type()) {
            case 'ibmdb2':
                return '"';
            default:
                return '`';
        }
    }
    
    /**
     * Build a SQL safe identifier string.
     *
     * @return string
     **/
    public function build_identifier($nodes,  $identifier_quote_character = null, $seperator = '.')
    {
    	if (empty($identifier_quote_character)) {
    		$identifier_quote_character = $this->get_identifier_quote_character();
    	}
    	
        if (is_array($nodes)) {
            foreach ($nodes as $k => $node) {
                $nodes[$k] = $identifier_quote_character . $node .
                            $identifier_quote_character;
            }
            return implode($seperator, $nodes);
        }
        
        return false;
    }
    
    /**
     * Builds where SQL string by the conditions array passed. $isolate query
     * with parentheses ($string).
     *
     * @param mixed $conditions
     * @param boolean $isolate
     * @return void
     **/
    final public function build_query_from_conditions($table, $conditions, $isolate = true)
    {
        $sql = '';
        
        // 1. hash or object condidtions
        if (CValidate::hash($conditions) || is_object($conditions)) {
            $cs = array();
            foreach ($conditions as $k => $v) {
                $cs[] = $this->build_identifier(array($this->__db->__schema, $table, $k)) . " = {$this->quote_value($v)}";
            }
            if ($isolate) {
                $sql = '(' . implode(' AND ', $cs) . ')';
            } else {
                $sql = implode(' AND ', $cs);
            }
            
        // 2. array condidtions
        } elseif (is_array($conditions) && CValidate::in_string('?', $conditions[0])) {
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
        } elseif (is_array($conditions) && CValidate::in_string(':', $conditions[0])) {
            $str = $conditions[0];
            if (is_array($conditions[1])) foreach ($conditions[1] as $k => $v) {
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
     * Build a string for using with WHERE statement.
     *
     * @param mixed $conditions
     * @param boolean $isolate
     * @return string
     **/
    final function build_where($table, $conditions = null, $isolate = true)
    {
        return $this->build_query_from_conditions($table, $conditions, $isolate);
    }
    
    /**
     * Find row into a table by column properties.
     *
     * @param string $table
     * @param mixed $conditions
     * @return object Row result
     **/
    public function find($table, $conditions, $type = 'all')
    {
        // set table from params
        $conditions = (array) $conditions;
        $conditions['table'] = $table;
        
        // find record
        $this->query($this->build_query($conditions));
        
        if ($type == 'all') {
            return clone $this;
        } else {
            return $this->current();
        }
    }
    
    /**
     * Find row into a table by column properties.
     *
     * @param string $table
     * @param mixed $conditions
     * @return object Row result
     **/
    public function find_row($table, $conditions)
    {
        return $this->find($table, $conditions, 'first');
    }
    
    /**
     * Insert a row into a table by columns properties.
     *
     * @param string $table
     * @param array $columns ActiveRecordField objects
     * @return integer Current result ID.
     **/
    final public function insert_row($table, $columns)
    {
        // Make sure columns are the coorect object
        $this->convert_columns($columns);
        
        // set created at
        if (isset($columns['created_at'])) {
            $columns['created_at']->has_changed = true;
            $columns['created_at']->value = CDate::datetime($columns['created_at']);
        }
        
        // do any special magic for certain columns
        $this->prepare_columns($columns);
        
        $table_str = $this->build_table_name($table);
        $columns_str = array();
        $values_str = array();
        
        // sanitize values
        foreach ($columns as $k => $field) {
            if (empty($field->value)) continue;
            // if same as default value skip
            if ($field->value === $columns[$k]->default) continue;
            // if value has not changed 
            if (!$columns[$k]->has_changed) continue;
            $columns_str[] = $this->build_identifier(array($this->db()->__schema, $table, $k));
            $values_str[] = $field->value == 'NULL' ? 'NULL' : $this->quote_value($field->value);
        }
        
        $columns_str = implode(',', $columns_str);
        $values_str = implode(',', $values_str);
        
        // build query
        $qry =  "INSERT INTO {$table_str} " .
                "({$columns_str}) " .
                "VALUES " .
                "({$values_str});";
        
        // insert record
        $this->query($qry);
        
        return $this->affected_rows();
    }
    
    /**
     * Update database with the current model's values.
     *
     * @param string $table
     * @param array $colmns ActiveRecordField objects
     * @param mixed $conditions
     * @return integer Current result ID.
     **/
    final public function update_row($table, $columns, $conditions)
    {
        // Make sure columns are the coorect object
        $this->convert_columns($columns);
        
        // set updated at
        if (isset($columns['updated_at'])) {
            $columns['updated_at']->has_changed = true;
            $columns['updated_at']->value = CDate::datetime();
        }
        
        // do any special magic for certain columns
        $this->prepare_columns($columns);
        
        $table_str = $this->build_table_name($table);
        $set_str = array();
        $where_str = $this->build_where($table, $conditions, false);
        
        // sanitize values
        foreach ($columns as $k => $field) {
            // if same as default value skip
            if ($field->value === $columns[$k]->default) continue;
            // if value has not changed 
            if (!$columns[$k]->has_changed) continue;
            $set_str[$k] = $this->build_identifier(array($this->db()->__schema, $table, $k));
            $set_str[$k] .= ' = ';
            $set_str[$k] .= $field->value == 'NULL' ? 'NULL' : $this->quote_value($field->value);
        }
        
        if (count($set_str)) {
            $set_str = implode(',', $set_str);
        } else {
            $set_str = str_replace('AND', ',', $where_str);
        }
        
        // build query
        $qry =  "UPDATE {$table_str} " .
                "SET {$set_str} " .
                "WHERE {$where_str};";
        
        // insert record
        $this->query($qry);
        
        return $this->affected_rows();
    }
    
    /**
     * Prepares the current columns and values for SQL.
     *
     * @param array $columns ActiveRecordField objects
     * @return array
     **/
    public function prepare_columns(&$columns)
    {
        foreach ($columns as $name => $field) {
            if (empty($field->value)) continue;
            switch (true) {
                // set array types
                case is_array($field->value):
                    if (strtolower($field->type) == 'datetime') {
                        $columns[$name]->value = CDate::datetime($field->value);
                    } else {
                        $columns[$name]->value = serialize($field->value);
                    }
                    break;
                // set NULL values blank
                case empty($field->value) && $field->null == 'YES':
                    $columns[$name]->value = '';
                    break;
            }
        }
        
        return $columns;
    }
    
    /**
     * Convert an array key value pairs into ActiveRecordField objects.
     *
     * @param array $columns
     * @return array
     **/
    public function convert_columns(&$columns)
    {
        foreach ($columns as $col => $data) {
            if (is_object($data)
                && get_class($data) == 'ActiveRecordField') {
                break;
            } else {
                $tmp = new ActiveRecordField;
                $tmp->has_changed = true;
                $tmp->value = $data;
                $columns[$col] = $tmp;
            }
        }
        return $columns;
    }
    
    /**
     * Pass through function to adapter.
     *
     * @return integer
     **/
    public function insert_id()
    {
        return $this->db()->adapter()->insert_id();
    }
    
    /**
     * Pass through function to adapter.
     *
     * @return integer
     **/
    public function affected_rows()
    {
        return $this->db()->adapter()->affected_rows();
    }
    
    /**
     * Execute DELETE command and return number affected rows.
     *
     *
     * @param string $table
     * @param mixed $conditions
     * @return integer
     **/
    final public function delete($table, $conditions = null)
    {
        $table_str = $this->build_table_name($table);
        
        // build delete statement
        $q = "DELETE FROM {$table_str}";
        
        if (!empty($conditions)) {
            $where_str = $this->build_where($table, $conditions, false);
            $q .= " WHERE {$where_str}";
        }
        
        $q .=";";
        
        $this->query($q);
        
        return $this->affected_rows();
    }
    
    /**
     * Buid a lowercased string of the current DB adapter.
     * 
     * @return string
     **/
    public function get_adapter_type()
    {
        return strtolower($this->db()->__adapter);
    }
    
} // END class ActiveQuery extends CObject implements Iterator
