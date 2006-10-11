<?

class Logger
{
	private $file_handle;
	private $full_output;
	private $severity_counts;

	public function __construct($file)
	{
		$this->full_output = '';
		$this->severity_counts = array();
		$this->render_time = time();

		if (touch($file)) {
			$this->file_handle = fopen($file, 'a');
		} else {
			trigger_error("'{$file}' is not writeable. Please fix.");
		}
	}

	public function write($severity, $message = null)
	{
		$this->severity_counts[$severity]++;
		$this->full_output .= "{$message}\n";
	}

	public function __destruct()
	{
		$params = creovel::get_events();

		$this->render_time = (time() - $this->render_time);

		$footer = "\n\n";
		$footer .= $this->stylize('Finished at: '.strftime('%Y-%m-%d %T', time()), 1)."\tRendered in: {$this->render_time} seconds\n\n";

		$footer .= 'Parameters: { ';
		if (count($params)) foreach ($params as $k => $v)
		{
			if (is_array($v)) foreach ($v as $i => $x) {
				$footer .= $this->stylize($this->stylize('"'.$i.'"', 1), 36)." => {$x} ";
			} else {
				$footer .= $this->stylize($this->stylize('"'.$k.'"', 1), 36)." => {$v} ";
			}
		}
		$footer .= " }\n\n";

		$footer .= "-------------------------------------------------------------\n";
		$footer .= $this->stylize('Group Counts', 1)."\n";
		$footer .= "-------------------------------------------------------------\n";
		foreach ($this->severity_counts as $k => $v) { $footer .= $this->stylize($this->stylize($k, 1), 36).":\t".$v."\n"; }

		fwrite($this->file_handle, $this->full_output."\n\n\n".$footer."\n\n\n");
		fclose($this->file_handle);
	}

	private function stylize($text, $value)
	{
		return "\033[{$value}m{$text}\033[0m";
	}
}

?>
