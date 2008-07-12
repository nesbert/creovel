<?php
/**
 * Copyright (c) 2005-2006, creovel.org
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated 
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software,
 * and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions
 * of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED
 * TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * Licensed under The MIT License. Redistributions of files must retain the above copyright notice.
 */

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
 * Truncates a string and adds trailing periods to it. Now handles words
 * better thank you Mel Cruz for the suggestion. By default trucates at 
 * end of words.
 *
 * @author Nesbert Hidalgo
 * @param string $str required
 * @param string $length optional default set to 100 characters
 * @param string $tail optional default set to '...'
 * @param bool $strict optional default false truncate at exact $length
 * @return string
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
	}
		
	return $str.$tail;
}
?>