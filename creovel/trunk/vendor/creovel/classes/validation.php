<?php
/*
 * Validation class.
 */
class validation
{

	/**
	 * Class properties.
	 *
	 * @author Nesbert Hidalgo
	 * @access private
	 */
	private static $is_valid = false;
	private static $rules;
	private static $data;
	private static $errors;
	
	/**
	 * Class constants.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 */	 
	const REGEX_NUMBER = '/^[0-9]+?[.]?[0-9]*$/';
	const REGEX_ALPHA_NUMERIC = '/^[a-zA-Z0-9]+$/';
	const REGEX_PHONE_NUMBER = '/^[01]?[- .]?(\([2-9]\d{2}\)|[2-9]\d{2})[- .]?\d{3}[- .]?\d{4}(| | x| ext| ext | ext.| ext. )\d{0,}$/';
	const REGEX_EMAIL_ADDRESS = '/\w+@\w+\.[a-zA-Z.]{2,5}/';
	const REGEX_WEB_URL = '/^(ht|f)tp(s?)\:\/\/[0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*(:(0-9)*)*(\/?)([a-zA-Z0-9\-\.\?\,\'\/\\\+&amp;%\$#_]*)?$/';
	const REGEX_PICTURE = '/.*\.(jpg|JPG|jpeg|JPEG|gif|GIF|bmp|BMP|png|PNG)$/';
	const REGEX_DATE = '/^[0-9]{1,2}(\/)[0-9]{1,2}(\/)[0-9]{4}$/'; // valid formats MM/DD/YYYY
	const REGEX_US_POSTAL_CODE = '/^\d{5}-\d{4}|\d{5}$/'; // valid formats NNNNN or NNNNN-NNNN
	const REGEX_NON_SPECIAL_CHARS = '/^[a-zA-Z0-9_]+$/';
	
	const FIELD_NAME = '@@field_name@@';
	const VALUE = '@@value@@';
	
