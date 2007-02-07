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
 * Validation class.
 *
 * @copyright	Copyright (c) 2005-2006, creovel.org
 * @package		creovel
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class validation
{

	/**
	 * Class constants.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 */
	const FIELD_NAME = '@@field_name@@';
	const VALUE = '@@value@@';
	
	public function __construct(&$errors  = null)
	{
		if ($errors) {
			$this->errors = $errors;
		} else {
			$this->errors = new error('model');
		}
	}
	
	
	/**
	 * Test for errors
	 * 
	 * @author John Faircloth
	 * @access public
	 * @return bool 
	 */
	 
	public function has_errors() {
		return $this->errors->has_errors();
	}
	
	/**
	 * Base function to validate by boolean value.
	 * 
	 * @author Nesbert Hidalgo
	 * @access private
	 * @param bool $bool required
	 * @param string $field required
	 * @param mixed $val required
	 * @param string $msg optional
	 * @param string $required optional
	 * @return bool 
	 */
	private function validate_field_by_bool($bool, $field, $val, $msg = null, $required = false)
	{
		switch ( true ) {
			case ( $required && $val && $bool ):
			case ( !$required && $val && $bool ):
			case ( !$required && !$val ):
				return true;
			break;
			
			default:
				$this->errors->add($field, $msg);
				return false;
			break;
		}
	}
	
	/**
	 * Format error message by adding fieldname or value we needed
	 * 
	 * @author Nesbert Hidalgo
	 * @access private
	 * @param string $field required
	 * @param mixed $val required
	 * @param string $msg required
	 * @param string $default_msg required
	 * @return string 
	 */
	private function format_message($field, $val, $msg, $default_msg)
	{
		$message = $msg ? $msg : $default_msg;
		
		// check for fieldname and humanze it
		if ( strstr($message, self::FIELD_NAME) ) {
			$message = str_replace(self::FIELD_NAME, humanize($field), $message);
		}
		
		// check for value and insert it into the message
		if ( strstr($message, self::VALUE) ) {
			$message = str_replace(self::VALUE, $val, $message);
		}
		
		return $message;
	}
	
	/**
	 * Validates that $val is not empty.
	 * 
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $field required
	 * @param mixed $val required
	 * @param string $msg optional default is "... is a required field."
	 * @return bool
	 */
	public function validates_presence_of($field, $val, $msg = null)
	{
		return self::validate_field_by_bool(true, $field, trim($val), self::format_message($field, $val, $msg, self::FIELD_NAME." is a required field."), true);
	}

	/**
	 * Validates $val with a regular expression $pattern using preg_match().
	 * 
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $field required
	 * @param mixed $val required
	 * @param string $msg optional default is "... is invalid."
	 * @param bool $required default is false
	 * @param string $pattern required regular expression
	 * @return bool
	 */
	public function validates_format_of($field, $val, $msg = null, $required = false, $pattern = false)
	{
		return self::validate_field_by_bool(preg_match($pattern, $val), $field, $val, self::format_message($field, $val, $msg, self::FIELD_NAME." is invalid."), $required);
	}
	
	/**
	 * Validates that $val is a valid email address.
	 * 
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $field required
	 * @param mixed $val required
	 * @param string $msg optional default is "... is an invalid email address."
	 * @param bool $required optional default is false
	 * @return bool
	 */
	public function validates_email_of($field, $val, $msg = null, $required = false)
	{
		return self::validate_field_by_bool(is_email($val), $field, $val, self::format_message($field, $val, $msg, self::FIELD_NAME." is an invalid email address."), $required);
	}
	
	/**
	 * Validates that $val is numeric.
	 * 
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $field required
	 * @param mixed $val required
	 * @param string $msg optional default is "... is not a number."
	 * @param bool $required optional default is false
	 * @return bool
	 */	
	public function validates_numericality_of($field, $val, $msg = null, $required = false)
	{
		return self::validate_field_by_bool(is_number($val), $field, $val, self::format_message($field, $val, $msg, self::FIELD_NAME." is not a number."), $required);
	}
}
?>