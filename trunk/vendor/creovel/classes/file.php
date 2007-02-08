<?php
/*

Class: file
	File managing class.

*/

class file
{
	private $filename;
	private $save_dir;
	private $temp_dir;
	private $max_size;

	// Section: Public
	
	/*
	
	Function: extension
		Returns just the extension of a file.

	Parameters:
		path - file path

	Returns:
		string

	*/

	public function extension($path)
	{
		return pathinfo($path, PATHINFO_EXTENSION);
	}

	/*

	Function: type
		Gets the mime type of a file.

	Parameters:	
		path - file path

	Returns:
		string	

	*/	

	public function type($path)
	{
		return get_mime_type($path);
	}

	/*

	Function: size
		Gets the size of a file.

	Parameters:	
		path - file path

	Returns:
		int	

	*/	
	
	public function size($path)
	{
		return get_filesize($path);
	}

	/*

	Function: info
		Gets an array of associated information of a file.

	Parameters:	
		path - file path

	Returns:
		array	

	*/	

	public function info($path)
	{
		$return = pathinfo($path);
		$return['type'] = self::type($path);
		$return['size'] = self::size($path);
		return $return;
	}

	/*

	Function: copy
		Copies a file.

	Parameters:	
		filename - original file path
		destination - new file path

	Returns:
		bool	

	*/	

	public function copy($filename, $destination)
	{
		return @copy($filename, $destination);
	}

	/*

	Function: move
		Moves a file.

	Parameters:	
		filename - original file path
		destination - new file path

	Returns:
		bool	

	*/	

	public function move($filename, $destination)
	{
		return @rename($filename, $destination);
	}

	/*

	Function: delete
		Deletes a file

	Parameters:	
		path - file path

	Returns:
		bool	

	*/	

	public function delete($path)
	{
		return @unlink($path);
	}
	
}
?>