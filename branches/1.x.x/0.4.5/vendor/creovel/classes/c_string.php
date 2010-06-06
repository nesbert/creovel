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
class CString extends CObject
{
    /**
     * Returns a pluralized version of a $word.
     *
     * @param string $word
     * @param integer $count
     * @return string
     * @author Nesbert Hidalgo
     **/
    public function pluralize($word, $count = null)
    {
        if ($count == 1) return $word;
        return Inflector::pluralize($word);
    }

    /**
     * Returns a singularized verision of a $word.
     *
     * @param string $word
     * @return string
     * @author Nesbert Hidalgo
     **/
    public function singularize($word)
    {
        return Inflector::singularize($word);
    }

    /**
     * Transform text like 'programmers_field' to 'Programmers Field'.
     *
     * @param string $word
     * @param boolean $ucwords
     * @return string
     * @author Nesbert Hidalgo
     **/
    public function humanize($word, $ucwords = false)
    {
        return Inflector::humanize($word, $ucwords);
    }

    /**
     * Transform text like 'programmers_field' to 'ProgrammersField'.
     *
     * @param string $word
     * @param boolean $lower_first_char
     * @return string
     * @author Nesbert Hidalgo
     **/
    public function camelize($word, $lower_first_char = false)
    {
        return Inflector::camelize($word, $lower_first_char);
    }

    /**
     * Replaces every instance of the underscore ("_") or space (" ")
     * character by a dash ("-").
     *
     * @param string $word
     * @param string $separator
     * @return string
     * @author Nesbert Hidalgo
     **/
    public function dasherize($word, $separator = '-')
    {
        return Inflector::underscore($word, $separator);
    }

    /**
     * Transforms text like 'ProgrammersField' to 'programmers_field'.
     *
     * @param string $word
     * @return string
     * @author Nesbert Hidalgo
     **/
    public function underscore($word)
    {
        return Inflector::underscore($word);
    }

    /**
     * Transforms text to 'ClassName'.
     *
     * @param string $word
     * @return string
     * @author Nesbert Hidalgo
     **/
    public function classify($word)
    {
        return Inflector::classify($word);
    }

    /**
     * Converts a class name to its table name according to rails naming
     * conventions. Converts "Person" to "people"
     *
     * @param string $class_name
     * @see classify()
     * @return string
     * @author Nesbert Hidalgo
     **/
    public function tableize($class_name)
    {
        return Inflector::tableize($class_name);
    }

    /**
     * Create a path string from class name. This method converts
     * API_PersonSubclass to api/person_subclass
     *
     * @param string $class_name
     * @return string
     * @author Nesbert Hidalgo
     **/
    public function patherize($class_name)
    {
        return Inflector::patherize($class_name);
    }

    /**
     * Helpful for alternating between between two values during a loop.
     *
     * <code>
     * <tr class="<?=CString::cycle('data_alt1', 'data_alt2')?>">
     * <tr class="data_alt<?=CString::cycle()?>">
     * </code>
     *
     * @param string $var1
     * @param string $var2
     * @param boolean $reset
     * @return mixed Returns 1 & 2 in to strings passed
     * @author Nesbert Hidalgo
     **/
    public function cycle($var1 = '', $var2 = '', $reset = false)
    {
        static $c;
        if (empty($c) || $reset) $c = 1;
        $var1 = $var1 ? $var1 : 1;
        $var2 = $var2 ? $var2 : 2;
        return CValidate::odd($c++) ? $var1 : $var2;
    }

    /**
     * Replace every " (quote) with its html equevelant.
     *
     * @param string $str
     * @return string
     * @author Nesbert Hidalgo
     **/
    public function quote2string($str)
    {
        return str_replace("\"", "&quot;", $str);
    }

    /**
     * Replace every character of a string with $mask
     *
     * @param string $str
     * @param string $mask Optional default set to '*'
     * @return string
     * @author Nesbert Hidalgo
     **/
    public function mask($str, $mask = '*')
    {
        return str_repeat($mask, strlen($str));
    }

    /**
     * Truncates a string and adds trailing periods to it. Now handles
     * words better thank you Mel Cruz for the suggestion. By default
     * truncates at end of words.
     *
     * @param string $str
     * @param integer $length Optional default set to 100 characters
     * @param string $tail Optional default set to '...'
     * @param boolean $strict Optional default false truncate at exact $length
     * @return string
     * @author Nesbert Hidalgo
     **/
    public function truncate($str, $length = 100, $tail = '...',
    $strict = false) {
        if (!$strict) $str = trim($str);

        if ( strlen($str) >= $length ) {
            if ($strict) {
                $str = trim(substr_replace($str, '', ($length - strlen($tail))));
            } else {
                if ( $length > 1 ) $offset = strpos($str, " ", $length - 1);
                $str = substr_replace($str, '', ( $offset ? $offset : $length) );
            }
            $str .= $tail;
        }

        return $str;
    }

