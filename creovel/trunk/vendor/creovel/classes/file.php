<?php
/**
 * Copyright (c) 2005-2006, creovel.org
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated 
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software,
 * and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions
 * of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED
 * TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * Licensed under The MIT License. Redistributions of files must retain the above copyright notice.
 */

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