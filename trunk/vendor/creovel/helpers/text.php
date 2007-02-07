<?

/*

Script: text

*/

/*

Function: underscore
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

Function: underscore
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

Function: underscore
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

Function: underscore
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
?>
