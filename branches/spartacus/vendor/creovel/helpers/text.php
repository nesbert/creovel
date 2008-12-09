<?php
/*

Script: text

*/

/*

Function: pluralize
	Returns a pluralized verision of a word.

Parameters:
	word - string
	count - number of items

Returns:
	string

*/	

function pluralize($word, $count = null)
{
	if ( $count == 1 ) return $word;
	return Inflector::pluralize($word);
}

/*

Function: singularize
	Returns a singularized verision of a word.

Parameters:
	word - string

Returns:
	string

*/	

function singularize($word)
{
	return Inflector::singularize($word);
}

/*

Function: humanize
	Transform text like 'programmers_field' to 'Programmers Field'

Parameters:
	word - string

Returns:
	string

*/	

function humanize($word)
{
	return Inflector::titleize($word);
} 

/*

Function: camelize
	Transform text like 'programmers_field' to 'ProgrammersField'

Parameters:
	word - string

Returns:
	string

*/	

function camelize($word)
{
	return Inflector::camelize($word);
}    

/*

Function: underscore
	Transforms text like 'ProgrammersField' to 'programmers_field'

Parameters:
	word - string

Returns:
	string

*/	

function underscore($word)
{
	return Inflector::underscore($word);
}

/*

Function: cycle
	Helpful for alternating between between two values during a loop.

	(start code)
 		<tr class="<?=cycle('data_alt1', 'data_alt2')?>">
 		<tr class="data_alt<?=cycle()?>">
	(end)

Returns:
	int/string

*/
 
function cycle($var1 = null, $var2 = null)
{
	static $return;
	$var1 = $var1 ? $var1 : 1;
	$var2 = $var2 ? $var2 : 2;
	$return = ( $return == $var2 || !$return ? $var1 : $var2 );
	return $return;
}

/*

Function: quote2string
	Replace every " (quote) with its html equevelant

Paremeters:
	str - required

Returns:
	string
*/

function quote2string($str)
{
	return str_replace("\"", "&quot;", $str);
}

/*

Function: mask
	Replace every charactor of a string with $mask

Parmeters:
	str - required
	mask - optional default set to '*'

Returns:
	string

*/

function mask($str, $mask = '*')
{
	$return = '';
	for ( $i = 0; $i <= ( strlen($str) - 1 ); $i++ ) $return .= $mask;
	return $return;
}

/*

Function: truncate
	Truncates a string and adds trailing periods to it. Now handles words better thank you Mel Cruz for the suggestion. By default trucates at end of words.

Parameters:
	str - required
	length - optional default set to 100 characters
	tail - optional default set to '...'
	strict - optional default false truncate at exact $length

Returns:
	string

*/

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

/*

Function: truncate
	Reformats a string to fit within a display with a certain
	number of columns.  Words are moved between the lines as
	necessary.  Particularly useful for formatting text to
	be sent via email (prevents nasty wrap-around problems).

Parameters:
	s - the string to be formatted
	l - the maximum length of a line

Credit:
	syneryder@namesuppressed.com

Returns:
	string

*/

function wordwrap_line($s, $l)
{
	$tok = strtok($s, " ");

	while (strlen($tok) != 0)
	{
		if (strlen($line) + strlen($tok) < ($l + 2) ) {
			$line .= " $tok";
		} else {
			$formatted .= "$line\n";
			$line = $tok;
		}
		$tok = strtok(" ");
	}

	$formatted .= $line;
	$formatted = trim($formatted);

	return $formatted;
}

/**
 * Retrieve a number from a string.
 *
 * @param string $str
 * @return float
 **/
function retrieve_number($str)
{
	return floatval(preg_replace('/[^0-9.-]/', '', $str));
}