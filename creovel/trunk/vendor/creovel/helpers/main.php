<?php
/*
 * General top-level functions.
 */

/**
 * Prints human-readable information about a variable much prettier.
 *
 * @author John Faircloth
 */
 
function print_obj($obj, $kill = false)
{

	echo '<pre class="print_obj" style="text-align: left;">'."\n";
	print_r($obj);
	echo '</pre>'."\n";
	if ( $kill ) die;

}

/**
 * Returns a pluralized verision of a word.
 */
function pluralize($word, $count = null)
{
	if ( $count === 1 ) return $word;
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
function underscore($camel_cased_word)
{
	return inflector::camelize($word);
}

/**
 * Helpful for alternating between between two values during a loop.
 * Ya'll don't want any of this!!!
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
 * Return user definde constats
 *
 * @author Nesbert Hidalgo
 * @return array
 */
 function get_user_defined_constants()
 {
 	$return = get_defined_constants(true);
	return $return['user'];
 }
 
/*
 * Returns a human readable size
 * http://us2.php.net/manual/hk/function.filesize.php#64387
 *
 * @author Nesbert Hidalgo
 * @param string $file
 * @return string
 */
function get_filesize($file)
{
	$size = filesize($file);
	$i = 0;
	$iec = array("B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB");
	while ( ($size/1024)>1 ) {
		$size = $size / 1024;
		$i++;
	}
	return substr($size, 0, strpos($size,'.') + 4).' '.$iec[$i];
}
?>