    /**
     * Reformats a string to fit within a display with a certain
     * number of columns.  Words are moved between the lines as
     * necessary.  Particularly useful for formatting text to
     * be sent via email (prevents nasty wrap-around problems).
     *
     * Credit: syneryder@namesuppressed.com
     *
     * @param string $s The string to be formatted
     * @param integer $l The maximum length of a line
     * @return string
     * @author Russ Smith
     **/
    public function wordwrap_line($s, $l)
    {
        $tok = strtok($s, " ");
        $formatted = '';
        $line = '';

        while (strlen($tok) != 0) {
            if (strlen($line) + strlen($tok) < ($l + 2) ) {
                $line .= " $tok";
            } else {
                $formatted .= "$line\n";
                $line = $tok;
            }
            $tok = strtok(" ");
        }

        $formatted .= $line;

        return trim($formatted);
    }

    /**
     * Retrieve a number from a string.
     *
     * @param string $str
     * @return float
     * @author Nesbert Hidalgo
     **/
    public function retrieve_number($str)
    {
        return floatval(preg_replace('/[^0-9.-]/', '', $str));
    }

    /**
     * Checks if the string starts with $needle.
     *
     * @param string $needle
     * @param string $haystack
     * @return string
     * @author Nesbert Hidalgo
     **/
    public function starts_with($needle, $haystack)
    {
        return substr($haystack, 0, strlen($needle)) == $needle;
    }

    /**
     * Checks if the string ends with $needle.
     *
     * @param string $needle
     * @param string $haystack
     * @return string
     * @author Nesbert Hidalgo
     **/
    public function ends_with($needle, $haystack)
    {
        return substr($haystack, -strlen($needle)) == $needle;
    }

    /**
     * Convert a number to word representation.
     *
     * @param integer $num
     * @param boolean $money
     * @param boolean $caps
     * @return string
     * @link http://us.php.net/manual/en/function.number-format.php#66895
     **/
    public function num2words($num, $money = false, $caps = false, $c = 1)
    {
        $ZERO = 'zero';
        $MINUS = 'minus';
        $lowName = array(
            /* zero is shown as "" since it is never used in combined forms */
            /* 0 .. 19 */
            "", "one", "two", "three", "four", "five",
            "six", "seven", "eight", "nine", "ten",
            "eleven", "twelve", "thirteen", "fourteen", "fifteen",
            "sixteen", "seventeen", "eighteen", "nineteen");

        $tys = array(
            /* 0, 10, 20, 30 ... 90 */
            "", "", "twenty", "thirty", "forty", "fifty",
            "sixty", "seventy", "eighty", "ninety");

        $groupName = array(
            /* We only need up to a quintillion, since a long is about 9 * 10 ^ 18 */
            /* American: unit, hundred, thousand, million, billion, trillion, quadrillion, quintillion */
            "", "hundred", "thousand", "million", "billion",
            "trillion", "quadrillion", "quintillion");

        $divisor = array(
            /* How many of this group is needed to form one of the succeeding group. */
            /* American: unit, hundred, thousand, million, billion, trillion, quadrillion, quintillion */
            100, 10, 1000, 1000, 1000, 1000, 1000, 1000) ;

        $num = str_replace(",","",$num);
        $num = number_format($num,2,'.','');
        $cents = substr($num,strlen($num)-2,strlen($num)-1);
        $num = (int)$num;

        $s = "";

        if ( $num == 0 ) $s = $ZERO;
        $negative = ($num < 0 );
        if ( $negative ) $num = -$num;

        // Work least significant digit to most, right to left.
        // until high order part is all 0s.
        for ( $i=0; $num>0; $i++ ) {
            $remdr = (int)($num % $divisor[$i]);
            $num = $num / $divisor[$i];

            // check for 1100 .. 1999, 2100..2999, ... 5200..5999
            // but not 1000..1099,  2000..2099, ...
            // Special case written as fifty-nine hundred.
            // e.g. thousands digit is 1..5 and hundreds digit is 1..9
            // Only when no further higher order.
            if ( $i == 1 /* doing hundreds */ && 1 <= $num && $num <= 5 ) {
                if ( $remdr > 0 ) {
                    $remdr = ($num * 10);
                    $num = 0;
                } // end if
            } // end if
            if ( $remdr == 0 ){
                continue;
            }
            $t = "";
            if ( $remdr < 20 ){
                $t = $lowName[$remdr];
            } else if ( $remdr < 100 ) {
                $units = (int)$remdr % 10;
                $tens = (int)$remdr / 10;
                $t = $tys [$tens];
                if ( $units != 0 ) {
                    $t .= "-" . $lowName[$units];
                }
            } else {
                $t = self::num2words($remdr, $money, $caps, 0);
            }
            $s = $t." ".$groupName[$i]." ".$s;
            $num = (int)$num;
        } // end for
        $s = trim($s);
        if ( $negative ) {
            $s = $MINUS . " " . $s;
        }

        if ($c == 1) {
            if ($money) {
                $s .= " dollars and $cents cents";
            } else {
                $s .= " and $cents/100";
            }
        }

        return $caps ? ucwords($s) : $s;
    }

