<?php
/**
 * Extend functionality of a string data type.
 *
 * @package     Creovel
 * @subpackage  Prototype
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.4.0
 * @author      Nesbert Hidalgo
 **/
class CreovelString extends Prototype
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
    public function camelize()
    {
        return camelize($this->value);
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
     * Replaces every instance of the underscore ("_") or space (" ")
     * character by a dash ("-").
     *
     * @return string
     **/
    public function contains($str)
    {
        return (bool) strstr($this->value, $str);
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
    
    public function ends_with($str)
    {
        return substr($this->value, -strlen($str)) == $str;
    }
    
    public function escape_HTML()
    {
        return htmlentities($this->value);
    }
    
    public function gsub($pattern, $replace)
    {
        return preg_replace(
                        (is_regex($pattern) ? $pattern : "~{$pattern}~"),
                        $replace,
                        $this->value
                        );
    }
    
    public function is_empty()
    {
        return empty($this->value);
    }
    
    public function starts_with($str)
    {
        return substr($this->value, 0, strlen($str)) == $str;
    }
    
    public function strip()
    {
        return trim($this->value);
    }
    
    public function strip_scripts($tag = 'script')
    {
        return preg_replace('/<'.$tag.'(|\W[^>]*)>(.*)<\/'. $tag .'>/iusU', '', $this->value);
    }
    
    public function strip_tags($allowed_tags = null)
    {
        return strip_tags($this->value, $allowed_tags);
    }
    
    public function sub($pattern, $replace, $times = 1)
    {
        return preg_replace(
                        (is_regex($pattern) ? $pattern : "~{$pattern}~"),
                        $replace,
                        $this->value,
                        $times);
    }
    
    public function times($count)
    {
        for ($i = 0; $i < $count; $i++ ) $str .= $this->value;
        return $str;
    }
    
    public function to_array()
    {
        $array = array();
        for ($i = 0; $i < $this->length(); $i++ ) $array[] = $this->value{$i};
        return $array;
    }
    
    public function to_json()
    {
        return '"'.escape_javascript($this->value).'"';
    }
    
    public function truncate($length = 30, $tail = '...', $strict = false)
    {
        return truncate($this->value, $length, $tail, $strict);
    }
    
    public function underscore()
    {
        return underscore($this->value);
    }
    
    public function unescape_html()
    {
        return html_entity_decode($this->value);
    }
} // END CreovelString extends Prototype