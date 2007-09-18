<?php
/*

Script: datetime

*/

/*

Function: datetime
	Returns MySQL Timestamp.

Parameters:
	datetime - optional

Returns:
	string

*/
 
function datetime($datetime = null)
{
	switch ( true )
	{
		case ( !$datetime ):
		default:
			return date('Y-m-d H:i:s');
		break;		

		case ( is_array($datetime) ):
			return date('Y-m-d H:i:s', mktime($datetime['hour'], $datetime['minute'], $datetime['second'], $datetime['month'], $datetime['day'], $datetime['year']));
		break;
		
		case ( is_int($datetime) ):
			return date('Y-m-d H:i:s', $datetime);
		break;
		
		case ( is_string($datetime) ):
			return date('Y-m-d H:i:s', strtotime($datetime));
		break;
	}
}

/*
Function: format_time
	Returns MySQL Timestamp.

Parameters:
	time - optional

Returns:
	string

*/
function format_time($time = null)
{
	switch ( true )
	{
		case ( !$time ):
		default:
			return date('H:i:s A');
		break;		

		case ( is_array($time) ):
			return date('H:i:s A', mktime($datetime['hour'], $datetime['minute'], $datetime['second'], $datetime['month'], $datetime['day'], $datetime['year']));
		break;
		
		case ( is_numeric($time) ):
			return date('H:i:s A', $time);
		break;
		
		case ( is_string($time) ):
			return date('H:i:s A', strtotime($time));
		break;
	}
}
?>