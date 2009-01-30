<?php
/**
 * Text & String functions.
 *
 * @package     Creovel
 * @subpackage  Creovel.Helpers
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.1.0
 **/

/**
 * Returns a pluralized version of a $word.
 *
 * @param string $word
 * @param integer $count
 * @return string
 * @author Nesbert Hidalgo
 **/
function pluralize($word, $count = null)
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
function singularize($word)
{
    return Inflector::singularize($word);
}

/**
 * Transform text like 'programmers_field' to 'Programmers Field'.
 *
 * @param string $word
 * @return string
 * @author Nesbert Hidalgo
 **/
function humanize($word)
{
    return Inflector::titleize($word);
}

/**
 * Transform text like 'programmers_field' to 'ProgrammersField'.
 *
 * @param string $word
 * @return string
 * @author Nesbert Hidalgo
 **/
function camelize($word)
{
    return Inflector::camelize($word);
}

/**
 * Transforms text like 'ProgrammersField' to 'programmers_field'.
 *
 * @param string $word
 * @return string
 * @author Nesbert Hidalgo
 **/
function underscore($word)
{
    return Inflector::underscore($word);
}

/**
 * Helpful for alternating between between two values during a loop.
 *
 * <code>
 * <tr class="<?=cycle('data_alt1', 'data_alt2')?>">
 * <tr class="data_alt<?=cycle()?>">
 * </code>
 *
 * @param string $var1
 * @param string $var2
 * @return mixed Returns 1 & 2 in to strings passed
 * @author Nesbert Hidalgo
 **/
function cycle($var1 = '', $var2 = '')
{
    static $return;
    $var1 = $var1 ? $var1 : 1;
    $var2 = $var2 ? $var2 : 2;
    $return = $return == $var2 || !$return ? $var1 : $var2;
    return $return;
}

/**
 * Replace every " (quote) with its html equevelant.
 *
 * @param string $str
 * @return string
 * @author Nesbert Hidalgo
 **/
function quote2string($str)
{
    return str_replace("\"", "&quot;", $str);
}

/**
 * Replace every charactor of a string with $mask
 *
 * @param string $str
 * @param string $mask Optional default set to '*'
 * @return string
 * @author Nesbert Hidalgo
 **/
function mask($str, $mask = '*')
{
    $return = '';
    for ($i = 0; $i <= (strlen($str) - 1); $i++) $return .= $mask;
    return $return;
}

/**
 * Truncates a string and adds trailing periods to it. Now handles
 * words better thank you Mel Cruz for the suggestion. By default
 * trucates at end of words.
 *
 * @param string $str
 * @param integer $length Optional default set to 100 characters
 * @param string $tail Optional default set to '...'
 * @param boolean $strict Optional default false truncate at exact $length
 * @return string
 * @author Nesbert Hidalgo
 **/
function truncate($str, $length = 100, $tail = '...', $strict = false)
{
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
function wordwrap_line($s, $l)
{
    $tok = strtok($s, " ");
    $formatted = '';
    
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
function retrieve_number($str)
{
    return floatval(preg_replace('/[^0-9.-]/', '', $str));
}