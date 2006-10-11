<? 

// Yeah its bad, i know

class PrintDiff
{
	public function print_header($filename)
	{
		echo '</table>';
		echo "<h2>{$filename}</h2>";
		echo '<table class="diff_file" border="0" cellpadding="0" cellspacing="0"">';
	}

	public function print_line($number, $type, $line)
	{
		if ($type == '+') $style = 'a4fda8';
		if ($type == '-') $style = 'fba9a9';

		echo '<tr style="background: #'.$style.'; border-bottom: 1px solid #ccc;">';
		echo '<td class="number">'.$number.'</td>';
		echo '<td class="line">'.$line.'</td>';
		echo '</tr>';
	}
}

$this->subversion->parse_diff($locals['changeset'], new PrintDiff);

?>

<table>