    /**
     * Escape a string without connecting to a DB.
     *
     * @return string
     * @link http://www.gamedev.net/community/forums/topic.asp?topic_id=448909
     **/
    public function escape_string($str)
    {
        $search = array("\x00",	"\n", "\r", "\\", "'", "\"", "\x1a");
        $replace = array("\\x00", "\\n", "\\r", "\\\\" ,"\'", "\\\"", "\\\x1a");
        return strtr(str_replace($search, $replace, $str), array(
            "\x00" => '\x00',
            "\n" => '\n', 
            "\r" => '\r', 
            '\\' => '\\\\',
            "'" => "\'", 
            '"' => '\"', 
            "\x1a" => '\x1a'
            ));
    }

    /** 
     * Split a string into groups of words with a line no longer than $max 
     * characters. 
     * 
     * @param string $string 
     * @param integer $max 
     * @return array 
     * @author Nesbert Hidalgo
     * @link http://us.php.net/manual/en/function.preg-split.php#95924
     **/ 
    public function split_words($string, $max = 1) 
    { 
        $words = preg_split('/\s/', $string); 
        $lines = array(); 
        $line = ''; 

        foreach ($words as $k => $word) { 
            $length = strlen($line . ' ' . $word); 
            if ($length <= $max) { 
                $line .= ' ' . $word; 
            } else if ($length > $max) { 
                if (!empty($line)) $lines[] = trim($line); 
                $line = $word; 
            } else { 
                $lines[] = trim($line) . ' ' . $word; 
                $line = ''; 
            } 
        } 
        $lines[] = ($line = trim($line)) ? $line : $word; 

        return $lines; 
    }
    
    /**
     * Returns a string with the first character of value capitalized, if that
     * character is alphabetic. 
     *
     * @param string $string
     * @return string
     **/
    public function capitalize($string)
    {
        return ucfirst($string);
    }
    
    /**
     * Checks the occurrence of $needle in $haystack.
     *
     * @param string $needle
     * @param string $haystack
     * @return boolean
     **/
    public function contains($needle, $haystack)
    {
        return CValidate::in_string($needle, $haystack);
    }
    
    /**
     * Converts HTML special characters to their entity equivalents.
     *
     * @param string $string
     * @return string
     **/
    public function escape_HTML($string)
    {
        return htmlentities($string);
    }
    
    /**
     * Returns the string with every occurrence of a given pattern replaced by
     * either a regular string, the returned value of a function or a Template
     * string. The pattern can be a string or a regular expression.
     *
     * @param string $string
     * @return string
     **/
    public function gsub($string, $pattern, $replace)
    {
        return preg_replace(
                        (CValidate::regex($pattern)
                            ? $pattern
                            : "~{$pattern}~"),
                        $replace,
                        $string
                        );
    }
    
    /**
     * Checks if the string is empty.
     *
     * @param string $string
     * @return boolean
     **/
    public function is_empty($string)
    {
        return empty($string);
    }
    
    /**
     * Strips all leading and trailing whitespace from a string.
     *
     * @param string $string
     * @return string
     **/
    public function strip($string)
    {
        return trim($string);
    }
    
    /**
     * Strips a string of anything that looks like an HTML script block
     *
     * @param string $string
     * @return string
     **/
    public function strip_scripts($string, $tag = 'script')
    {
        return preg_replace('/<'.$tag.'(|\W[^>]*)>(.*)<\/'. $tag .'>/iusU',
                    '', $string);
    }
    
    /**
     * Strip HTML and PHP tags from a string.
     *
     * @param string $string
     * @return string
     **/
    public function strip_tags($string, $allowed_tags = null)
    {
        return strip_tags($string, $allowed_tags);
    }
    
    /**
     * Returns a string with the first count occurrences of pattern
     * replaced by either a regular string, the returned value of a 
     * function or a Template string. Pattern can be a string or a 
     * regular expression.
     *
     * @param string $string
     * @return string
     **/
    public function sub($string, $pattern, $replace, $times = 1)
    {
        return preg_replace(
                        (CValidate::regex($pattern)
                            ? $pattern
                            : "~{$pattern}~"),
                        $replace,
                        $string,
                        $times);
    }
    
    /**
     * Repeat a string N ($count) amount of times.
     *
     * @param string $string
     * @return string
     **/
    public function times($string, $count)
    {
        return str_repeat($string, $count);
    }
    
    /**
     * Splits the string character-by-character and returns an array with
     * the result.
     *
     * @param string $string
     * @return array
     **/
    public function to_array($string)
    {
        return preg_split('//', $string, -1, PREG_SPLIT_NO_EMPTY);
    }
    
    /**
     * Cleans up javascript and properly escapes quotes.
     *
     * @param string $javascript
     * @return string
     * @author Nesbert Hidalgo
     **/
    public function prep_javascript($javascript)
    {
        $escape = array(
            "\r\n"  => '\n',
            "\r"    => '\n',
            "\n"    => '\n',
            '"'     => '\"',
            "'"     => "\\'"
        );
        return str_replace(array_keys($escape),
                array_values($escape), $javascript);
    }
    
    /**
     * Strips tags and converts the entity forms of special HTML characters to
     * their normal form.
     *
     * @param string $string
     * @return string
     **/
    public function unescape_html($string)
    {
        return html_entity_decode($string);
    }
} // END CString extends CObject