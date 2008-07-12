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
		
		case ( is_numeric($datetime) ):
			return date('Y-m-d H:i:s', $datetime);
		break;
		
		case ( is_string($datetime) ):
			return date('Y-m-d H:i:s', strtotime($datetime));
		break;
	}
}
?>