<?php
/*

	Class: file
	
	File managing class.
	
	Todo:
	
		* Advance file munipulation.
		* File editing within the class.

*/

class file
{

	// Section: Public
	
	/*
	
		Function: extension
		
		Returns just the extension of a file.
		
		Parameters:
		
			path - String of file path.
		
		Returns:
		
			String of the file extension.

	*/

	public function extension($path)
	{
		return pathinfo($path, PATHINFO_EXTENSION);
	}

	/*
	
		Function: type
		
			Gets the mime type of a file.
			
		Parameters:
		
			path - String of file path.
			
		Returns:
		
			String of the file type.
	
	*/

	public function type($path)
	{
		return get_mime_type($path);
	}

	/*
	
		Function: size
		
		Gets the size of a file.
		
		Parameters:
		
			path - String of file path.
			
		Returns:
	
			Integer
	
	*/
	
	public function size($path)
	{
		return get_filesize($path);
	}

	/*
	
		Function: info
		
		Gets an array of associated information of a file.
		
		Parameters:
		
			path - String of file path.
			
		Returns:
		
			Array
	
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
		
		Copies a file to a new location.
		
		Parameters:
		
			filename - String of original file path.
			destination - String of new file path.
			
		Returns:
		
			Boolean
	
	*/	

	public function copy($filename, $destination)
	{
		return @copy($filename, $destination);
	}

	/*
	
		Function: move
		
		Moves a file to a new location.
		
		Parameters:
		
			filename - String of original file path.
			destination - String of new file path.
			
		Returns:
		
		Boolean
	
	*/

	public function move($filename, $destination)
	{
		return @rename($filename, $destination);
	}

	/*
	
		Function: delete
		
		Deletes a file.
		
		Parameters:
		
			path - String of file path.

	Returns:
	
		Boolean
	
	*/	

	public function delete($path)
	{
		return @unlink($path);
	}
	
	// Section: Private
	
	/*
		Property: filename
		
		String path of the file
	*/
	private $filename;
	private $save_dir;
	private $temp_dir;
	private $max_size;

}
?>