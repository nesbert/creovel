<?php
/*
 * Validation class.
 *
 */
class validation
{

	/**
	 * Class properties.
	 */

	private $is_valid = false;
	private $rules;
	private $data;
	private $replace_needles;
	
	public $errors;
	
	/**
	 * Class constants.
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
	
	/**
	 * Loads $rules, $data, $replace_needles class properties.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param array $rules_arr optional
	 * @param array $data_arr optional
	 */
	
	public function __construct($rules_arr = null, $data_arr = null)
	{
		if ( $rules_arr ) $this->set_rules($rules_arr);
		if ( $data_arr ) $this->set_data($data_arr);
		$this->replace_needles = array(self::FIELD_NAME, self::VALUE);
	}
	
	/**
	 * Sets $rules class properties.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param array $rules_arr required
	 */
	
	public function set_rules($rules_arr)
	{	
		$this->rules = $rules_arr;
	}
	
	/**
	 * Sets $data class properties.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param array $data_arr required
	 */
	
	public function set_data($data_arr)
	{
		$this->data = $data_arr;
	}
	
	/**
	 * Validates $data with $rules and sets $is_valid property true if 
	 * $errors property is empty. If no rules are set checks that $errors 
	 * property is empty and sets $is_valid property to true.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @return bool
	 */
	
	public function validate()
	{
		// reset is_valid
		$this->is_valid = false;
	
		// if rules are set continue
		if ( count($this->rules) ) {
			
			// foreach rule, set and call validate function
			foreach ( $this->rules as $verify_function => $verify_data ) {
			
				foreach ($verify_data as $key => $val ) {
					// if field is an array validate each field
					if ( is_array($val['field']) ) {
						foreach ( $val['field'] as $field ) {
							$this->$verify_function($field, $this->data[$field], $val['msg'], $val['required'], $val['extra_1'], $val['extra_2']);
						}
					} else {
						$this->$verify_function($val['field'], $this->data[$val['field']], $val['msg'], $val['required'], $val['extra_1'], $val['extra_2']);
					}
				}
			
			}			
			
		}
		
		// if no errors, set is_valid to true
		if ( empty($this->errors) ) { 
		
			$this->is_valid = true;
		
		// load errors into session	
		} else {
		
			$GLOBALS['form_errors'] = array_merge($GLOBALS['form_errors'], $this->errors);
			
		}
		
		return $this->is_valid;		
	
	}
	
	/**
	 * Adds an error to $errors class property.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $field required
	 * @param string $val required
	 * @param string $msg required
	 */
	
	public function add_to_error($field, $val, $msg)
	{
		$this->errors[$field]['value'] = $val;	
		$this->errors[$field]['message'][] = $this->message_check($field, $val, $msg);
	}
	
	/**
	 * Gets a fields error message(s).
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $field required
	 * @param string $glue optional default set to ", "
	 * @return string
	 */
	
	public function display_error_message($field, $glue = ", ")
	{
		return implode("{$glue}", $this->errors[$field]['message']);
	}
	
	/**
	 * Check if a field has an error
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $field required
	 * @return bool
	 */
	
	public function has_error($field)
	{
		return in_array($field, array_keys($this->errors));
	}
	
	/**
	 * Removes slashes and uppercase. Used for field names.
	 *
	 * @author Nesbert Hidalgo
	 * @param string $name required
	 * @return string 
	 */
	
	private function format_name($name)
	{
		return	ucwords(str_replace('_', ' ', strtolower($name)));
	}
	
	/**
	 * Replace keywords in error messages.
	 * 
	 * @@field_name@@ = formatted field name
	 * @@value@@ = current value of the field being validated
	 
	 * <code>
	 * $this->validate_email('email', ( $this->email ? '@@value@@ is not a valid email address.' : 'E-mail is a required field.' ), true);
	 * </code>
	 *
	 * @author Nesbert Hidalgo
	 * @access private
	 * @param string $field required
	 * @param mixed $val required
	 * @param string $msg required
	 * @return string 
	 */
	
	private function message_check($field, $val, $msg)
	{
		foreach ( $this->replace_needles as $pattern ) {
		
			switch ( $pattern ) {
				case self::FIELD_NAME:
					$replace = $this->format_name($field);
				break;
				case self::VALUE:
					$replace = $val;
				break;
			}
			
			$msg = str_replace($pattern, $replace, $msg);
			
		}		
		return $msg;
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
			case ( !$required && !$val):
				return true;
			break;
			
			default:
				$this->add_to_error($field, $val, $msg);
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
	
	public function validate_field_by_regex($regex, $field, $val, $msg = null, $required = false)
	{

		// regular expression does not start or end with a forward slash add slashes
		$regex = ( $regex{0} != '/' ? '/'.$regex : $regex );
		$regex = ( $regex{strlen($regex)-1} != '/' ? $regex.'/' : $regex );
		
		return $this->validate_field_by_bool(preg_match($regex, $val), $field, $val, $msg, $required);
		
	}
	
	/**
	 * Add to errors.
	 * 
	 * @author Nesbert Hidalgo
	 * @access private
	 * @param string $field required
	 * @param mixed $val required
	 * @param string $msg optional
	 * @return bool 
	 */
	
	public function validate_add_to_error($field, $val, $msg = null)
	{
		$msg = $msg ? $msg : self::FIELD_NAME." is invalid.";
		$this->add_to_error($field, $val, $msg);
		return false;
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
		$msg = $msg ? $msg : self::FIELD_NAME." is a required field.";
		$val = trim($val);

		if ( $val ) {
			return true;
		} else {
			$this->add_to_error($field, $val, $msg);
			return false;
		}		
	}

	public function validate_confirmation_of($field, $val, $val2, $msg = null)
	{
		$msg = $msg ? $msg : self::FIELD_NAME." doesn't match.";

		if ($val == $val2) {
			return true;
		} else {
			$this->add_to_error($field, $val, $msg);
			return false;
		}		
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
		$msg = $msg ? $msg : self::FIELD_NAME." is not a number.";
		return $this->validate_field_by_regex(self::REGEX_NUMBER, $field, $val, $msg, $required);
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
		$msg = $msg ? $msg : self::FIELD_NAME." is an invalid email address.";
		return $this->validate_field_by_regex(self::REGEX_EMAIL_ADDRESS, $field, $val, $msg, $required);
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
		$msg = $msg ? $msg : self::FIELD_NAME." must be a valid web address.";
		return $this->validate_field_by_regex(self::REGEX_WEB_URL, $field, $val, $msg, $required);
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
		$msg = $msg ? $msg : self::FIELD_NAME." is an invalid picture format.";
		return $this->validate_field_by_regex(self::REGEX_PICTURE, $field, $val, $msg, $required);
	}
	
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
		$msg = $msg ? $msg : self::FIELD_NAME." is invalid.";
		return $this->validate_field_by_regex($pattern, $field, $val, $msg, $required);
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
		$msg = $msg ? $msg : "Enter a number only with no decimals for ".self::FIELD_NAME.".";
		return $this->validate_field_by_bool(( is_numeric($val) && !strstr($val, '.') ), $field, $val, $msg, $required = false);
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