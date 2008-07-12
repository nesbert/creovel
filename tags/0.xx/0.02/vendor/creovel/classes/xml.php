<?php
/*

	Class: xml
	
	XML class to rule all.

	Todo:
	
		* Validate CDATA
		* might not need get_attributes()
		* mapper -> xml to array -> easier interface/view

*/

class xml
{

	// Section: Public

	/*
		Property: version
		
		XML version
	*/
	
	public $version = '1.0'; // XML version
	
	/*
		Property: encoding
		
		File encoding.
	*/
	
	public $encoding		= 'ISO-8859-1';
	
	/*
		Property: data
		
		Data object of XML structure.
	*/
	
	public $data = '';
	
	/*
		Property: parser
		
		PHP xml_parser_create()
	*/
	
	private $parser = '';
	
	/*
	
		Function: __construct
		
		Initialize class and set data property.
	
	*/

	public function __construct()
	{
		$this->data = new element;
		$this->data->name = 'root';
	}
	
	/*
	
		Function: load
		
		Load XML data from file path or load from array.
		
		Parameters:
		
			xml_file - required
			
		Returns:
		
			Object
	
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
			return $this->data->children = $this->_parse($data);
		} else {
			application_error($error);
		}	
	}
	
	/*
	
		Function: to_str
		
		Create XML string from the $data currentlly loaded.
		
		Returns:
		
			String
	
	*/

	public function to_str()
	{
		return $this->array_to_xml($this->data->children);
	}
	
	/*
	
		Function: file
		
		Output XML file.
		
		Returns:
		
			String
	
	*/	

	public function file()
	{
		header("Connection: close");
		header("Content-Length: " . strlen( $output = $this->to_str() ));
		header("Content-Type: application/xml");
		header("Date: " . date("r"));
		die($output);
	}
	
	/*
	
		Function: array_to_xml
		
		Convert an array or object to an XML string. Sample array structure:
		
		(start code)
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
		(end)
		
		Parameters:
		
			data - required
			level - optional element level
		
		Returns:
		
			String
	
	*/
	
	public function array_to_xml($data, $level = 1)
	{
		$elm = (object) $data;
        $xml = '';
		if ( $level == 1 ) {
			$xml .= '<?xml version="' . $this->version . '"' . ( $this->encoding != false ? ' encoding="' . $this->encoding : '' ) . '"?>'."\n";	
		}
		
		$xml .= "<" . $elm->name . $this->_attribute_str($elm->attributes);
		
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
	
	/*
	
		Function: cdata
		
		 Create and validate CDATA.
		
		Parameters:
		
			cdata - required
			
		Returns:
		
			String
	
	*/
	
	public function cdata($cdata)
	{
		$cdata = trim($cdata);
		return '<![CDATA[' . $cdata . ']]>';
	}
	
	// Section: Private
	
	/*
	
		Function: _parse
		
		XML parser that creates an object structured similar to the file.
		A modified version of efredricksen at gmail dot com's "one true parser".
		http://us2.php.net/manual/en/function.xml-parse-into-struct.php#66487
		
		Parameters:
		
			xml - required XML data
		
		Returns:
		
			Object
	
	*/
	
	private function _parse($xml)
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
	
	/*
	
		Function: _get_attributes
		
		Formats and converts attributes array to an object.
		
		Parameters:
		
			attributes - required
			
		Returns:
		
			Object
	
	*/

	private function _get_attributes($attributes)
	{
		if ( !$attributes ) return;
		foreach ( $attributes as $key => $val ) {
			 $attributes_temp[ strtolower($key) ] = $val;
		}
		return (object) $attributes_temp;
	}
	
	/*
	
		Function: _attribute_str
		
		Create attribute string.
		
		Parameters:
		
			attributes - required
		
		Returns:
		
			String
	
	*/
	
	private function _attribute_str($attributes)
	{
		$str = '';
		if ( $attributes ) foreach (  $attributes as $id => $val ) {
			$str .= ' '. $id . '="' . $val . '"';
		}
		return $str;
	}
	
	
}

/*

	Class: element
	
	Element class used for XML class. Class used to store data.

*/

class element
{

	public $name;
	public $cdata;
	public $attributes;
	public $children;
	
}
?>