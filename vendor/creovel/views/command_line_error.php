<?php
/*
	Layout view used by creovel to display application error information for command line interfaces.
*/
$column_length = 80;
echo str_repeat("-", $column_length * 1.5)."\n";
echo "Application Error\n";
echo str_repeat("-", $column_length * 1.5)."\n\n";
echo wordwrap_line($this->error->message, $column_length)."\n\n";

echo "You received this error message from the following web address:\n\n".BASE_URL.$_SERVER['REQUEST_URI']."\n\n";

if ($error_count = count($this->error->traces))
{
	echo str_repeat("-", $column_length)."\n";
	echo "Debug Trace\n";
	echo str_repeat("-", $column_length)."\n\n";

	$trace_count = 0;
	$offset = 0;
	foreach ($this->error->traces as $trace)
	{
		// skip traces with no file or line number or magic fucntion calls
		if (!$trace['file'] || !$trace['file'] || in_string('__call', $trace['function']))
		{
			$offset++;
			continue;
		}

		$value = str_replace("('')", '()', ("('" . ( is_array($trace['args']) ? implode("', '", $trace['args']) : '')) . "')");
		echo '#'.($error_count - $trace_count - $offset)." {$trace['class']}{$trace['type']}{$trace['function']}".$value." in {$trace['file']} on line {$trace['line']}\n";

		$trace_count++;
	}
	
	echo "\n";
}

echo str_repeat("-", $column_length * 1.5)."\n";
echo "User Information\n";
echo str_repeat("-", $column_length * 1.5)."\n\n";

echo "HTTP_USER_AGENT: {$_SERVER[HTTP_USER_AGENT]}\n";
echo "HTTP_REFERER: {$_SERVER[HTTP_REFERER]}\n";
echo "HTTP_COOKIE: {$_SERVER[HTTP_COOKIE]}\n";
echo "REMOTE_ADDR: {$_SERVER[REMOTE_ADDR]}\n\n";

echo str_repeat("-", $column_length * 1.5)."\n";
echo wordwrap_line("This an automated message brought to by Creovel ".get_version()." (http://creovel.org)", $column_length).".\n";
echo str_repeat("-", $column_length * 1.5)."\n\n";

?>