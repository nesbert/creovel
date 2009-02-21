<?php
/*
	Class: file
	
	File managing class.
*/

class file
{

	// Section: Public
	
	/*
	
		Function: __construct
		
		Class construct.
		
		Parameters:
		
			file - String of file path.
	*/
	
	public function __construct($file)
	{
		if ($file) {
			$this->initialize($file);
		}
	}
	
	/*
	
		Function: initialize
		
		Initialize class.
		
		Parameters:
		
			file - String of file path.
	*/
	
	public function initialize($file)
	{
		if (is_array($file)) {
			$this->filename = $file['tmp_name'];
			$this->name = $file['name'];
		} else if (is_string($file)) {
			$this->filename = $file;
		}
	}
	
	/*
		Function: extension
		
		Returns just the extension of a file.
		
		Parameters:
		
			path - String of file path.
		
		Returns:
		
			String of the file extension.
	*/
	
	public function extension($path = '')
	{
		if ($this->name) {
			$tmp = explode('.', $this->name);
			if (count($tmp)) {
				return $tmp[count($tmp) - 1];
			} else {
				return;
			}
		} else {
			return pathinfo(($path ? $path : $this->filename), PATHINFO_EXTENSION);
		}
	}
	
	/*
		Function: extension
		
		Returns just the extension of a file. Alias to file::extention().
		
		Parameters:
		
			path - String of file path.
		
		Returns:
		
			String of the file extension.
	*/
	
	public function ext($path = '')
	{
		return $this->extension($path);
	}
	
	/*
		Function: type
		
			Gets the mime type of a file.
			
		Parameters:
		
			path - String of file path.
			
		Returns:
		
			String of the file type.
	*/

	public function type($path = '')
	{
		return get_mime_type(($path ? $path : $this->filename));
	}

	/*
		Function: size
		
		Gets the size of a file in human readable format.
		
		Parameters:
		
			path - String of file path.
			
		Returns:
		
			String.
	*/
	
	public function size($path = '')
	{
		return get_filesize(($path ? $path : $this->filename));
	}
	
	/*
		Function: filesize
		
		Gets the size of a file in bytes.
		
		Parameters:
		
			path - String of file path.
			
		Returns:
		
			Integer.
	*/
	
	public function filesize($path = '')
	{
		return filesize(($path ? $path : $this->filename));
	}
	
	/*
		Function: info
		
		Gets an array of associated information of a file.
		
		Parameters:
		
			path - String of file path.
		
		Returns:
		
			Array.
	*/
	
	public function info($path = '')
	{
		$path = $path ? $path : $this->filename;
		$return = pathinfo($path);
		$return['type'] = self::type($path);
		$return['size'] = self::size($path);
		$return['modified'] = date ("Y-m-d H:i:s", filemtime($path));
		return $return;
	}
	
	/*
		Function: name
		
		Returns original file name or basename.
		
		Parameters:
		
			path - String of file path.
		
		Returns:
		
			String of the file name.
	*/
	
	public function name($path = '')
	{
		if ($this->name && !$path) {
			return $this->name;
		} else {
			return basename(($path ? $path : $this->filename));
		}
	}
	
	/*
		Function: copy
		
		Copies a file to a new location.
		
		Parameters:
		
			filename - String of original file path.
			destination - String of new file path.
			
		Returns:
		
			Boolean.
	*/
	
	public function copy($filename, $destination)
	{
		$return = @copy($filename, $destination);
		chmod($destination, 0755);
		return $return;
	}
	
	/*
		Function: copy
		
		Copies a file to a new location.
		
		Parameters:
		
			destination - String of new file path.
			
		Returns:
		
			Boolean.
	*/
	
	public function copy_to($destination)
	{
		return $this->copy(($filename ? $filename : $this->filename), $destination);
	}
	
	/*
		Function: move
		
		Moves a file to a new location.
		
		Parameters:
		
			filename - String of original file path.
			destination - String of new file path.
		
		Returns:
		
			Boolean.
	*/
	
	public function move($filename, $destination)
	{
		$return = @rename($filename, $destination);
		chmod($destination, 0755);
		return $return;
	}
	
	/*
		Function: move_to
		
		Moves a file to a new location.
		
		Parameters:
		
			destination - String of new file path.
			
		Returns:
		
			Boolean.
	*/
	
	public function move_to($destination)
	{
		return $this->move(($filename ? $filename : $this->filename), $destination);
	}
	
	/*
		Function: rename
		
		Renames a file and saves it to current location.
		
		Parameters:
		
			destination - String of new file path.
		
		Returns:
		
			Boolean.
	*/
	
	public function rename($name)
	{
		return $this->move_to(dirname($this->filename).DS.$name);
	}
	
	/*
		Function: delete
		
		Deletes a file.
		
		Parameters:
		
			path - String of file path.
		
		Returns:
		
			Boolean.
	*/
	
	public function delete($path = '')
	{
		if (!$path) return false;
		return @unlink(($path ? $path : $this->filename));
	}
	
	/*
		Function: uploaded
		
		Tells whether the file was uploaded via HTTP POST.
		
		Parameters:
		
			path - String of file path.
		
		Returns:
		
			Boolean.
	*/
	
	public function uploaded($path = '')
	{
		return is_uploaded_file(($path ? $path : $this->filename));
	}
	
	/*
		Function: exists
		
		Tells whether the file exists and is a regular file.
		
		Parameters:
		
			path - String of file path.
		
		Returns:
		
			Boolean.
	*/
	
	public function exists($path = '')
	{
		return is_file(($path ? $path : $this->filename));
	}
	
	// Section: Private
	
	/*
		Property: filename
		
		String path of the file.
	*/
	
	private $filename;

}
?>