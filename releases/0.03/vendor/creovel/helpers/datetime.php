<?php
/*
	Script: datetime
	
	Date and time functions go here.
*/

/*
	Function: datetime
	
	Returns MySQL Timestamp.
	
	Parameters:
	
		datetime - optional
	
	Returns:
	
		String.
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
		
		case ( is_number($datetime) ):
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
	
		String.
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

/*
	Function: time_ago
	
	Returns time passed. Latest activity about "8 hours" ago.
	
	Parameters:
	
		time - required
	
	Returns:
	
		String.
*/

function time_ago($time)
{ 
	$time = time() - (is_string($time) ? strtotime($time) : $time);
	
	switch (true) {
		
		case ($time < MINUTE):
			$time = round(((($time % WEEK) % DAY) % HOUR) % MINUTE);
			$return = "{$time} second";
		break;
		
		case ($time < HOUR):
			$time = round(((($time % WEEK) % DAY) % HOUR) / MINUTE);
			$return = "{$time} minute";
		break;
		
		case ($time < DAY):
			$time = round((($time % WEEK) % DAY) / HOUR);
			$return = "{$time} hour";
		break;
		
		case ($time < WEEK):
			$time = round(($time % WEEK) / DAY);
			$return = "{$time} day";
		break;
		
		case ($time < WEEK * 4):
			$time = round($time / WEEK);
			$return = "{$time} week";
		break;
		
		default:
			return false;
		break;
	
	}
	
	return pluralize($return, $time);
}
?>