<?php
/*
	Class: validation
	
	Validation class.
*/

class validation
{
	const FIELD_NAME = '@@field_name@@';
	const VALUE = '@@value@@';
	
	// Section: Public
	
	/*
		Function: __construct
		
		Parameters:
		
			errors - Array of errors.
	*/
	
	public function __construct(&$errors = null)
	{
		if ($errors) {
			$this->errors = $errors;
		} else {
			$this->errors = new error('model');
		}
	}
	
	/*
		Function: has_errors
		
		Test for errors.
		
		Returns:
		
			Boolean.
	*/
	 
	public function has_errors()
	{
		return $this->errors->has_errors();
	}
	
	/*
		Function: add_error
		
		Manually add error.
		
		Parameters:
		
			field - required
			message - required
	*/
	 
	public function add_error($field, $message)
	{
		$this->errors->add($field, $message);
	}
	
	/*
		Validation Functions
		
		All function parameters need to follow this order.
		
		Parameters:
		
			field - field name
			value - field value
			message - error message
			required - is field required?
			option1 - optional parameter
			option2 - optional parameter
			option3 etc...
	*/
	
	/*
		Function: validates_acceptance_of
		
		Validates the user agreed to something.
		
		Parameters:
		
			field - field name
			value - field value
			message - optional default is "... must be accepted."
			
		Returns:
		
			Boolean.
	*/
	
	public function validates_acceptance_of($field, $value, $message = null)
	{
		return self::validate_field_by_bool(($value == '1'), $field, $value, self::format_message($field, $value, $message, self::FIELD_NAME." must be accepted."), true);
	}
	
	/*
		Function: validates_confirmation_of
		
		Validates that two fields are equal (like a password field). Second field has the name of
		attribute with _confirmation appended. Second field need not be stored in the database.
		
		Parameters:
		
			field - field name
			value - first value
			message - optional default is "... doesn't match confirmation."
			required - optional default is true
			value2 - second value
			
		Returns:
		
			Boolean.
	*/
	
	public function validates_confirmation_of($field, $value, $message = null, $required = true, $value2 = null)
	{
		// no value2 get from params
		if ( $value2 === null ) $value2 = get_params($field . '_confirmation');
		return self::validate_field_by_bool(is_match($value, $value2), $field, $value, self::format_message($field, $value, $message, self::FIELD_NAME." doesn't match confirmation."), $required);
	}
	
	/*
		Function validates_email_format_of
		
		Validates that $value is a valid email address.
		
		Parameters:	
		
			field - required
			value - required
			message - optional default is "... is an invalid email address."
			required - optional default is false
			
		Returns:
		
			Boolean.
	*/
	
	public function validates_email_format_of($field, $value, $message = null, $required = false)
	{
		return self::validate_field_by_bool(is_email($value), $field, $value, self::format_message($field, $value, $message, self::FIELD_NAME." is an invalid email address."), $required);
	}
	
	/*
		Function: validates_format_of	
		
		Validates $value with a regular expression $pattern using preg_match().
		
		Parameters:	
		
			field - required
			value - required
			message - optional default is "... is invalid."
			required - default is false
			pattern - required regular expression
			
		Returns:
		
			Boolean.
	*/
	
	public function validates_format_of($field, $value, $message = null, $required = false, $pattern)
	{
		return self::validate_field_by_bool(preg_match($pattern, $value), $field, $value, self::format_message($field, $value, $message, self::FIELD_NAME." is invalid."), $required);
	}

	/*
		Function: validates_presence_of
		
		Validates that $value is not empty.
		
		Parametes:	
			
			field - required
			value - required
			message - optional default is "... is a required field."
			
		Returns:
		
			Boolean.
	*/
	
	public function validates_presence_of($field, $value, $message = null)
	{
		return self::validate_field_by_bool(!empty($value), $field, trim($value), self::format_message($field, $value, $message, self::FIELD_NAME." is a required field."), true);
	}
	
	/*
		Function: validates_numericality_of
		
		Validates that $val is numeric.
		
		Parameters:	
			
			field - required
			value - required
			message - optional default is "... is not a number."
			required - optional default is false
			only_integer - optional default is false
		
		Returns:
		
			Boolean.
	*/
	
	public function validates_numericality_of($field, $value, $message = null, $required = false, $only_integer = false)
	{
		return self::validate_field_by_bool(($only_integer ? is_int($value) : is_number($value)), $field, $value, self::format_message($field, $value, $message, self::FIELD_NAME." is not a number."), $required);
	}
	
