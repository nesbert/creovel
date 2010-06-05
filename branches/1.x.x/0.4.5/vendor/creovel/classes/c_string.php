<?php
/**
 * Extend functionality of a string data type.
 *
 * @package     Creovel
 * @subpackage  Core
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.0
 * @author      Nesbert Hidalgo
 **/
class CString extends CData
{
    /**
     * String value.
     *
     * @var string
     **/
    public $value;
    
    /**
     * Set value.
     *
     * @return void
     **/
    public function __construct($value)
    {
        $this->value = $value;
    }
    
    /**
     * Camelize value.
     *
     * @return string
     * @see Inflector::camelize()
     **/
    public function camelize($lower_first = false)
    {
        return Inflector::camelize($this->value, $lower_first);
    }
    
    /**
     * Returns a string with the first character of value capitalized, if that
     * character is alphabetic. 
     *
     * @return string
     **/
    public function capitalize()
    {
        return ucfirst($this->value);
    }
    
    /**
     * Checks the occurrence of $str in value.
     *
     * @return boolean
     **/
    public function contains($str)
    {
        return CValidate::in_string($str, $this->value);
    }
    
    /**
     * Replaces every instance of the underscore ("_") or space (" ")
     * character by a dash ("-").
     *
     * @return string
     **/
    public function dasherize()
    {
        return dasherize($this->value);
    }
    
    /**
     * Checks if the string ends with $str.
     *
     * @return string
     **/
    public function ends_with($str)
    {
        return ends_with($str, $this->value);
    }
    
    /**
     * Converts HTML special characters to their entity equivalents.
     *
     * @return string
     **/
    public function escape_HTML()
    {
        return htmlentities($this->value);
    }
    
    /**
     * Returns the string with every occurrence of a given pattern replaced by
     * either a regular string, the returned value of a function or a Template
     * string. The pattern can be a string or a regular expression.
     *
     * @return string
     **/
    public function gsub($pattern, $replace)
    {
        return preg_replace(
                        (CValidate::regex($pattern)
                            ? $pattern
                            : "~{$pattern}~"),
                        $replace,
                        $this->value
                        );
    }
    
    /**
     * Checks if the string is empty.
     *
     * @return boolean
     **/
    public function is_empty()
    {
        return empty($this->value);
    }
    
    /**
     * Checks if the string starts with $str.
     *
     * @return string
     **/
    public function starts_with($str)
    {
        return starts_with($str, $this->value);
    }
    
    /**
     * Strips all leading and trailing whitespace from a string.
     *
     * @return string
     **/
    public function strip()
    {
        return trim($this->value);
    }
    
    /**
     * Strips a string of anything that looks like an HTML script block
     *
     * @return string
     **/
    public function strip_scripts($tag = 'script')
    {
        return preg_replace('/<'.$tag.'(|\W[^>]*)>(.*)<\/'. $tag .'>/iusU',
                    '', $this->value);
    }
    
    /**
     * Strip HTML and PHP tags from a string.
     *
     * @return string
     **/
    public function strip_tags($allowed_tags = null)
    {
        return strip_tags($this->value, $allowed_tags);
    }
    
    /**
     * Returns a string with the first count occurrences of pattern replaced by
     * either a regular string, the returned value of a function or a Template
     * string. Pattern can be a string or a regular expression.
     *
     * @return string
     **/
    public function sub($pattern, $replace, $times = 1)
    {
        return preg_replace(
                        (CValidate::regex($pattern)
                            ? $pattern
                            : "~{$pattern}~"),
                        $replace,
                        $this->value,
                        $times);
    }
    
    /**
     * Repeat a string N ($count) amount of times.
     *
     * @return string
     **/
    public function times($count)
    {
        for ($i = 0; $i < $count; $i++ ) $str .= $this->value;
        return $str;
    }
    
    /**
     * Splits the string character-by-character and returns an array with
     * the result.
     *
     * @return array
     **/
    public function to_array()
    {
        $array = array();
        for ($i = 0; $i < $this->length(); $i++ ) $array[] = $this->value{$i};
        return $array;
    }
    
    /**
     * Create a JSON string with current value.
     *
     * @return string
     **/
    public function to_json()
    {
        return '"'.escape_javascript($this->value).'"';
    }
    
    /**
     * Truncates a string and adds trailing periods to it. By default
     * truncates at end of words.
     *
     * @param integer $length Optional default set to 100 characters
     * @param string $tail Optional default set to '...'
     * @param boolean $strict Optional default false truncate at exact $length
     * @return string
     **/
    public function truncate($length = 30, $tail = '...', $strict = false)
    {
        return truncate($this->value, $length, $tail, $strict);
    }
    
    /**
     * Converts a camelized, spaced, dashed string into a series of words
     * separated by an underscore ("_").
     *
     * @return string
     **/
    public function underscore()
    {
        return underscore($this->value);
    }
    
    /**
     * Strips tags and converts the entity forms of special HTML characters to
     * their normal form.
     *
     * @return string
     **/
    public function unescape_html()
    {
        return html_entity_decode($this->value);
    }
} // END CString extends CData