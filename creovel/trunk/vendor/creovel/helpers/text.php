<?php
/**
 * Returns a pluralized verision of a word.
 */
function pluralize($word, $count = null)
{
	if ( $count == 1 ) return $word;
	return inflector::pluralize($word);
}

/**
 * Returns a singularized verision of a word.
 */
function singularize($word)
{
	return inflector::singularize($word);
}

/**
 * Transform text like 'programmers_field' to 'Programmers Field'
 */
function humanize($word)
{
	return inflector::titleize($word);
} 

/*
 * Transform text like 'programmers_field' to 'ProgrammersField'
 */	
function camelize($word)
{
	return inflector::camelize($word);
}    

/*
 * Transforms text like 'ProgrammersField' to 'programmers_field'
 */	
function underscore($word)
{
	return inflector::underscore($word);
}

/**
 * Helpful for alternating between between two values during a loop.
 *
 * <code>
 *  <tr class="<?=cycle('data_alt1', 'data_alt2')?>">
 *
 *  <tr class="data_alt<?=cycle()?>">
 * </code> 
 *
 * @author Nesbert Hidalgo
 * @return int/string
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
 * Replace every " (quote) with its html equevelant
 *
 * @author Nesbert Hidalgo
 * @param string $str required
 * @return string
 */
function quote2string($str)
{
	return str_replace("\"", "&quot;", $str);
}

/*
 * Replace every charactor of a string with $mask
 *
 * @author Nesbert Hidalgo
 * @param string $str required
 * @param string $mask optional default set to '*'
 * @return string
 */
function mask($str, $mask = '*')
{
	for ( $i = 0; $i <= ( strlen($str) - 1 ); $i++ ) $return .= $mask;
	return $return;
}

/*
 * Truncates a tring and add trailing periods to it
 *
 * @author Nesbert Hidalgo
 * @param string $str required
 * @param string $length optional default set to '*'
 * @param string $tail optional default set to '...'
 * @return string
 */
function truncate($str, $length = 30, $tail = '...')
{	
	$str = trim($str);

	if ( strlen($str) >= $length ) {
	
		$str = trim(substr_replace($str, '', ($length - strlen($tail))));
		$str .= $tail;
	
	}
	
	return $str;
}
?>