	/*
		Function: validates_length_of
		
		Validates that $val is numeric.
		
		Parameters:	
			
			field - required
			value - required
			options/message - options array or default error message
				$sample_options = array(
					'range'			=> '5..23',			// The length must be in range from 5 to 23. Key can also be 'in' or 'within'
					'is'			=> 6,				// Value must be integer character long
					'minimum'		=> 3,				// Value may not be less than the integer characters long
					'maximum'		=> 10,				// Value may not be more than the integer characters long
					'message'		=> 'Must be...',	// Default error message.
					'required'		=> false,			// Boolean value for is field required
					'too_long'		=> 'Too long...',	// Synonym for message when maximum is used
					'too_short'		=> 'Too short...',	// Synonym for message when miniimum is used
					'wrong_length'	=> 'Wrong ...',		// Synonym for message when range is used
				);
			required - optional default is false
			minimum - optional default is 0
			maximum - optional default is 50
			
		Returns:
		
			Boolean.
	*/	
	
	public function validates_length_of($field, $value, $options = null, $required = false, $minimum = 0, $maximum = 50)
	{
		if (!is_array($options)) {
			$options = array(
				'message' => $options,
				'required' => $required,
				'minimum' => $minimum,
				'maximum' => $maximum
			);
		}
		
		if (isset($options['in'])) $options['range'] = $options['in'];
		if (isset($options['within'])) $options['range'] = $options['within'];
		
		switch (true) {
		
			case (isset($options['is'])):
				$options['minimum'] = $options['is'];
				$options['maximum'] = $options['is'];
				$d = $options['is'];
				$message = $options['wrong_length'] ? $options['wrong_length'] : self::FIELD_NAME . " must be {$d} characters.";
			break;
			
			case (isset($options['range'])):
				$length = @explode('..', $options['range']);
				$options['minimum'] = $length[0];
				$options['maximum'] = $length[1];
				$message = self::FIELD_NAME . ' must be between ' . $options['minimum'] . ' and ' . $options['maximum'] . ' characters.';
			break;
			
			case (isset($options['minimum']) && !isset($options['maximum'])):
				$options['maximum'] = strlen($value);
				$d = $options['minimum'];
				$message = $options['too_short'] ? $options['too_short'] : self::FIELD_NAME . " must have a minimum of {$d} characters.";
			break;
			
			case (!isset($options['minimum']) && isset($options['maximum'])):
				$options['minimum'] = 0;
				$d = $options['maximum'];
				$message = $options['too_long'] ? $options['too_long'] : self::FIELD_NAME . " must have a maximum of {$d} characters.";
			break;
		
		}
		
		// set message and replace %d with minimum, maximum or exact length
		$options['message'] = str_replace('%d', $d, ( $options['message'] ? $options['message'] : $message ));
		//print_obj($options, 1);
		
		return self::validate_field_by_bool(is_between(strlen($value), $options['minimum'], $options['maximum']), $field, $value, self::format_message($field, $value, $options['message'], self::FIELD_NAME." is not a number."), $options['required']);
	}

	/*
		Function validates_url_format_of
		
		Validates that $value is a valid url address.
		
		Parameters:	
		
			field - required
			value - required
			message - optional default is "... is an invalid web address."
			required - optional default is false
			
		Returns:
		
			Boolean.
	*/
	
	public function validates_url_format_of($field, $value, $message = null, $required = false)
	{
		return self::validate_field_by_bool(is_url($value), $field, $value, self::format_message($field, $value, $message, self::FIELD_NAME." is an invalid web address."), $required);
	}
	
	// Section: Private
	
	/*
		Function: validate_field_by_bool
		
		Base function to validate by boolean value.
		
		Parameters:	
		
			bool - required
			field - required
			value - required
			message - optional
			required - optional default is false
		
		Returns:
		
			Boolean.
	*/
	
	private function validate_field_by_bool($bool, $field, $value, $message = null, $required = false)
	{
		switch ( true ) {
			case ( $required && $value && $bool ):
			case ( !$required && $value && $bool ):
			case ( !$required && !$value ):
				return true;
			break;
			
			default:
				$this->errors->add($field, $message);
				return false;
			break;
		}
	}
	
	/*
		Function: format_message
		
		Format error message by adding fieldname or value we needed
		
		Parameters:	
		
			field - required
			value - required
			message - required
			default_message - required
			
		Returns:
		
			String.
	*/
	
	private function format_message($field, $value, $message, $default_message)
	{
		$message = $message ? $message : $default_message;
		
		// check for fieldname and humanze it
		if ( strstr($message, self::FIELD_NAME) ) {
			$message = str_replace(self::FIELD_NAME, humanize($field), $message);
		}
		
		// check for value and insert it into the message
		if ( strstr($message, self::VALUE) ) {
			$message = str_replace(self::VALUE, $value, $message);
		}
		
		return $message;
	}

}
?>