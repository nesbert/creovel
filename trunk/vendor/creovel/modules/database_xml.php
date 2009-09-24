<?php
/**
 * Database to XML class used for creating and reading xml files.
 * variety of block algorithms.
 *
 * @package     Creovel
 * @subpackage  Modules
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.2
 * @author      Nesbert Hidalgo
 **/
class DatabaseXML extends ModuleBase
{
    /**
     * Table name.
     *
     * @var string
     **/
    private $_table_name_ = '';
    
    
    /**
     * Table name.
     *
     * @var string
     **/
    private $_columns_ = '';
    
    /**
     * Table name.
     *
     * @var string
     **/
    private $_schema_file_ = '';
    
    /**
     * undocumented function
     *
     * @return void
     **/
    public function __construct($table_name = null, $file = null)
    {
        $this->set_table($table_name);
        $this->load_file($file);
    }
    
    /**
     * Set table name.
     *
     * @param string $table_name
     * @return void
     **/
    public function set_table($table_name)
    {
        $this->_table_name_ = $table_name;
    }
    
    /**
     * Load XML file.
     *
     * @param string $file
     * @return void
     **/
    public function load_file($file = null)
    {
        if ($file) {
            $this->_schema_file_ = $file;
        } else {
            $file = Inflector::underscore($this->_table_name_);
            $file = Inflector::singularize($this->_table_name_);
            $this->_schema_file_ = SCHEMAS_PATH . strtolower($file) . '.xml';
        }
        
        if (file_exists($this->_schema_file_)) {
            $this->xml = simplexml_load_file($this->_schema_file_);
        } else {
            $this->throw_error("Schema not found in <strong>{$this->_schema_file_}</strong>");
        }
    }
    
    /**
     * Get/build columns array.
     *
     * @return void
     **/
    public function columns()
    {
        // if already set return
        if (!empty($this->_columns_)) return $this->_columns_;
        
        if (!empty($this->xml->table->columns)) {
            $this->_columns_ = array();
            foreach ($this->xml->table->columns->column as $col) {
                $this->_columns_[(string) $col->attributes()->name] = (object) array(
                        'type'      => $this->column_type($col->attributes()),
                        'null'      => $this->column_null($col->attributes()),
                        'key'       => $this->column_key($col->attributes()),
                        'default'   => $this->column_default($col->attributes()),
                        'extra'     => $this->column_extra($col->attributes()),
                        'value'     => null
                        );
                
            }
            return $this->_columns_;            
        } else {
            $this->throw_error("Unable to load <em>table/columns</em> from <strong>{$this->_schema_file_}</strong>");
        }
    }
    
    /**
     * Get/build indexes array.
     *
     * @return void
     **/
    public function indexes()
    {
        // if already set return
        if (!empty($this->_indexes_)) return $this->_indexes_;
        
        if (!empty($this->xml->table->indexes)) {
            $this->_indexes_ = array();
            foreach ($this->xml->table->indexes->index as $idx) {
                $type = strtolower((string) $idx->attributes()->type);
                $name = strtolower((string) $idx->attributes()->name);
                switch ($type) {
                    case 'primary':
                        if ($name) {
                            $this->_indexes_[$type]->{$name}->keys = explode(',', (string) $idx->attributes()->column);
                        }
                    break;
                }
            }
            return $this->_indexes_;
        }
    }
    
    /**
     * Get column type.
     *
     * @return string
     **/
    public function column_type(SimpleXMLElement $attrs)
    {
        return (string) $attrs->type . '(' . (string) $attrs->size . ')';
    }
    
    /**
     * Get if column null.
     *
     * @return string
     **/
    public function column_null(SimpleXMLElement $attrs)
    {
        switch (strtoupper((string) $attrs->null)) {
            case 'YES':
            case '1':
                return 'YES';
            
            default:
                return 'NO';
        }
    }
    
    /**
     * Get if column key.
     *
     * @return string
     **/
    public function column_key(SimpleXMLElement $attrs)
    {
        $key = (string) $attrs->name;
        foreach ($this->indexes() as $type => $index) {
            foreach ($index as $idx) {
                if (in_array($key, $idx->keys)) {
                    return substr(strtoupper($type), 0, 3);
                }
            }
        }
    }
    
    /**
     * Get column default value.
     *
     * @return string
     **/
    public function column_default(SimpleXMLElement $attrs)
    {
        $default = (string) $attrs->default;
        switch (strtoupper($default)) {
            case 'NULL':
            case '':
                return null;
            
            default:
                return $default;
        }
    }
    
    /**
     * Get column extra value.
     *
     * @return string
     **/
    public function column_extra(SimpleXMLElement $attrs)
    {
        $extra = array();
        
        // check auto increment
        switch (strtoupper((string) $attrs->auto_increment)) {
            case 'YES':
            case '1':
                $extra[] = 'auto_increment';
        }
        
        return implode(' ', $extra);
    }
} // END class DatabaseXML extends ModuleBase