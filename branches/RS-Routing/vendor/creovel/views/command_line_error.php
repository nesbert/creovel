<?php
/*
	Layout view used by creovel to display application error information for command line interfaces.
*/

echo "------------------------------------------------------------------------------\n";
echo "Application Error\n";
echo "------------------------------------------------------------------------------\n";
echo wordwrap_line($this->message, 80)."\n\n";

if (count($this->traces))
{
	echo "------------------------------------------------------------------------------\n";
	echo "Debug Trace\n";
	echo "------------------------------------------------------------------------------\n\n";

	$trace_count = 0;
	$offset = 0;
	foreach ($this->traces as $trace)
	{
		// skip traces with no file or line number or magic fucntion calls
		if (!$trace['file'] || !$trace['file'] || strstr($trace['function'], '__call'))
		{
			$offset++;
			continue;
		}

		$value = str_replace("('')", '()', ("('" . ( is_array($trace['args']) ? implode("', '", $trace['args']) : '')) . "')");
		echo '#'.(count($this->traces) - $trace_count - $offset)." {$trace['class']}{$trace['type']}{$trace['function']}".$value." in {$trace['file']} on line {$trace['line']}\n";

		$trace_count++;
	}
}
?>
