#!/usr/bin/php
<?

// Found and Converted to PHP
// http://www.webtypes.com/files/bc-post-commit.rb
// Thanks Guys!

$_ENV['environment'] = 'production';
require dirname(__FILE__).'/../config/environment.php';

define(BROSE_REPOSITORY_PREFIX, 'http://creovel.org/svn/creovel/creovel/');
define(SVNLOOK, '/usr/bin/env svnlook');

function gather_and_post($repo_path, $revision)
{
	$commit_author	= shell_exec(SVNLOOK." author {$repo_path} -r {$revision}"); //chop
	$commit_log		= shell_exec(SVNLOOK." log {$repo_path} -r {$revision}");
	$commit_diff	= shell_exec(SVNLOOK." diff {$repo_path} -r {$revision}");
	$commit_date	= shell_exec(SVNLOOK." date {$repo_path} -r {$revision}");
	$commit_changed	= shell_exec(SVNLOOK." changed {$repo_path} -r {$revision}");

	foreach (preg_split("/\n/", $commit_changed) as $line) {
		if ($line != '') {
			preg_match("/^(\w)\s+(.*)/", $line, $matches);
			$files .= "{$matches[1]} \"{$matches[2]}\":".BROSE_REPOSITORY_PREFIX."{$matches[2]}\n";
		}
	}

	$changeset = &new changeset_model();
	$changeset->set_revision($revision);
	$changeset->set_author($commit_author);
	$changeset->set_log($commit_log);
	$changeset->set_changed_files($files);
	$changeset->save();
}

switch ($argv[1])
{
	case 'import':
		//$model = &new model();
		//$model->query_records('DELETE FROM changesets');

		$youngest = shell_exec(SVNLOOK." youngest {$argv[2]}");
		for ($i = 1; $i <= (int)$youngest; $i++) { gather_and_post($argv[2], $i); }
		break;

	default:
		gather_and_post($argv[1], $argv[2]);
		break;
}

?>
