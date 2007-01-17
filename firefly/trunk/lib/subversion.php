<?

class Subversion
{
	public $repository_path;
	public $browse_repository_path;
	public $svn_look_path;

	public function __construct($repository_path, $browse_repository_path, $svn_look_path)
	{
		$this->repository_path			= $repository_path;
		$this->browse_repository_path	= $browse_repository_path;
		$this->svn_look_path			= $svn_look_path;
	}

	public function youngest()
	{
		return (int)shell_exec($this->svn_look_path.' youngest '.$this->repository_path);
	}

	public function revision($revision = null)
	{
		if (!$revision) $revision = $this->youngest();

		$info = array();
		$info['revision']		= $revision;
		$info['author']			= trim(shell_exec($this->svn_look_path." author {$this->repository_path} -r {$revision}"));
		$info['log']			= trim(shell_exec($this->svn_look_path." log {$this->repository_path} -r {$revision}"));
		$info['diff']			= trim(shell_exec($this->svn_look_path." diff {$this->repository_path} -r {$revision}"));
		$info['created_at']		= trim(shell_exec($this->svn_look_path." date {$this->repository_path} -r {$revision}"));

		$changed_files = trim(shell_exec($this->svn_look_path." changed {$this->repository_path} -r {$revision}"));
		foreach (preg_split("/\n/", $changed_files) as $line) {
			if ($line != '') {
				preg_match("/^(\w)\s+(.*)/", $line, $matches);
				if ($matches[1] == 'D') {
					$info['changed_files'] .= "{$matches[1]} {$matches[2]}\n";
				} else {
					$info['changed_files'] .= "{$matches[1]} \"{$matches[2]}\":"."/source/view_file?revision={$revision}&path=".urlencode($matches[2])."\n";
				}
			}
		}

		preg_match("/^(\d+-\d+-\d+ \d+:\d+:\d+)/ie", $info['created_at'], $matches);
		$info['commit_date'] = $matches[1];
		$info['created_at'] = $matches[1];
		$info['updated_at'] = $matches[1];

		return $info;
	}

	public function import($revision)
	{
		$changeset = new changeset($this->revision($revision));
		$changeset->save();
	}

	public function tree($revision, $path)
	{
		$raw = shell_exec($this->svn_look_path." tree -r {$revision} {$this->repository_path} {$path}");

		foreach (preg_split("/\n/", $raw)  as $line) 
		{
			if ($line[1] != ' ') {
				$type = (preg_match('/\//ie', $line) > 0) ? 'dir' : 'file';
				$tree[] = array( 'type' => $type, 'name' => str_replace('/', '', trim($line)) );
			}
		}

		array_shift($tree);
		array_pop($tree);

		return $tree;
	}

	public function file($revision, $path)
	{
		return trim(shell_exec($this->svn_look_path." cat -r {$revision} {$this->repository_path} {$path}"));
	}

	public function parse_diff(&$changeset, &$delegate)
	{
		foreach (preg_split("/\n/", htmlspecialchars($changeset->diff)) as $line)
		{
			if (preg_match('/^Modified:\s+(.*)/', $line, $matches)) {	
				$delegate->print_header($matches[1]);
				$i = 0;

			// Figure out later how to parse this line and track the line number
			} elseif (preg_match('/^=.*$/', $line, $matches)) { 		$i--;
			} elseif (preg_match('/^--(.*)/', $line, $matches)) { 		$i--;
			} elseif (preg_match('/^\+\+(.*)/', $line, $matches)) { 	$i--;
			} elseif (preg_match('/^@@(.*)@@$/', $line, $matches)) { 	$i--;

			} elseif (preg_match('/^\+\s+(.*)/', $line, $matches)) { 	$delegate->print_line($i, '+', $matches[1]);
			} elseif (preg_match('/^\-\s+(.*)/', $line, $matches)) { 	$delegate->print_line($i, '-', $matches[1]);
			} else { 													$delegate->print_line($i, null, $line);
			}

			$i++;
		}
	}
}

?>
