<?php
/*
 * Validation class.
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
	
	public function __construct(&$errors)
	{
		$this->errors = $errors;
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