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
     * Class name.
     *
     * @var string
     **/
    private $_model_ = '';
    
    
    /**
     * Table columns array.
     *
     * @var array
     **/
    private $_columns_ = array();
    
    /**
     * Schema file path.
     *
     * @var string
     **/
    private $_schema_file_ = '';
    
    /**
     * Class construct.
     *
     * @param string $model ActiveRecord object
     * @return string $file
     * @return void
     **/
    public function __construct($model = null, $file = null)
    {
        $this->_model_ = $model;
        if ($file) $this->load_file($file);
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
            $this->_schema_file_ = SCHEMAS_PATH . CString::underscore($this->_model_) . '.xml';
        }
        
        if (file_exists($this->_schema_file_)) {
            if (!class_exists('SimpleXMLElement')) {
                $this->throw_error("SimpleXMLElement module needed for <strong>DatabaseXML</strong>");
            }
            $this->xml = simplexml_load_file($this->_schema_file_);
        } else {
            $this->throw_error("Schema not found in <strong>{$this->_schema_file_}</strong>");
        }
    }
    
    /**
     * Create XML file.
     *
     * //model/table
     * //model/table/columns
     * //model/table/columns/column
     * //model/table/indexes
     * //model/table/indexes/index
     *
     * @param array $options
     * @return void
     **/
    public function create_file($options)
    {
        //  set class name
        $class_name = empty($options['class_name']) ? '' : $options['class_name'];
        
        if (empty($class_name)) {
            self::throw_error("Model name needed for <strong>DatabaseXML</strong>.");
        }
        
        // load class without initializing
        $obj = new $class_name(null, null, false);
        $cols = $obj->columns(true);
        
        // set db settings
        $settings = $obj->connection_properties();
        
        // if table not set use model
        $options['table_name'] = $obj->table_name();
        
        //  set file path
        $file = empty($options['file']) ? '' : $options['file'];
        if (!$file) {
            // use default path dir for schemas
            $file = SCHEMAS_PATH . CString::underscore($class_name) . '.xml';
        }
        
        // create DOM
        if (!class_exists('DomDocument')) {
            self::throw_error("DomDocument module needed for <strong>DatabaseXML</strong>.");
        }
        $doc = new DomDocument('1.0', 'utf-8');
        $doc->formatOutput = true;
        
        // root element
        $model = $doc->createElement('model');
        $model = $doc->appendChild($model);
        
        // root child element
        $table = $doc->createElement('table');
        $table = $model->appendChild($table);
        $table->setAttribute('name', $options['table_name']);
        $table->setAttribute('db', $settings['default']);
        
        // table child element
        $columns = $doc->createElement('columns');
        $columns = $table->appendChild($columns);
        
        // index holder
        $idxs = array();
        
        if (count($cols)) foreach ($cols as $name => $col) {
            
            // columns child element
            $column = $doc->createElement('column');
            $column = $columns->appendChild($column);
            
            // set name
            $column->setAttribute('name', $name);
            
            // set type
            $remove_from_type = array('unsigned');
            preg_match('/\((.*)\)/', $col->type, $matches); // find size values
            if (isset($matches[0])) {
                $remove_from_type[] = $matches[0];
            }
            $type = trim(str_replace($remove_from_type, '', $col->type));
            $column->setAttribute('type', $type);
            
            // set size
            if (isset($matches[1])) {
                $column->setAttribute('size', $matches[1]);
            }
            
            // set default
            if ($col->default) $column->setAttribute('default', $col->default);
            
            // set auto_increment
            if (preg_match('/(YES|1)/i', $col->null)) {
                $column->setAttribute('null', 'yes');
            }
            
            if (preg_match('/(auto_increment)/i', $col->extra)) {
                $column->setAttribute('auto_increment', 'yes');
            }
            
            if (preg_match('/(unsigned)/i', $col->type)) {
                $column->setAttribute('unsigned', 'yes');
            }
            
            // check for index
            if (!empty($col->key)) {
                switch ($col->key) {
                    case 'PRI':
                        $idxs['primary'][] = $name;
                        break;
                        
                    case 'UNI':
                        $idxs['unique'][] = $name;
                        break;
                        
                    default:
                        $idxs[$name][] = $name;
                        break;
                }
            }
        } else {
            self::throw_error("Unable to load columns from table <em>{$options['table_name']}</em> for <strong>DatabaseXML</strong>.");
        }
        
        // check indexes
        if ($idxs) {
            
            // sort
            ksort($idxs);
            
            // table child element
            $indexes = $doc->createElement('indexes');
            $indexes = $table->appendChild($indexes);
            
            foreach ($idxs as $type => $idx) {
            
                $index = $doc->createElement('index');
                $index = $indexes->appendChild($index);
                
                switch ($type) {
                    case 'primary':
                    case 'unique':
                        $index->setAttribute('name', strtoupper($type));
                        $index->setAttribute('type', $type);
                        break;
                    
                    default:
                        $index->setAttribute('name', 'index_' . $type);
                        $index->setAttribute('type', 'index');
                        break;
                    
                }
            
                $index->setAttribute('column', implode(',', $idx));
            }
        }
        
        $xml = $doc->saveXML();
        
        if (!@file_put_contents($file, $xml)) {
            self::throw_error("Unable to create <em>{$file}</em> for <strong>DatabaseXML</strong>.");
        } else {
            return true;
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
                        'value'     => $this->column_default($col->attributes())
                        );
                
            }
            return $this->_columns_;            
        } else {
            $this->throw_error("Unable to load <em>//model/table/columns</em> from <strong>{$this->_schema_file_}</strong>");
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
        return (string) $attrs->type . (empty($attrs->size) ? '' : '(' . (string) $attrs->size . ')');
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