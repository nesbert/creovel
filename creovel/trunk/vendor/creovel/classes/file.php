<?php
/*
 * File managing class.
 */

class file
{

	private $filename;
	private $save_dir;
	private $temp_dir;

	private $max_size;
	
	public function extension($path)
	{
		return pathinfo($path, PATHINFO_EXTENSION);
	}
	
	public function type($file)
	{
		return get_mime_type($file);
	}
	
	public function size($file_path)
	{
		return get_filesize($file_path);
	}

	public function info($filename)
	{
		$return = pathinfo($filename);
		$return['type'] = self::type($filename);
		$return['size'] = self::size($filename);
		return $return;
	}

	public function copy($filename, $destination)
	{
		return @copy($filename, $destination);
	}

	public function move($filename, $destination)
	{
		return @rename($filename, $destination);
	}

	public function delete($filename)
	{
		return @unlink($filename);
	}

}
?>