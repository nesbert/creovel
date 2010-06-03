<?php
/**
 * Fields class layer for ActiveRecord.
 *
 * @package     Creovel
 * @subpackage  Core
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.x
 * @author      Nesbert Hidalgo
 **/
class ActiveRecordField extends CreovelObject
{
    /**
     * Field type (CHAR, VARCHAR, TEXT, etc).
     *
     * @var string
     **/
    public $type = '';
    
    /**
     * Field size.
     *
     * @var string
     **/
    public $size = '';
    
    /**
     * Field allowed to be null (YES, NO).
     *
     * @var string
     **/
    public $null = 'NO';
    
    /**
     * Field has changed flag.
     * 
     * @var boolean
     **/
    public $has_changed = false;
    
    /**
     * Field is identity flag.
     *
     * @var string
     **/
    public $is_identity = false;
    
    /**
     * Field key (PK, FR, blank).
     *
     * @var string
     **/
    public $key = '';
    
    /**
     * Field key name.
     *
     * @var string
     **/
    public $key_name = '';
    
    /**
     * Field default value.
     *
     * @var mixed
     **/
    public $default;
    
    /**
     * Field value.
     *
     * @var mixed
     **/
    public $value;
        
    /**
     * Pass a object of database table column attributes and map them to a
     * common structure used be active record.
     *
     * @return void
     **/
    public function __construct($attributes = stdClass)
    {
        switch ($attributes->adapter) {
            
            case 'ibmdb2':
                $this->type = $attributes->TYPE_NAME;
                if ($attributes->DATA_TYPE == 3) {
                    $this->size = "{$attributes->NUM_PREC_RADIX},{$attributes->DECIMAL_DIGITS}";
                } else if ($attributes->DATA_TYPE == 1) {
                    $this->size = "{$attributes->NUM_PREC_RADIX},{$attributes->DECIMAL_DIGITS}";
                } else {
                    unset($this->size);
                }
                if (!empty($attributes->key) && ($attributes->key == 'PRI' || $attributes->key == 'PK')) {
                    $this->key = 'PK';
                    $this->key_name = $attributes->key_name;
                } else {
                    unset($this->key);
                    unset($this->key_name);
                }
                $this->null = $attributes->IS_NULLABLE;
                $this->default = $attributes->COLUMN_DEF;
                if ($this->null == 'NO' && empty($this->default)) {
                    $this->default = '';
                }
                break;
            
            // mysql & mysqli adpater field routine
            default:
                if (in_string('(', $attributes->type)) {
                    $this->type = strtoupper(preg_replace('/(\w+)\((.*)\)/i', '${1}', $attributes->type));
                    $this->size = preg_replace('/(\w+)\((.*)\)/i', '${2}', $attributes->type);
                } else {
                    $this->type = strtoupper($attributes->type);
                    unset($this->size);
                }
                
                if ($attributes->key == 'PK' ||
                    $attributes->key == 'PRI') {
                    $this->key = 'PK';
                    $this->key_name = 'PRIMARY';
                } else {
                    unset($this->key);
                    unset($this->key_name);
                }
                
                if ($attributes->extra == 'auto_increment') {
                    $this->is_identity = true;
                } else {
                    unset($this->is_identity);
                }
                
                $this->null = $attributes->null;
                $this->default = $attributes->default;
                break;
        }
        
        $this->value = $this->default;
    }
    
    /**
     * Return a loaded object of ActiveRecordField.
     *
     * @param object $attributes
     * @return object ActiveRecordField
     **/
    public function object($attributes)
    {
        return new ActiveRecordField($attributes);
    }
} // END class ActiveRecordField extends CreovelObject