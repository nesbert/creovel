<?php
/**
 * Validation class for ActiveRecord.
 *
 * @package     Creovel
 * @subpackage  Core
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.1.0
 * @author      Nesbert Hidalgo
 **/
class ActiveValidation extends CObject
{
    /**
     * Special tag for error messages.
     *
     * @var string
     **/
    const FIELD_NAME = '@@field_name@@';
    
    /**
     * Special tag for error value.
     *
     * @var string
     **/
    const VALUE = '@@value@@';
    
    /**
     * Class construct
     *
     * @param string $errors - Array of errors.
     * @return void
     **/
    public function __construct(&$errors = null)
    {
        if ($errors) {
            $GLOBALS['CREOVEL']['VALIDATION_ERRORS'] = $errors;
        }
    }
    
    /**
     * Add error to session.
     *
     * @param string $field
     * @param string $message
     * @return void
     **/
    public function add_error($field, $message)
    {
        $GLOBALS['CREOVEL']['VALIDATION_ERRORS'][$field] = $message;
    }
    
    /**
     * Count number of errors in session.
     *
     * @return integer
     **/
    public function has_errors()
    {
        return count($GLOBALS['CREOVEL']['VALIDATION_ERRORS']) >= 1;
    }
    
    /**
     * Clear errors in session.
     *
     * @return void
     **/
    public function clear_errors()
    {
        $GLOBALS['CREOVEL']['VALIDATION_ERRORS'] = array();
    }
    
    /**
     * Validation Functions
     *
     * All function parameters need to follow this order.
     *
     * Parameters:
     *
     * $field - field name
     * $value - field value
     * $message - error message
     * $required - is field required?
     * $option1 - optional parameter
     * $option2 - optional parameter
     * $option3 etc...
     */
    
    /**
     * Validates the user agreed to something.
     *
     * @param string $field - field name
     * @param mixed $value - field value
     * @param string $message - optional default is "... must be accepted."
     * @return boolean
     **/
    public function validates_acceptance_of($field, $value, $message = null)
    {
        return self::validate_field_by_bool(($value == '1'), $field, $value, self::format_message($field, $value, $message, self::FIELD_NAME." must be accepted."), true);
    }
    
    /**
     * Validates that two fields are equal (like a password field). Second field
     * has the name of attribute with _confirmation appended. Second field need
     * not be stored in the database.
     *
     * @param string $field - field name
     * @param mixed $value - field value
     * @param string $message - optional default is "... doesn't match confirmation."
     * @param boolean $required - optional default is true
     * @param string $value2 - second value
     * @return boolean
     **/
    public function validates_confirmation_of($field, $value, $message = null, $required = true, $value2 = null)
    {
        // no value2 get from params
        if ( $value2 === null ) $value2 = Creovel::params($field . '_confirmation');
        return self::validate_field_by_bool(CValidate::match($value, $value2), $field, $value, self::format_message($field, $value, $message, self::FIELD_NAME." doesn't match confirmation."), $required);
    }
    
    /**
     * Validates that $value is a valid email address.
     *
     * @param string $field - field name
     * @param mixed $value - field value
     * @param string $message - optional default is "... is an invalid
     * email address."
     * @param boolean $required - optional default is false
     * @return boolean
     **/
    public function validates_email_format_of($field, $value, $message = null, $required = false)
    {
        return self::validate_field_by_bool(CValidate::email($value), $field, $value, self::format_message($field, $value, $message, self::FIELD_NAME." is an invalid email address."), $required);
    }
    
    /**
     * Validates $value with a regular expression $pattern using preg_match().
     *
     * @param string $field - field name
     * @param mixed $value - field value
     * @param string $message - optional default is "... is invalid."
     * @param boolean $required - optional default is false
     * @param string $pattern - required regular expression
     * @return boolean
     **/
    public function validates_format_of($field, $value, $message = null, $required = false, $pattern)
    {
        return self::validate_field_by_bool(preg_match($pattern, $value), $field, $value, self::format_message($field, $value, $message, self::FIELD_NAME." is invalid."), $required);
    }
    
