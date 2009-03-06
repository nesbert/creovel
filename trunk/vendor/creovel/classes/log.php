<?php
/**
 * Logging class.
 *
 * @package     Creovel
 * @subpackage  Core
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.2
 * @author      Nesbert Hidalgo
 **/
class Log extends Object
{
    /**
     * Filename and path to log file.
     *
     * @var string
     **/
    public $filename = '';
    
    /**
     * Flag to include time stamp in logs.
     *
     * @var integer
     **/
    public $filesize_limit = 104857600;
    
    /**
     * Flag to include time stamp in logs.
     *
     * @var boolean
     **/
    public $timestamp = true;
    
    /**
     * Set $filename.
     *
     * @param string $filename
     * @return void
     **/
    public function __construct($filename = '')
    {
        $this->filename = $filename;
    }
    
    /**
     * Write a message to file.
     *
     * @param string $message
     * @param boolean $auto_break
     * @return void
     **/
    public function write($message, $auto_break = true)
    {
        clearstatcache();
        
        if (@filesize($this->filename) >= $this->filesize_limit) {
            $this->partition($this->filename);
        }
        
        $message = ($this->timestamp ? '[' . datetime() . '] ' : '') .
                    $message . ($auto_break ? "\n" : '');
                    
        if (!@file_put_contents($this->filename, $message, FILE_APPEND)) {
            error_log("Creovel The file {$this->filename} is not writable!");
        }
    }
    
    /**
     * Partitions current log by renaming the current file and date stamps it.
     *
     * @return boolean
     **/
    public function partition()
    {
        return rename($this->filename, $this->filename . '.' . date('YmdHis'));
    }
}