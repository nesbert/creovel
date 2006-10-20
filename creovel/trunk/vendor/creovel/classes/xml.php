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
 * XML class to rule all.
 *
 * @copyright	Copyright (c) 2005-2006, creovel.org
 * @package		creovel
 * @subpackage	classes
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @author		Nesbert Hidalgo
 * @version		0.1 (10/16/2006)
 * @todo		validate CDATA
 *				might not need get_attributes()
 *				mapper -> xml to array -> easier interface/view
 */
class xml
{

	public $version			= '1.0'; // XML version
	public $encoding		= 'ISO-8859-1'; // file encoding
	public $data			= ''; // Data object of XML structure
	
	private $parser			= ''; // PHP xml_parser_create()
	
	/**
	 * Initialize class and set data property.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 */
	public function __construct()
	{
		$this->data = new element;
		$this->data->name = 'root';
	}
	
	/**
	 * Load XML data from file path or load from array.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string/array $xml_file required
	 * @return object
	 */
	public function load($xml_file)
	{	
		if ( is_string($xml_file) ) {
			// load xml file
			$data = file_get_contents($xml_file);
			$error = "Unable to load XML data. File not found <strong>{$xml_file}</strong>";
		} else {
			// create xml from array and load xml file
			$data = $this->array_to_xml($xml_file);
			$error = 'XML structure not valid.';
		}
		
		if ($data) {
			return $this->data->children = $this->parse($data);
		} else {
			application_error($error);
		}	
	}
	
	/**
	 * Create XML string from the $data currentlly loaded.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @return string
	 */	
	public function to_str()
	{
		return $this->array_to_xml($this->data->children);
	}
	
	/**
	 * Output XML file.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @return string
	 */	
	public function file()
	{
		header("Connection: close");
		header("Content-Length: " . strlen( $output = $this->to_str() ));
		header("Content-Type: application/xml");
		header("Date: " . date("r"));
		die($output);
	}
	
	/**
	 * Convert an array or object to an XML string. Sample array structure:
	 *
	 * <code>
		element Object
            (
                [name] => rss
                [cdata] => 
                [attributes] => stdClass Object
                    (
                        [version] => 2.0
                        [xmlns:digg] => docs/diggrss/
                    )

                [children] => Array
                    (
                        [0] => element Object
                            ( ... )
                        ...
                    )
            )
	 * </code>
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param array/object $data required
	 * @param int $level optional element level
	 * @return string
	 */
	public function array_to_xml($data, $level = 1)
	{
		$elm = (object) $data;
        $xml = '';
		if ( $level == 1 ) {
			$xml .= '<?xml version="' . $this->version . '"' . ( $this->encoding != false ? ' encoding="' . $this->encoding : '' ) . '"?>'."\n";	
		}
		
		$xml .= "<" . $elm->name . $this->attribute_str($elm->attributes);
		
		if ( $elm->no_end_tag ) {
			$xml .= " />\n";
		} else {
			$xml .= '>';
			if ( $elm->cdata ) {
				$xml .= $this->cdata($elm->cdata);
			} else {
				$xml .= "\n";
				if ( is_array($elm->children) ) foreach ( $elm->children as $child ) {
					$xml .= $this->array_to_xml($child, 0);
				}
			}
			$xml .= '</' . $elm->name . ">\n";
		}
		
		return $xml;	
	}
	
	/**
	 * XML parser that creates an object structured similar to the file. A modified version of
	 * efredricksen at gmail dot com's "one true parser".
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $xml XML data
	 * @return object
	 * @link http://us2.php.net/manual/en/function.xml-parse-into-struct.php#66487
	 */	
	private function parse($xml)
	{
		$this->parser = xml_parser_create();
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parse_into_struct($this->parser, $xml, $tags);
		xml_parser_free($this->parser);
		
		$elements = array();  // the currently filling [child] 	array
		$stack = array();
		foreach ( $tags as $tag ) {
			
			$index = count($elements);
			
			if ( $tag['type'] == 'complete' || $tag['type'] == 'open' ) {
			
				$elements[$index] = new element;
				$elements[$index]->name = $tag['tag'];
				$elements[$index]->cdata = trim($tag['value']);
				
				if ( $tag['attributes'] ) {
					$elements[$index]->attributes = (object) $tag['attributes'];
				}
								
				if ( $tag['type'] == 'open' ) {  // push
					$elements[$index]->children = array();
					$stack[count($stack)] = &$elements;
					$elements = &$elements[$index]->children;
				}
				
			}
			
			if ($tag['type'] == "close") {  // pop
				$elements = &$stack[count($stack) - 1];
				unset($stack[count($stack) - 1]);
			}
			
		}
		
		return $elements[0];
	}
	
	/**
	 * Formats and converts attributes array to an object.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param array $attributes required
	 * @return object
	 */	
	private function get_attributes($attributes)
	{
		if ( !$attributes ) return;
		foreach ( $attributes as $key => $val ) {
			 $attributes_temp[ strtolower($key) ] = $val;
		}
		return (object) $attributes_temp;
	}
	
	/**
	 * Create attribute string.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param array $attributes required
	 * @return string
	 */	
	private function attribute_str($attributes)
	{
		$str = '';
		if ( $attributes ) foreach (  $attributes as $id => $val ) {
			$str .= ' '. $id . '="' . $val . '"';
		}
		return $str;
	}
	
	/**
	 * Create and validate CDATA.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $cdata required
	 * @return string
	 */	
	private function cdata($cdata)
	{
		$cdata = trim($cdata);
		return '<![CDATA[' . $cdata . ']]>';
	}
	
}

/**
 * Element class used for XML class.
 *
 * @copyright	Copyright (c) 2005-2006, creovel.org
 * @package		creovel
 * @subpackage	classes
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class element
{

	public $name;
	public $cdata;
	public $attributes;
	public $children;
	
}
?>