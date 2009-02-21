<?

define(CD, "cd ");
define(LN, "/bin/ln -s ");
define(LS, "/bin/ls ");
define(RM, "/bin/rm ");
define(SVN, "/usr/bin/svn ");

class phoenix
{
	private $stdin;
	private $stdout;
	private $stderr;

	private $tasks;
	private $config;
	private $application_path;

	public function __construct()
	{
		$this->stdin	= fopen('php://stdin', 'r');
		$this->stdout	= fopen('php://stdout', 'w');
		$this->stderr	= fopen('php://stderr', 'w');

		$this->config = new phoenix_configuration();
		$this->tasks = new phoenix_tasks();

		$this->application_path = "{$this->config->path}/{$this->config->appname}";
		$this->config->application_path = $this->application_path;
	}

	public static function shell_exec($connection, $command)
	{
		$stream = ssh2_exec($connection, $command);
		stream_set_blocking($stream, true);
		while ($line = fgets($stream, 4096)) $out .= $line;
		fclose($stream);

		return $out;
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
					foreach ($this->config->servers as $name => $config) $this->halt($name, $config);
					break;

				case 'R':
					$invalidSelection = false;
					foreach ($this->config->servers as $name => $config) $this->release($name, $config);
					break;

				case 'F':
					$invalidSelection = false;
					foreach ($this->config->servers as $name => $config) $this->fallback($name, $config);
					break;

				case 'A':
					$this->stdout('');
					$this->stdout('Phoenix aborted.');
					$this->stdout('');
					exit;

				default:
					$this->stdout('You have made an invalid selection. Please choose an action by entering H, R or F.');
			}
		}
	}

	public function halt($name, $config)
	{
		$connection = $this->ssh_connect($config);

		// Pre Halt Tasks
		$this->tasks->pre_halt($connection, $this->config, $config);

		$this->user_exec($connection, RM."-f {$this->application_path}/current");
		$this->user_exec($connection, CD."{$this->application_path}");
		$this->user_exec($connection, LN."halt current");

		// Post Halt Tasks
		$this->tasks->post_halt($connection, $this->config, $config);

		$this->stdout("Application Halted on {$name}");
	}

	public function release($name, $config)
	{
		$connection = $this->ssh_connect($config);

		$release = $this->getInput('What branch/releases/trunk do you want to release?');

		$config['timestamp'] = strftime("%Y%m%d%H%M%S", time());

		// Pre Release Tasks
		$this->tasks->pre_release($connection, $this->config, $config);

		$this->shell_exec($connection, "mkdir -p {$this->application_path}");
		$this->shell_exec($connection, "mkdir -p {$this->application_path}/releases");
		$this->shell_exec($connection, "mkdir -p {$this->application_path}/halt");

		if (isset($this->config->svnusername)) $auth = " --username={$this->config->svnusername} --password={$this->config->svnpassword}";
		$this->shell_exec($connection, SVN." export {$auth} {$this->config->svnurl}/{$release} {$this->application_path}/releases/{$config['timestamp']}");
		$this->shell_exec($connection, RM."-f {$this->application_path}/current");
		$this->shell_exec($connection, CD."{$this->application_path} && ".LN."releases/{$config['timestamp']} current");

		// Post Release Tasks
		$this->tasks->post_release($connection, $this->config, $config);

		$this->stdout("Application Released on {$name}");
	}

	public function fallback($name, $config)
	{
		$connection = $this->ssh_connect($config);

		$stream = ssh2_exec($connection, LS."-r {$this->application_path}/releases");

		// Pre Fallback Tasks
		$this->tasks->pre_release($connection, $this->config, $config);

		stream_set_blocking($stream, true);
		$releases = explode("\n", fread($stream, 4096));
		array_pop($releases);
		fclose($stream);

		$stream = ssh2_exec($connection, RM."-rf {$this->application_path}/releases/".$releases[0]);
		$stream = ssh2_exec($connection, RM."{$this->application_path}/current");
		if ($releases[1]) {
			$stream = ssh2_exec($connection, CD."{$this->application_path}");
			$stream = ssh2_exec($connection, LN."releases/".$releases[1]." current");
		}

		// Post Fallback Tasks
		$this->tasks->post_release($connection, $this->config, $config);

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