	function __construct()
	{
		$this->errors = new error('model');
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
			//case ( !$required && !$val):
				return true;
			break;
			
			default:
				return false;
			break;			
		}
	}
	
	/**
	 * Base function to validate by regular expression.
	 * 
	 * @author Nesbert Hidalgo
	 * @access private
	 * @param string $regex required
	 * @param string $field required
	 * @param mixed $val required
	 * @param string $msg optional
	 * @param string $required optional
	 * @return bool 
	 */	
	private function validate_field_by_regex($regex, $field, $val, $msg = null, $required = false)
	{
		// regular expression does not start or end with a forward slash add slashes
		$regex = ( $regex{0} != '/' ? '/'.$regex : $regex );
		$regex = ( $regex{strlen($regex)-1} != '/' ? $regex.'/' : $regex );
		return self::validate_field_by_bool(preg_match($regex, $val), $field, $val, $msg, $required);
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
	 * Adds form error to the environment error object.
	 * 
	 * @author Nesbert Hidalgo
	 * @access private
	 * @param string $field required
	 * @param mixed $val required
	 * @param string $msg required
	 */	
	private function add_error($field, $val, $msg)
	{
		//add('form', $msg, array('field' => $field, 'value' => $val, 'message' => $msg));
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
	public function validate_presence_of($field, $val, $msg = null)
	{
		return self::validate_field_by_bool(true, $field, trim($val), self::format_message($field, $val, $msg, self::FIELD_NAME." is a required field."), true);
	}

	/**
	 * Validates that $val and $val2 match.
	 * 
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $field required
	 * @param mixed $val required
	 * @param mixed $val2 required
	 * @param string $msg optional default is "... doesn't match."
	 * @return bool 
	 */	
	public function validate_confirmation_of($field, $val, $val2, $msg = null)
	{
		return self::validate_field_by_bool(($val == $val2), $field, $val, self::format_message($field, $val, $msg, self::FIELD_NAME." doesn't match."));
	}
	
	/**
	 * Validates that $val is an integer.
	 * 
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $field required
	 * @param mixed $val required
	 * @param string $msg optional default is "Enter a number only with no decimals for ..."
	 * @param bool $required optional default is false
	 * @return bool 
	 */
	public function validate_integer($field, $val, $msg = null, $required = false)
	{
		return self::validate_field_by_bool(( is_numeric($val) && !strstr($val, '.') ), $field, $val, self::format_message($field, $val, $msg, "Enter only a number with no decimals for ".self::FIELD_NAME."."), $required);
	}	
	
	/**
	 * Validates that $val preg_match with $pattern (regular expression).
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
	public function validate_format_of($field, $val, $msg = null, $required = false, $pattern)
	{
		return self::validate_field_by_regex($pattern, $field, $val, self::format_message($field, $val, $msg, self::FIELD_NAME." is invalid."), $required);
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
	public function validate_numericality_of($field, $val, $msg = null, $required = false)
	{
		return self::validate_field_by_regex(self::REGEX_NUMBER, $field, $val, self::format_message($field, $val, $msg, self::FIELD_NAME." is not a number."), $required);
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
	public function validate_email($field, $val, $msg = null, $required = false)
	{
		return self::validate_field_by_regex(self::REGEX_EMAIL_ADDRESS, $field, $val, self::format_message($field, $val, $msg, self::FIELD_NAME." is an invalid email address."), $required);
	}
	
	/**
	 * Validates that $val is a valid web address.
	 * 
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $field required
	 * @param mixed $val required
	 * @param string $msg optional default is "... is an invalid web address."
	 * @param bool $required optional default is false
	 * @return bool 
	 */	
	public function validate_url($field, $val, $msg = null, $required = false)
	{
		return self::validate_field_by_regex(self::REGEX_WEB_URL, $field, $val, self::format_message($field, $val, $msg, self::FIELD_NAME." must be a valid web address."), $required);
	}
	
	/**
	 * Validates that $val is a valid picture extension.
	 * 
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $field required
	 * @param mixed $val required
	 * @param string $msg optional default is "... is an invalid picture format."
	 * @param bool $required optional default is false
	 * @return bool 
	 */	
	public function validate_picture($field, $val, $msg = null, $required = false)
	{
		return self::validate_field_by_regex(self::REGEX_PICTURE, $field, $val, self::format_message($field, $val, $msg, self::FIELD_NAME." is an invalid picture format."), $required);
	}
	
	
	
	
	
	
	
	/* anything below i have not updated [NH] */
	
	
	
	
	
	
	
	
	/**
	 * Validates that $val is a valid date format (eb. MM/DD/YYYY).
	 * 
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $field required
	 * @param mixed $val required
	 * @param string $msg optional default is "... is an invalid date format."
	 * @param bool $required optional default is false
	 * @return bool 
	 */
	
	public function validate_date($field, $val, $msg = null, $required = false)
	{
		$msg = $msg ? $msg : self::FIELD_NAME." is an invalid date format.";
		return $this->validate_field_by_regex(self::REGEX_DATE, $field, $val, $msg, $required);
	}
	
	/**
	 * Validates that $val is a valid phone number (eg. NNN-NNN-NNNN).
	 * 
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $field required
	 * @param mixed $val required
	 * @param string $msg optional default is "... iis an invalid US Postal Code."
	 * @param bool $required optional default is false
	 * @return bool 
	 */
	
	public function validate_phone($field, $val, $msg = null, $required = false)
	{
		$msg = $msg ? $msg : self::FIELD_NAME." is invalid.";
		return $this->validate_field_by_regex(self::REGEX_PHONE_NUMBER, $field, $val, $msg, $required);
	}
	
	/**
	 * Validates that $val is a valid US Postal Code (eg. NNNNN or NNNNN-NNNN).
	 * 
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $field required
	 * @param mixed $val required
	 * @param string $msg optional default is "... iis an invalid US Postal Code."
	 * @param bool $required optional default is false
	 * @return bool 
	 */
	
	public function validate_us_postal_code($field, $val, $msg = null, $required = false)
	{
		$msg = $msg ? $msg : self::FIELD_NAME." is an invalid US Postal Code.";
		return $this->validate_field_by_regex(self::REGEX_US_POSTAL_CODE, $field, $val, $msg, $required);
	}
	
	/**
	 * Validates that $val has no specail characters.
	 * 
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $field required
	 * @param mixed $val required
	 * @param string $msg optional default is "... is invalid."
	 * @param bool $required default is false
	 * @return bool 
	 */
	
	public function validate_non_special_chars($field, $val, $msg = null, $required = false)
	{
		$msg = $msg ? $msg : self::FIELD_NAME." is invalid.";
		return $this->validate_field_by_regex(self::REGEX_NON_SPECIAL_CHARS, $field, $val, $msg, $required);
	}
	
	/**
	 * Validates that $val is a positive number.
	 * 
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $field required
	 * @param mixed $val required
	 * @param string $msg optional default is "...  must be a positive number."
	 * @param bool $required optional default is false
	 * @return bool 
	 */
	
	public function validate_positive_number($field, $val, $msg = null, $required = false)
	{
		$msg = $msg ? $msg : self::FIELD_NAME." must be a positive number.";
		return $this->validate_field_by_bool(( is_numeric($val) && $val > 0 ), $field, $val, $msg, $required = false);
	}
	
	/**
	 * Validates that $val is falls between $min and $max.
	 * 
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $field required
	 * @param mixed $val required
	 * @param string $msg optional default is "... must be between {$min} and  {$max}."
	 * @param bool $required optional default is false
	 * @param int $min optional default is 0
	 * @param int $min optional default is 100
	 * @return bool 
	 */
	
	public function validate_range($field, $val, $msg = null, $required = false, $min = 0, $max = 100)
	{
		$msg = $msg ? $msg : self::FIELD_NAME." must be between {$min} and  {$max}.";
		$bool = ( (is_numeric($min) && is_numeric($max)) && ($val >= $min && $val <= $max) );
		return $this->validate_field_by_bool($bool, $field, $val, $msg, $required = false);
	}
	
	/**
	 * Validates that $val's length falls between $min and $max.
	 * 
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $field required
	 * @param mixed $val required
	 * @param string $msg optional default is "... must be atleast {$min} characters."
	 * @param bool $required optional default is false
	 * @param int $min optional default is 0
	 * @param int $min optional default is 100
	 * @return bool 
	 */
	
	public function validate_length($field, $val, $msg = null, $required = false, $min = 0, $max = 100)
	{
		$msg = $msg ? $msg : self::FIELD_NAME." must be atleast {$min} characters.";
		$bool = ( (is_numeric($min) && is_numeric($max)) && (strlen($val) >= $min && strlen($val) <= $max) );
		return $this->validate_field_by_bool($bool, $field, $val, $msg, $required = false);
	}
	
	/**
	 * Validates that $val does not already exists in $table_name. If $id is passed
	 * will allow over-riding of current record.
	 * 
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $field required
	 * @param mixed $val required
	 * @param string $msg optional default is "... already exists, please enter another."
	 * @param bool $required default is false
	 * @param string $table_name required
	 * @param int/array $id optional
	 * @return bool 
	 */
	
	public function validate_uniqueness_of($field, $val, $msg = null, $required = false, $table_name, $id = null)
	{
		$msg = $msg ? $msg : self::FIELD_NAME." already exists, please enter another.";
		
		if ( $required || $val ) {

			$conditions = "{$field} = '{$val}'";
			
			if ( $id ) {
				if ( is_assoc_array($id) ) {
					foreach ( $id as $key => $val ) $conditions .= " AND $key != '{$val}'";
				} else {
					$conditions .= " AND id != '{$id}'";
				}
			}
			
			$obj = new model($table_name);
			$obj->find(array('conditions' => $conditions));
					
			if ( $obj->get_row_count() ) {
				$this->add_to_error($field, $val, $msg);
				return false;
			} else {
				true;
			}
			
		} else {
		
			return true;
		
		}
		
	}
	
	/**
	 * Validates that atleast one of the two values is entered
	 * will allow over-riding of current record.
	 * 
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $field 1
	 * @param mixed $val 1
	 * @param string $field 2
	 * @param mixed $val 2
	 * @param string $msg optional default is "... already exists, please enter another."
	 * @return bool 
	 */
	
	public function verify_1_required_between_2($field_1, $val_1, $field_2, $val_2, $msg = null)
	{
		$msg = $msg ? $msg : 'You must enter either ' . $this->format_name($field_1) . ' or ' . $this->format_name($field_2) . '.';

		if ( $val_1 || $val_2 ) {
			return true;
		} else {
			$this->add_to_error($field_1, $val_1, $msg);
			return false;
		}		
		
	}

	public function validate_agreement($field, $value, $msg = null)
	{
		$msg = $msg ? $msg : "Your must agree to the ".self::FIELD_NAME;
		return $this->validate_field_by_bool(!is_null($value), $field, $value, $msg, true);
	}

}
?>