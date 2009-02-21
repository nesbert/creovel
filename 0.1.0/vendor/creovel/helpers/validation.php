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

/*
 * Validation helpers.
 */

/*
 * Finds whether a variable is a valid email address
 *
 * @author Nesbert Hidalgo
 * @access public
 * @param string $var required
 * @return bool
 */
function is_email($var)
{
	return eregi('^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$', $var) ? true : false;
}

/*
 * Finds whether a variable only contains characters A-Z or a-z
 *
 * @author Nesbert Hidalgo
 * @access public
 * @param string $var required
 * @return bool
 */
function is_alpha($var)
{
	return preg_match('/^[a-z]+$/i', $var) ? true : false;
}

/*
 * Finds whether a variable only contains characters A-Z or a-z or 0-9
 *
 * @author Nesbert Hidalgo
 * @access public
 * @param string $var required
 * @return bool
 */
function is_alpha_numeric($var)
{
	return preg_match('/^[a-zA-Z0-9]+$/', $var) ? true : false;
}

/*
 * Finds whether a variable is a number
 *
 * @author Nesbert Hidalgo
 * @access public
 * @param string $var required
 * @return bool
 */
function is_number($var)
{
	return preg_match('/^[0-9]+?[.]?[0-9]*$/', $var) ? true : false;
}

/*
 * Finds whether a variable is a positive number
 *
 * @author Nesbert Hidalgo
 * @access public
 * @param string $var required
 * @return bool
 */
function is_positive_number($var)
{
	return is_number($var) && $var > 0 ? true : false;
}

/*
 * Finds whether a $var1 is equal to $var2
 *
 * @author Nesbert Hidalgo
 * @access public
 * @param mixed $var1 required
 * @param mixed $var2 required
 * @return bool
*/
function is_match($var1, $var2)
{
	return $var1 == $var2;
}

/*
 * Finds whether a variable is between $min and $max
 *
 * @author Nesbert Hidalgo
 * @access public
 * @param mixed $var required
 * @param int $min required
 * @param int $max required
 * @return bool
 */
function is_between($var, $min, $max)
{
	return ( (is_numeric($min) && is_numeric($max)) && ($var >= $min && $var <= $max) );
}

/*
 * Finds whether a variable length equals $length
 *
 * @author Nesbert Hidalgo
 * @access public
 * @param string $var required
 * @param int $length required
 * @return bool
 */
function is_length($var, $length)
{
	return count(str_split($var)) == $length;
}

/*
 * Finds whether a variable length is between $min and $max
 *
 * @author Nesbert Hidalgo
 * @access public
 * @param string $var required
 * @param int $min required
 * @param int $max required
 * @return bool
 */
function is_length_between($var, $min, $max)
{
	$length = count(str_split($var));
	return ( $length >= $min ) && ( $length <= $max );
}

?>