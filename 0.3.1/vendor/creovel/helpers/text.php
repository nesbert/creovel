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
	return inflector::pluralize($word);
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
	return inflector::singularize($word);
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
	return inflector::titleize($word);
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
	return inflector::camelize($word);
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
	return inflector::underscore($word);
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
	//$var1 = $var1 ? $var1 : 1;
	//$var2 = $var2 ? $var2 : 2;
	$return = ( $return == $var2 ? $var1 : $var2 );
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

/*
	Function: amphersand_encode
	
	Reformats every charater of a string to their ampersand equevilant.
	
	Parameters:
		
		str - the string to be formatted
	
	Returns:
	
		String.
*/

function amphersand_encode($str)
{
	$ampersand = array(
		' ' => '&#32;',
		'!' => '&#33;',
		'"' => '&#34;',
		'#' => '&#35;',
		'$' => '&#36;',
		'%' => '&#37;',
		'&' => '&#38;',
		"'" => '&#39;',
		'(' => '&#40;',
		')' => '&#41;',
		'*' => '&#42;',
		'+' => '&#43;',
		',' => '&#44;',
		'-' => '&#45;',
		'.' => '&#46;',
		'/' => '&#47;',
		'0' => '&#48;',
		'1' => '&#49;',
		'2' => '&#50;',
		'3' => '&#51;',
		'4' => '&#52;',
		'5' => '&#53;',
		'6' => '&#54;',
		'7' => '&#55;',
		'8' => '&#56;',
		'9' => '&#57;',
		':' => '&#58;',
		';' => '&#59;',
		'<' => '&#60;',
		'=' => '&#61;',
		'>' => '&#62;',
		'?' => '&#63;',
		'@' => '&#64;',
		'A' => '&#65;',
		'B' => '&#66;',
		'C' => '&#67;',
		'D' => '&#68;',
		'E' => '&#69;',
		'F' => '&#70;',
		'G' => '&#71;',
		'H' => '&#72;',
		'I' => '&#73;',
		'J' => '&#74;',
		'K' => '&#75;',
		'L' => '&#76;',
		'M' => '&#77;',
		'N' => '&#78;',
		'O' => '&#79;',
		'P' => '&#80;',
		'Q' => '&#81;',
		'R' => '&#82;',
		'S' => '&#83;',
		'T' => '&#84;',
		'U' => '&#85;',
		'V' => '&#86;',
		'W' => '&#87;',
		'X' => '&#88;',
		'Y' => '&#89;',
		'Z' => '&#90;',
		'[' => '&#91;',
		'\' => '&#92;',
		']' => '&#93;',
		'^' => '&#94;',
		'_' => '&#95;',
		'`' => '&#96;',
		'a' => '&#97;',
		'b' => '&#98;',
		'c' => '&#99;',
		'd' => '&#100;',
		'e' => '&#101;',
		'f' => '&#102;',
		'g' => '&#103;',
		'h' => '&#104;',
		'i' => '&#105;',
		'j' => '&#106;',
		'k' => '&#107;',
		'l' => '&#108;',
		'm' => '&#109;',
		'n' => '&#110;',
		'o' => '&#111;',
		'p' => '&#112;',
		'q' => '&#113;',
		'r' => '&#114;',
		's' => '&#115;',
		't' => '&#116;',
		'u' => '&#117;',
		'v' => '&#118;',
		'w' => '&#119;',
		'x' => '&#120;',
		'y' => '&#121;',
		'z' => '&#122;',
		'{' => '&#123;',
		'|' => '&#124;',
		'}' => '&#125;',
		'~' => '&#126;',
		'™' => '&#153;',
		'©' => '&#169;',
		'®' => '&#174;'
	);
	
	$str = str_split($str);
	$ampersand_str = '';
	
	foreach ($str as $char) {
		$ampersand_str .= $ampersand[$char];
	}
	
	return $ampersand_str;
}

?>