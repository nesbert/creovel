<?

/*

Class: validation
	Validation class.

*/

class validation
{
	const FIELD_NAME = '@@field_name@@';
	const VALUE = '@@value@@';

	/*
	
	Function: __construct

	Parameters:
		errors - array

	*/

	public function __construct(&$errors  = null)
	{
		if ($errors) {
			$this->errors = $errors;
		} else {
			$this->errors = new error('model');
		}
	}
	
	
	/*
	
	Function: has_errors
		Test for errors

	Returns:
		bool
		
	*/
	 
	public function has_errors() {
		return $this->errors->has_errors();
	}
	
	/*
	
	Function: add_error
		Manually add error

	Parameters:	
		field - required
		msg - required
	 
	*/
	 
	public function add_error($feild, $msg) {
		$this->errors->add($field, $msg);
	}
	
	/*
	
	Function:
		Base function to validate by boolean value.

	Parameters:	
		bool - required
		field - required
		val - required
		msg - optional
		required - optional
	
	Returns:
		bool

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
	
	/*

	Function:		
		Format error message by adding fieldname or value we needed

	Parameters:	
		field - required
		val - required
		msg - required
		default_msg - required

	Returns:
		string

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
	
	/*
	
	Function: validates_presence_of
		Validates that $val is not empty.

	Parametes:	
		field - required
		val - required
		msg - optional default is "... is a required field."

	Returns:
		bool

	*/

	public function validates_presence_of($field, $val, $msg = null)
	{
		return self::validate_field_by_bool(true, $field, trim($val), self::format_message($field, $val, $msg, self::FIELD_NAME." is a required field."), true);
	}

	/*
	
	Function: validates_format_of	
		Validates $val with a regular expression $pattern using preg_match().

	Parameters:	
		field - required
		val - required
		msg - optional default is "... is invalid."
		required - default is false
		pattern - required regular expression

	Returns:
		bool

	*/

	public function validates_format_of($field, $val, $msg = null, $required = false, $pattern = false)
	{
		return self::validate_field_by_bool(preg_match($pattern, $val), $field, $val, self::format_message($field, $val, $msg, self::FIELD_NAME." is invalid."), $required);
	}
	
	/*

	Function validate_email_of
		Validates that $val is a valid email address.

	Parameters:	
		field - required
		val - required
		msg - optional default is "... is an invalid email address."
		required - optional default is false

	Returns:
		bool

	*/

	public function validates_email_of($field, $val, $msg = null, $required = false)
	{
		return self::validate_field_by_bool(is_email($val), $field, $val, self::format_message($field, $val, $msg, self::FIELD_NAME." is an invalid email address."), $required);
	}
	
	/*
			
	Function: validates_numericality_of
		Validates that $val is numeric.

	Parameters:	
		field - required
		val - required
		msg - optional default is "... is not a number."
		required - optional default is false

	Returns:
		bool

	*/	

	public function validates_numericality_of($field, $val, $msg = null, $required = false)
	{
		return self::validate_field_by_bool(is_number($val), $field, $val, self::format_message($field, $val, $msg, self::FIELD_NAME." is not a number."), $required);
	}
}

?>
