<?php
/**
 * WARNING!
 * These functions has been DEPRECATED as of 0.4.5 and have been moved
 * to the CString object. Relying on this feature is highly discouraged.
 * 
 * Text & String functions.
 *
 * @package     Creovel
 * @subpackage  Helpers
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
    return CString::pluralize($word, $count = null);
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
    return CString::singularize($word);
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
    return CString::titleize($word);
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
    return CString::camelize($word);
}

/**
 * Replaces every instance of the underscore ("_") or space (" ")
 * character by a dash ("-").
 *
 * @param string $word
 * @return string
 * @author Nesbert Hidalgo
 **/
function dasherize($word)
{
    return CString::underscore($word, '-');
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
    return CString::underscore($word);
}

/**
 * Transforms text to 'ClassName'.
 *
 * @param string $word
 * @return string
 * @author Nesbert Hidalgo
 **/
function classify($word)
{
    return CString::classify($word);
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
    return CString::cycle($var1, $var2);
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
    return CString::quote2string($str);
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
    return CString::mask($str, $mask);
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
    return CString::masktruncate($str, $length, $tail, $strict);
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
    return CString::wordwrap_line($s, $l);
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
    return CString::retrieve_number($str);
}

/**
 * Checks if the string starts with $needle.
 *
 * @param string $needle
 * @param string $haystack
 * @return string
 * @author Nesbert Hidalgo
 **/
function starts_with($needle, $haystack)
{
    return CString::starts_with($needle, $haystack);
}

/**
 * Checks if the string ends with $needle.
 *
 * @param string $needle
 * @param string $haystack
 * @return string
 * @author Nesbert Hidalgo
 **/
function ends_with($needle, $haystack)
{
    return CString::ends_with($needle, $haystack);
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
function num2words($num, $money = false, $caps = false, $c = 1)
{
    return CString::num2words($num, $money, $caps, $c);
}

/**
 * Escape a string without connecting to a DB.
 *
 * @return string
 * @link http://www.gamedev.net/community/forums/topic.asp?topic_id=448909
 **/
function escape_string($str)
{
    return CString::escape_string($str);
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
function split_words($string, $max = 1)
{ 
    return CString::split_words($string, $max);
}