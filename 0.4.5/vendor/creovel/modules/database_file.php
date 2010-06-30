<?php
/**
 * Database to File class used for creating and reading mapping files.
 * Reducing the number of connections/requests to a database in order to
 * model a tables columns and attributes.
 *
 * @package     Creovel
 * @subpackage  Modules
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.2
 * @author      Nesbert Hidalgo
 **/
class DatabaseFile extends ModuleBase
{
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
    public function __construct($file = null)
    {
        parent::__construct();
        if ($file) $this->load($file);
    }
    
    /**
     * Load XML file.
     *
     * @param string $file
     * @return void
     **/
    public function load($file)
    {
        if (!defined('SCHEMAS_PATH')) {
            $this->throw_error("SCHEMAS_PATH not defined.");
        }
        
        if ($file) {
            $this->_schema_file_ = $file;
        } else {
            $this->throw_error("Missing argument $file for DatabaseFile::load.");
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
     * @return string
     **/
    public function create($options)
    {
        if (!defined('SCHEMAS_PATH')) {
            self::throw_error("SCHEMAS_PATH not defined.");
        }
        
        //  set class name
        $class_name = empty($options['class_name']) ? '' : $options['class_name'];
        
        if (empty($class_name)) {
            self::throw_error("Model name needed for <strong>DatabaseFile</strong>.");
        }
        
        // load class without initializing
        $obj = new $class_name(null, null, false);
        
        $cols = $obj->columns(true);
        
        // if table not set use model
        $options['table_name'] = $obj->table_name();
        
        //  set file path
        $file = empty($options['file']) ? '' : $options['file'];
        if (!$file) {
            // use default path dir for schemas
            $file = self::default_file(
                            $obj->schema_name(),
                            $options['table_name']
                            );
        }
        
        // create DOM
        if (!class_exists('DomDocument')) {
            self::throw_error("DomDocument module needed for <strong>DatabaseFile</strong>.");
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
        $table->setAttribute('database', $obj->database_name());
        $table->setAttribute('schema', $obj->schema_name());
        
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
            $column->setAttribute('type', $col->type);
            
            // set size
            if (!empty($col->size))
                $column->setAttribute('size', $col->size);
            
            // set default
            if (!empty($col->default))
                $column->setAttribute('default', $col->default);
            
            // set auto_increment
            if ($col->null == 'YES' || $col->null) {
                $column->setAttribute('null', 'YES');
            }
            
            if (!empty($col->key)) {
                $column->setAttribute('key', $col->key);
                $column->setAttribute('key_name', $col->key_name);
            }
            
            if (!empty($col->is_identity)) {
                $column->setAttribute('identity', 'YES');
            }
            
            // if (preg_match('/(unsigned)/i', $col->type)) {
            //     $column->setAttribute('unsigned', 'yes');
            // }
            
            // check for index
            if (!empty($col->key)) {
                switch ($col->key) {
                    case 'PK':
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
            self::throw_error("Unable to load columns from table ".
            "<em>{$options['table_name']}</em> for <strong>{$class_name}</strong>.");
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
                        $index->setAttribute('type', strtoupper($type));
                        break;
                    
                    default:
                        $index->setAttribute('name', strtoupper('index_' . $type));
                        $index->setAttribute('type', 'INDEX');
                        break;
                    
                }
            
                $index->setAttribute('column', implode(',', $idx));
            }
        }
        
        $xml = $doc->saveXML();
        
        if (!file_put_contents($file, $xml)) {
            self::throw_error("Unable to create <em>{$file}</em> for ".
            "<strong>{$class_name}</strong>.");
        } else {
            return $file;
        }
    }
    
    /**
     * Default file name.
     *
     * @return string
     **/
    public function default_file($schema, $table)
    {
        return SCHEMAS_PATH . Inflector::underscore(
                $schema . '_' . $table . '.xml', '.');
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
                
                $attr = array();
                foreach ($col->attributes() as $k => $v) {
                    $attr[(string) $k] = (string) $v;
                }
                
                $this->_columns_[$attr['name']] =
                    new ActiveRecordField($attr);
            }
            return $this->_columns_;            
        } else {
            $this->throw_error("Unable to load <em>//model/table/columns</em> from <strong>{$this->_schema_file_}</strong>");
        }
    }
    
} // END class DatabaseFile extends ModuleBase