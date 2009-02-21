<?

class phoenix_configuration
{
	public function __construct()
	{
  		$this->appname		= 'creovel';
		$this->path			= '/var/www/html/';

		$this->svnurl		= 'http://creovel.googlecode.com/svn';
		$this->svnusername	= null;
		$this->password		= null;

		$this->servers = array
		(	
			'localhost' => array
			(
				'address'	=> 'localhost',
				'user'		=> 'user',
				'pass'		=> 'pass',
				'port'		=> 22
			)
		);
	}
}

class phoenix_tasks
{
	public function pre_halt($connection, $config, $server_config)
	{
	}

	public function post_halt($connection, $config, $server_config)
	{
	}

	public function pre_fallback($connection, $config, $server_config)
	{
	}

	public function post_fallback($connection, $config, $server_config)
	{
	}

	public function pre_release($connection, $config, $server_config)
	{
	}

	public function post_release($connection, $config, $server_config)
	{
		// Automatically Set Production Environment
		Phoenix::shell_exec($connection, "sed -e \"s/= 'development'/= 'production'/g\" {$config->application_path}/releases/{$server_config['timestamp']}/config/environment.php > {$config->application_path}/releases/{$server_config['timestamp']}/temp");
		Phoenix::shell_exec($connection, "mv {$config->application_path}/releases/{$server_config['timestamp']}/temp {$config->application_path}/releases/{$server_config['timestamp']}/config/environment.php");
	}
}

?>
