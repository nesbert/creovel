<?

define(LN, "/bin/ln -s ");

class Pheonix
{
	private $stdin;
	private $stdout;
	private $stderr;

	private $lowCtrl = null;
	private $interactive = false;

	public function __construct()
	{
	  $this->stdin	= fopen('php://stdin', 'r');
	  $this->stdout	= fopen('php://stdout', 'w');
	  $this->stderr	= fopen('php://stderr', 'w');
	}

	public function main()
	{
		$this->stdout('Options:');
		$this->stdout("  [H]alt: Halt the Current Application");
		$this->stdout("  [R]elease: Release the Application");
		$this->stdout("  [F]allback: Fallback to the Last Version");
		$this->stdout("  [A]bort");
		
		$invalidSelection = true;
		
		while ($invalidSelection)
		{
			$action = strtoupper($this->getInput('What would you like to do?', array( 'H', 'R', 'F', 'A' ), 'H'));

			switch($action)
			{
				case 'H':
					$invalidSelection = false;
					$this->halt();
					break;

				case 'R':
					$invalidSelection = false;
					$this->release();
					break;

				case 'F':
					$invalidSelection = false;
					$this->fallback();
					break;

				case 'A':
					$this->stdout('');
					$this->stdout('Pheonix aborted.');
					$this->stdout('');
					exit;

				default:
					$this->stdout('You have made an invalid selection. Please choose an action by entering H, R or F.');
			}
		}
	}

	public function halt()
	{
		$this->stdout('Halting Application');

		$halt_path = $this->getInput('Enter in the Halt Document Web Root:');

		$this->stdout('Application Halted. Please Come back Soon!');
	}

	public function release()
	{
		$this->stdout('Release Application');

		$this->stdout('Application Released');
	}

	public function fallback()
	{
		$this->stdout('Falling Back Application');

		$this->stdout('Application Reset to the Last Version');
	}

	/*----General purpose functions----*/

	public function getInput($prompt, $options = null, $default = null)
	{
		if (!is_array($options)) {
			$print_options = '';
		} else {
			$print_options = '(' . implode('/', $options) . ')';
		}

		if ($default == null) {
			$this->stdout('');
			$this->stdout($prompt . " $print_options \n" . '> ', false);
		} else {
			$this->stdout('');
			$this->stdout($prompt . " $print_options \n" . "[$default] > ", false);
		}

		$result = trim(fgets($this->stdin));

		return ($default != null && empty($result)) ? $default : $result;
	}

	public function stdout($string, $newline = true)
	{
		if ($newline) {
			fwrite($this->stdout, $string . "\n");
		} else {
			fwrite($this->stdout, $string);
		}
	}

	public function stderr($string)
	{
		fwrite($this->stderr, $string);
	}

	public function hr()
	{
		$this->stdout('--------------------------------------------------------------------------------');
	}
}

?>
