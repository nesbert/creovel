<?

class Migrate
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
		$this->getVersion();
		$this->stdout('Current version: '.$this->current_version);
		
		$this->getMigrations();
		$this->stdout('Total migration files: '.$this->migration_count);
		$this->stdout('');
		
		$this->stdout('Options:');
		$this->stdout("  [M]igrate: Run a migration");
		$this->stdout("  [G]enerate: Generate a migration file");
		$this->stdout("  [R]eset: Reset schema version to zero");
		$this->stdout("  [A]bort");
		
		$invalidSelection = true;
		
		while ($invalidSelection)
		{
			$action = strtoupper($this->getInput('What would you like to do?', array( 'M', 'G', 'R', 'A' ), 'M'));

			switch($action)
			{
				case 'M':
					$invalidSelection = false;
					$this->doMigrate();
					break;

				case 'G':
					$invalidSelection = false;
					$this->doGenerate();
					break;

				case 'R':
					$invalidSelection = false;
					$this->doReset();
					break;

				case 'A':
					$this->stdout('');
					$this->stdout('Migrations aborted.');
					$this->stdout('');
					exit;

				default:
					$this->stdout('You have made an invalid selection. Please choose an action by entering M, G or A.');
			}
		}
	}

	public function getVersion()
	{	
		$model = new model();
		$model->query("CREATE TABLE IF NOT EXISTS `schema_info` (`id` int(10) unsigned NOT NULL auto_increment, `version` int(10) unsigned NOT NULL default '0', PRIMARY KEY  (`id`))");

		$schema_info = new schema_info();
		$schema_info->find_first_by_id(1);

		if ($schema_info->row_count() == 0) {
			$schema_info->version = 0;
			$schema_info->save();
		}

		$this->current_version = (int)$schema_info->version;
	}

	public function getMigrations()
	{
		if (!is_dir(MIGRATIONS_PATH)) mkdir(MIGRATIONS_PATH);

		$this->migration_count = 0;

		$dir = dir(MIGRATIONS_PATH);
		while (false !== ($entry = $dir->read()))
		{
			preg_match("/[0-9]+_.+\.yml/ie", $entry, $matches);
			if (count($matches) > 0) {
				$this->migration_count++;
				$this->migrations[] = $entry;
			}
		}
	}

	public function doReset()
	{
		$destroy = strtoupper($this->getInput('Would you also like to delete all your migration files?', array( 'Y', 'N' ), 'N'));
		
		$this->stdout('');
		$this->stdout('  *************************** WARNING! ***************************');
		if ($destroy == 'Y') {
			$this->stdout('  You are about to reset your database schema version to zero');
			$this->stdout('  and delete all your migration files.');
		} else {
			$this->stdout('  You are about to reset your database schema version to zero.');
		}		
		
		$confirm = strtoupper($this->getInput('Are you sure that you want to do this?', array( 'Y', 'N' ), 'N'));
		
		if ($confirm == 'N') {

			$this->stdout('');
			$this->stdout('Resetting aborted.');
			$this->stdout('');
			exit;

		} else {

			$schema_info = new schema_info();
			$schema_info->find_first_by_id('1');
			$schema_info->version = 0;
			$schema_info->save();

			if ($destroy == 'Y') exec('rm -rf '.MIGRATIONS_PATH.'/*');

			$this->stdout('');
			$this->stdout('  Schema version reset to zero.');
			$this->stdout('');
			exit;

		}
	}

	public function doMigrate()
	{
		if ($this->migration_count === 0)
		{
			$this->stdout("\nNo migrations found\n");
			exit;
		}
		
		$this->stdout('');
		$this->stdout('Available Migrations:');
		$this->stdout('  [0] > (remove all migrations)');
		foreach($this->migrations as $mig)
		{
			preg_match("/^([0-9]+)\_(.+)\.yml$/", $mig, $match);
			$num = $match[1];
			$name = $match[2];
			if ($num == $this->migration_count) {
				$this->stdout('  ['.$num.'] > \''.$name.'\'  (most recent migration)');
			} else {
				$this->stdout('  ['.$num.'] > \''.$name.'\'');
			}
		}
		
		$new_version = $this->getInput('Please select a version number to migrate to:');
		
		if (!is_numeric($new_version))
		{
			$this->stdout('');
			$this->stdout('Migration version number ('.$new_version.') is invalid.');
			$this->doMigrate(false);
		}

		if ($new_version > $this->migration_count)
		{
			$this->stdout('');
			$this->stdout('Version number entered ('.$new_version.') does not exist.');
			$this->doMigrate(false);
		}

		if ($this->current_version === $new_version)
		{
			$this->stdout('');
			$this->stdout('Migrations are up to date');
			$this->stdout('');
			exit;
		}
		
		$direction = ($new_version < $this->current_version) ? 'down' : 'up';
		if ($direction == 'down') rsort($this->migrations);
		
		$this->stdout('');
		$this->stdout("Migrating database from version {$this->current_version} to $new_version ...");
		$this->stdout('');

		$schema_info = new schema_info();
		$schema_info->find_first_by_id('1');
		
		foreach($this->migrations as $migration_name)
		{
			preg_match("/^([0-9]+)\_(.+)$/", $migration_name, $match);
			$num = $match[1];
			$name = $match[2];
			
			if ($direction == 'up') {

				if ($num <= $this->current_version) continue;
				if ($num > $new_version) break;

			} else {

				if ($num > $this->current_version) continue;
				if ($num == $new_version) break;

			}
		
			$this->stdout("  Migrating $direction: [$num] $name ...");
			
			$res = $this->startMigration(MIGRATIONS_PATH . '/' . $migration_name, $direction);
			if ($res == 1) {

				$this->stdout('   - complete.');
				$this->stdout('');
				$schema_info->version = ($direction == 'up') ? ($schema_info->version + 1) : ($schema_info->version - 1);
				$schema_info->save();

			} else {

				$this->stdout("   - ERROR: $res");
				$this->stdout('');
				exit;

			}	
		}
		
		$this->stdout('');
		$this->stdout('Migrations completed.');
		$this->stdout('');
		exit;
	}

	public function doGenerate()
	{
		$name = strtolower($this->getInput('Please enter the name of the migration: (Underscored string. Example: my_first_migration)'));
		
		if (empty($name))
		{
			$this->stdout('');
			$this->stdout('Migration name not specified.');
			$this->doGenerate();
		}

		if (!preg_match("/^([a-z0-9]+|_)+$/", $name))
		{
			$this->stdout('');
			$this->stdout('Migration name ('.$name.') is invalid');
			$this->doGenerate();
		}

		$new_migration_count = $this->migration_count + 1;		
		$data = ($new_migration_count == 1) ? $this->doInitialGenerate() : "#\n# migration YAML file\n#\nUP: null\nDOWN: null";
		$this->createFile(MIGRATIONS_PATH.'/'.$new_migration_count.'_'.$name.'.yml', $data);
		
		$this->stdout('');
		$this->stdout('Generation of \''.$name.'\' completed.');
		$this->stdout('Please edit \''.MIGRATIONS_PATH . '/' .$new_migration_count . '_' . $name . '.yml\' to customise your migration.');
		$this->stdout('');
		exit;
	}

	public function doInitialGenerate()
	{
		$data = "#\n# migration YAML file\n#\nUP:\n";

		$model = new model();
		foreach ($model->all_tables() as $table)
		{
			if ($table != 'schema_info')
			{
				$table_model = new model();

				$data .= "  create table:\n";
				$data .= "    - name: $table\n";
				$data .= "      columns:\n";

				foreach ($table_model->field_breakdown($table) as $field)
				{
					if ($field['field'] == 'id') continue;

					$data .= "        - name: {$field['field']}\n";

					switch (true)
					{
						case preg_match("/^int\((.*)\)/ie", $field['type'], $matches):
							$data .= "          type: integer\n";
							$data .= "          limit: {$matches[1]}\n";
							$data .= "          not_null: ".$this->not_null($field['null'])."\n";
							break;

						case preg_match("/^varchar\((.*)\)/ie", $field['type'], $matches):
							$data .= "          type: string\n";
							$data .= "          limit: {$matches[1]}\n";
							$data .= "          not_null: ".$this->not_null($field['null'])."\n";
							break;

						case preg_match("/^text/ie", $fields['type'], $matches):
							$data .= "          type: text\n";
							$data .= "          not_null: ".$this->not_null($field['null'])."\n";
							break;

						case preg_match("/^enum\((.*)\)/ie", $field['type'], $matches):
							$values = explode(',', str_replace('\'', '', $matches[1]));
							$data .= "          type: enum\n";
							$data .= "          values:\n";
							$data .= "          not_null: ".$this->not_null($field['null'])."\n";
							foreach ($values as $value) $data .= "            - {$value}\n";
							break;
					}
				}

				foreach ($table_model->key_breakdown($table) as $key)
				{
					if ($key['key_name'] != 'PRIMARY')
					{
						switch ($key['key_name'])
						{
							case 'primary':
								$keys[$key['key_name']]['type'] = 'primary';
								break;

							case 'fulltext':
								$keys[$key['key_nme']]['type'] = 'fulltext';
								break;

							case 'unique':
								$keys[$key['key_name']]['type'] = 'unique';
								break;

							case 'btree':
							case 'hash':
							case 'rtree':
							default:
								$keys[$key['key_name']]['type'] = 'index';
								break;
						}
						$keys[$key['key_name']]['name'] = $key['key_name'];
						$keys[$key['key_name']]['fields'][] = $key['column_name'];
					}
				}

				if (count($keys) > 0) {
					$data .= "      keys:\n";
					foreach ($keys as $key)
					{
						$data .= "        - type: {$key['type']}\n";
						$data .= "          name: {$key['name']}\n";
						$data .= "          fields:\n";
						foreach ($key['fields'] as $field) $data .= "            - {$field}\n";
					}
				}
			}
		}

		$data .= "DOWN: null";

		return $data;
	}

	public function not_null($value)
	{
		return ($value == '' || strtoupper($value) == 'YES' || $value == null) ? 'false' : 'true';
	}

	public function startMigration($file, $direction)
	{
		$array = Spyc::YAMLLoad($file);
		
		if (!is_array($array)) return "Unable to parse YAML Migration file";
		if (!$array[strtoupper($direction)]) return "Direction does not exist!";

		return $this->array_to_sql($array[strtoupper($direction)]);
	}

	private function array_to_sql($array)
	{
		$model = new model();

		foreach ($array as $name=>$action)
		{
			if ($name == 'create_table' || $name == 'create_tables')
			{
				foreach ($action as $value)
				{
					$create_table_sql1 = '';
					$create_table_sql2 = '';
					$create_table_sql1 .= "CREATE TABLE `{$value['name']}` (\n";
					if(!$value['no_id'])
					{
						$create_table_sql2 .= "`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY";
					}
					foreach ($value['columns'] as $col)
					{
						$create_table_sql2 .= ($create_table_sql2) ? ",\n" : '';
						$create_table_sql2 .= $this->_sql_field($col);
					}
					foreach ((array)$value['keys'] as $key)
					{
						$create_table_sql2 .= ($create_table_sql2) ? ",\n" : '';
						$create_table_sql2 .= $this->_sql_key($key);
					}
					$model->query($create_table_sql1 . $create_table_sql2 . ")");
				}
			}
			elseif ($name == 'drop_table')
			{
				$model->query("DROP TABLE `$action`");
			}
			elseif ($name == 'drop_tables')
			{
				$drop_table_sql1 = "DROP TABLE ";
				foreach ($action as $table)
				{
					$drop_table_sql2 .= ($drop_table_sql2) ? ", `$table`" : "`$table`";
				}
				$model->query($drop_table_sql1 . $drop_table_sql2);
			}
			elseif ($name == 'insert_data')
			{
				foreach ($action as $table=>$rows)
				{
					foreach ($rows as $row)
					{
						$cols='';
						$vals='';
						foreach ($row as $col=>$val)
						{
							$cols = ($cols) ? "$cols, `$col`" : "`$col`";
							$vals = ($vals) ? "$vals, '$val'" : "'$val'";
						}
						$model->query("INSERT INTO `$table` ($cols) VALUES ($vals)");
					}
				}
			}
			elseif ($name == 'empty_data')
			{
				if (is_array($action))
				{
					foreach ($action as $table)
					{
						$model->query("DELETE FROM `$table`");
					}
				}
				else
				{
					$model->query("DELETE FROM `$action`");
				}
			}
			elseif ($name == 'add_columns' || $name == 'add_column')
			{
				foreach ($action as $table=>$cols)
				{
					$create_table_sql1 = '';
					$create_table_sql2 = '';
					$create_table_sql1 .= "ALTER TABLE `$table` ";
					foreach ($cols as $col)
					{
						$create_table_sql2 .= ($create_table_sql2) ? ", ADD " : 'ADD ';
						$create_table_sql2 .= $this->sql_field($col);
						if(isset($col['after']))
							$create_table_sql2 .= 'AFTER '.$col['after'];
					}
					$sql = $create_table_sql1 . $create_table_sql2;
					$model->query($sql);
				}
			}
			elseif ($name == 'drop_columns' || $name == 'drop_column')
			{
				foreach ($action as $table=>$cols)
				{
					if (is_array($cols))
					{
						$sql='';
						foreach ($cols as $col)
						{
							$sql = ($sql) ? "$sql, DROP `$col`" : "DROP `$col`";
						}
						$model->query("ALTER TABLE `$table` $sql");
					}
					else
					{
						$model->query("ALTER TABLE `$table` DROP `$cols`");
					}
				}
			}
			elseif ($name == 'alter_columns' || $name == 'alter_column')
			{
				foreach ($action as $table=>$cols)
				{
					$sql = '';
					foreach ($cols as $col)
					{
						$sql = ($sql) ? "$sql, ALTER " : 'ALTER ';
						$sql .= $this->_sql_field($col);
					}
					$model->query("ALTER TABLE `$table` $sql");
				}
			}
			elseif ($name == 'add_keys' || $name == 'add_key')
			{
				foreach($action as $table=>$keys)
				{
					$sql = '';
					foreach ($keys as $key)
					{
						$sql = ($sql) ? "$sql, ADD " : 'ADD ';
						$sql .= $this->_sql_key($key);
					}
					$model->query("ALTER TABLE `$table` $sql");
				}
			}
			elseif ($name == 'drop_keys' || $name == 'drop_key')
			{
				foreach($action as $table=>$keys)
				{
					if(is_array($keys))
					{
						$sql = '';
						foreach ($keys as $key)
						{
							$sql = ($sql) ? "$sql, DROP KEY {$key['name']}" : "DROP KEY {$key['name']}";
						}
						$model->query("ALTER TABLE `$table` $sql");
					}
					else
					{
						$model->query("ALTER TABLE `$table` DROP $keys");
					}
				}
			}
			elseif ($name == 'sql')
			{
				if (is_array($action))
				{
					foreach ($action as $sql)
					{
						$model->query($sql);
					}
				}
				else
				{
					$model->query($action);
				}
			}
		}
		return 1;
	}

	private function sql_field($col)
	{
		switch($col['type']) {
			case 'integer':
				$type = 'INT';
				if(isset($col['limit']))
					$type .= '('.$col['limit'].')';
				break;
			case 'string':
				if(isset($col['limit']))
					$type = 'VARCHAR('.$col['limit'].')';
				else
					$type = 'VARCHAR(255)';
				break;
			case 'text':
				$type = 'TEXT';
				if(isset($col['limit']))
					$type .= '('.$col['limit'].')';
				break;
			case 'bool':
				$type = 'BOOL';
				break;
			case 'enum':
				if(is_array($col['values']))
					$type .= "enum('".implode("', '", $col['values'])."')";
				elseif(isset($col['values']))
					$type .= "enum('".$col['values']."')";
				else
					$type .= "enum('')";
				break;
			default:
				$type = up($col['type']);
				if(isset($col['limit']))
					$type .= "({$col['limit']})";
				break;
		}
		if ($col['name'] == 'created' || $col['name'] == 'created_at')	$type = 'DATETIME ';
		if ($col['name'] == 'modified' || $col['name'] == 'updated' || $col['name'] == 'updated_at')	$type = 'DATETIME ';
		$null = (!$col['not_null']) ? 'NULL' : 'NOT NULL';
		
		if(isset($col['default'])) {
			$default = "DEFAULT '{$col['default']}'";
		} elseif($col['not_null']) {
			$default = "DEFAULT '0'";
		} else {
			$default = '';
		}
		
		return "`{$col['name']}` $type $null $default";
	}
	
	private function sql_key($key)
	{
		$type = 'INDEX';
		if(isset($key['type']))
			$type = up($key['type']);
		
		$name = '';
		if(isset($key['name']))
			$name = $key['name'];
		
		if(is_array($key['fields']))
			$fields = '`'.implode('`, `', $key['fields']).'`';
		else
			$fields = '`'.$key['fields'].'`';
		
		return "$type $name ($fields)";
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

	public function createFile ($path, $contents)
	{
		echo "\nCreating file $path\n";
		$shortPath = str_replace(ROOT,null,$path);
		$path = str_replace('//', '/', $path);
		if (is_file($path) && $this->interactive === true)
		{
			fwrite($this->stdout, "File {$shortPath} exists, overwrite? (y/n/q):");
			$key = trim(fgets($this->stdin));

			if ($key=='q') {
				fwrite($this->stdout, "Quitting.\n");
				exit;
			} elseif ($key=='a') {
				$this->dont_ask = true;
			} elseif ($key=='y') {
			} else {
				fwrite($this->stdout, "Skip   {$shortPath}\n");
				return false;
			}
		}

		if ($f = fopen($path, 'w')) {
			fwrite($f, $contents);
			fclose($f);
			fwrite($this->stdout, "Wrote   {$shortPath}\n");
			return true;
		} else {
			fwrite($this->stderr, "Error! Couldn't open {$shortPath} for writing.\n");
			return false;
		}
	}
}

?>
