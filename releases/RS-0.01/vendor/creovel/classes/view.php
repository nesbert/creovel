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

/**
 * View class
 *
 * @copyright	Copyright (c) 2005-2006, creovel.org
 * @package		creovel
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 */
 class view
 {
 
	/**
	 * Creates the page to be displayed and sets it to the page property
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 * @param string $view_path
	 * @param string $layout_path
	 */
	public function _create_view($view_path, $layout_path, $options = null)
	{
		try {
			// set content data
			$content = $options['text'];
			
			if ( $options['render'] !== false ) {
				$content .= self::_get_include_contents($view_path, $options);
			}
			
			// combine content and template. else use content only
			switch ( true ) {
			
				case ( $options['layout'] !== false ):
					if ( file_exists($layout_path) ) {
						$page = str_replace('@@page_contents@@', $content, self::_get_include_contents($layout_path, $options));
					} else {
						throw new Exception("Unable to render 'layout'. File not found <strong>{$layout_path}</strong>.");
					}
				break;
			
				default:
					$page = $content;
				break;
			
			}
			
			return $page;
			
		} catch ( Exception $e ) {
		
			// add to errors
			$_ENV['error']->add($e->getMessage());
		
		}		
	}
	
	/**
	 * Return the page to be displayed as string
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 * @return string 
	 */
	public function _get_view($view_path = null, $layout_path = null, $options = null)
	{
		return self::_create_view($view_path, $layout_path, $options);
	}

	/**
	 * Print page to screen
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 */
	public function _show_view($view_path = null, $layout_path = null, $options = null)
	{
		print self::_create_view($view_path, $layout_path, $options);
	}

	/*
	 * http://us3.php.net/manual/en/function.include.php
	 * Example 16-11. Using output buffering to include a PHP file into a string
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 * @param string $filename
	 * @return string
	 */
	public function _get_include_contents($filename, $options = null)
	{
	   if ( is_file($filename) ) {
		   ob_start();
		   
			// create a variable foreach option, using its key as the vairable name
			if ( count($options) ) foreach ( $options as $key => $values ) $$key = $values;

		   include $filename;
		   $contents = ob_get_contents();
		   ob_end_clean();
		   return $contents;
	   }
	   return false;
	}
	
 }
 ?>