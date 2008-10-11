<?php
/**
 * undocumented class
 *
 * @package Creovel
 * @subpackage Creovel.Classes
 **/
class Logger
{
	public static $filesize_limit = 167772160;
	
	public static $display = false;
	
	public static $timestamp = true;
	
	public static function write($filename, $message, $auto_break = false)
	{
		clearstatcache();
		
		if (@filesize($filename) > self::$filesize_limit) {
			self::paginate($filename);
		}
		
		if (!file_exists($filename)) {
			if (self::$display) echo "Creating new file ({$filename})!\n";
			touch($filename);
			chmod($filename, 0755);
		}
		
		if (is_writable($filename)) {
			if (!$handle = fopen($filename, 'a')) {
				if (self::$display) echo "Cannot open file ({$filename})!\n";
				exit;
			}
			
			if (self::$timestamp) $time = datetime().' ';
			
			if (fwrite($handle, $time.$message.($auto_break ? "\n" : '')) === FALSE) {
				if (self::$display) echo "Cannot write to file ({$filename})!\n";
				exit;
			}
			
			if (self::$display) echo $time." Success, wrote to file ($filename).\n";
			
			fclose($handle);
			
		} else {
			if (self::$display) echo "The file $filename is not writable!\n";
		}
	}
	
	public static function paginate($filename)
	{
		if (self::$display) echo "Paging file $filename!\n";
		rename($filename, $filename.'.'.date('YmdHis'));
	}
} // END class Logger