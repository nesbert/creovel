<?

define(LN, "/bin/ln -s ");
define(LS, "/bin/ls ");
define(RM, "/bin/rm ");
define(SVN, "/usr/bin/svn ");

class Pheonix
{
	private $stdin;
	private $stdout;
	private $stderr;

	private $lowCtrl = null;
	private $interactive = false;

	private $config;

	public function __construct()
	{
		$this->stdin	= fopen('php://stdin', 'r');
		$this->stdout	= fopen('php://stdout', 'w');
		$this->stderr	= fopen('php://stderr', 'w');

		if (!$this->load_configuration(PHEONIX_CONFIG)) die("Unable to parse YAML Migration file");
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
					foreach ($this->config['servers'] as $name => $config) $this->halt($name, $config);
					break;

				case 'R':
					$invalidSelection = false;
					foreach ($this->config['servers'] as $name => $config) $this->release($name, $config);
					break;

				case 'F':
					$invalidSelection = false;
					foreach ($this->config['servers'] as $name => $config) $this->fallback($name, $config);
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

	public function load_configuration($file)
	{
		$this->config = Spyc::YAMLLoad($file);
		
		return (is_array($this->config));
	}

	public function halt($name, $config)
	{
		$connection = $this->ssh_connect($config);

		$stream = ssh2_exec($connection, RM."{$this->config['config']['apps_path']}/current");
		$stream = ssh2_exec($connection, LN."{$this->config['config']['apps_path']}/halt {$this->config['config']['apps_path']}/current");

		$this->stdout("Application Halted on {$name}");
	}

	public function release($name, $config)
	{
		$connection = $this->ssh_connect($config);

		$release_stamp = strftime("%Y%m%d%H%M", time());

		$branch = $this->getInput('What branch do you want to release?');
		$branch = ($branch == 'trunk') ? 'trunk' : "branches/{$branch}";

		$stream = ssh2_exec($connection, SVN." co {$this->config['config']['svn_base_url']}/{$branch} {$this->config['config']['apps_path']}/releases/{$release_stamp}");

		$stream = ssh2_exec($connection, RM."{$this->config['config']['apps_path']}/current");
		$stream = ssh2_exec($connection, LN."{$this->config['config']['apps_path']}/releases/{$release_stamp} {$this->config['config']['apps_path']}/current");

		$this->stdout("Application Released on {$name}");
	}

	public function fallback($name, $config)
	{
		$connection = $this->ssh_connect($config);

		$stream = ssh2_exec($connection, LS."-r {$this->config['config']['apps_path']}/releases");

		stream_set_blocking($stream, true);
		$releases = explode("\n", fread($stream, 4096));
		array_pop($releases);
		fclose($stream);

		$stream = ssh2_exec($connection, RM."-rf {$this->config['config']['apps_path']}/releases/".$releases[0]);
		$stream = ssh2_exec($connection, RM."{$this->config['config']['apps_path']}/current");
		if ($releases[1]) $stream = ssh2_exec($connection, LN."{$this->config['config']['apps_path']}/releases/".$releases[1]." {$this->config['config']['apps_path']}/current");

		$this->stdout("Application Reset to the Last Version on {$name}");
	}

	private function ssh_connect($config)
	{
		$connection = ssh2_connect($config['address'], $config['port']);
		ssh2_auth_password($connection, $config['user'], $config['pass']);

		return $connection;
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