    /**
     * Validates that $value is not empty.
     *
     * @param string $field - field name
     * @param mixed $value - field value
     * @param string $message - optional default is "... is a required field."
     * @return boolean
     **/
    public function validates_presence_of($field, $value, $message = null)
    {
        $value = is_string($value) ? trim($value) : $value;
        return self::validate_field_by_bool(!empty($value), $field, $value, self::format_message($field, $value, $message, self::FIELD_NAME." is a required field."), true);
    }
    
    /**
     * Validates that $val is numeric.
     *
     * @param string $field - field name
     * @param mixed $value - field value
     * @param string $message - optional default is "... is not a number."
     * @param boolean $required - optional default is false
     * @param boolean $only_integer - optional default is false
     * @return boolean
     **/
    public function validates_numericality_of($field, $value, $message = null, $required = false, $only_integer = false)
    {
        
        return self::validate_field_by_bool(($only_integer ? (CValidate::number($value) && is_int((int) $value)) : CValidate::number($value)), $field, $value, self::format_message($field, $value, $message, self::FIELD_NAME." is not a number."), $required);
    }
    
    /**
     * Validates the length of $value. $options array sample:
     *
     * <code>
     * $options = array(
     *          'range'  => '5..23', // The length must be in range from 5 to 23.  Key can also be 'in' or 'within'
     *         'is'            => 6,                // Value must be integer character long
     *         'minimum'        => 3,                // Value may not be less than the integer characters long
     *         'maximum'        => 10,                // Value may not be more than the integer characters long
     *         'message'        => 'Must be...',    // Default error message.
     *         'required'        => false,            // Boolean value for is field required
     *         'too_long'        => 'Too long...',    // Synonym for message when maximum is used
     *         'too_short'        => 'Too short...',    // Synonym for message when miniimum is used
     *         'wrong_length'    => 'Wrong ...',        // Synonym for message when range is used
     *         );
     * </code>
     *
     * @param string $field - field name
     * @param mixed $value - field value
     * @param string $options/$message - options array or default error message
     * @param boolean $required - optional default is false
     * @param boolean $only_integer - optional default is false
     * @return boolean
     **/
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
        $d = '';
        $message = '';
        
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
        
        return self::validate_field_by_bool(CValidate::between(strlen($value), $options['minimum'], $options['maximum']), $field, $value, self::format_message($field, $value, $options['message'], self::FIELD_NAME." is not a number."), $options['required']);
    }
    
    /**
     * Validates that $value is a valid url address.
     *
     * @param string $field - field name
     * @param mixed $value - field value
     * @param string $message - optional default is "... is an invalid web address."
     * @param boolean $required - optional default is false
     * @return boolean
     **/
    public function validates_url_format_of($field, $value, $message = null, $required = false)
    {
        return self::validate_field_by_bool(CValidate::url($value), $field, $value, self::format_message($field, $value, $message, self::FIELD_NAME." is an invalid web address."), $required);
    }
    
    /**
     * Base function to validate by boolean value.
     *
     * @param boolean $bool
     * @param string $field
     * @param mixed $value
     * @param string $message
     * @param boolean $required - optional default is false
     * @return boolean
     **/
    public function validate_field_by_bool($bool, $field, $value, $message = null, $required = false)
    {
        switch ( true ) {
            case ( $required && $value && $bool ):
            case ( !$required && $value && $bool ):
            case ( !$required && !$value ):
                return true;
            break;
            
            default:
                self::add_error($field, $message);
                return false;
            break;
        }
    }
    
    /**
     * Format error message by adding field name or value we needed.
     *
     * @param string $field
     * @param mixed $value
     * @param string $message
     * @param string $message
     * @return string
     **/
    private function format_message($field, $value, $message, $default_message)
    {
        $message = $message ? $message : $default_message;
        
        // check for fieldname and humanze it
        if ( in_string(self::FIELD_NAME, $message) ) {
            $message = str_replace(self::FIELD_NAME, humanize($field), $message);
        }
        
        // check for value and insert it into the message
        if ( in_string(self::VALUE, $message) ) {
            $message = str_replace(self::VALUE, $value, $message);
        }
        
        return $message;
    }
} // END class ActiveValidation extends CObject