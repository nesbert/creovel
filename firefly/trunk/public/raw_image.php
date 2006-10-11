<?

require_once dirname(__FILE__).'/../config/environment.php';
require_once dirname(__FILE__).'/../lib/subversion.php';

$subversion = new Subversion(LOCAL_REPOSITORY_PATH, BROWSE_REPOSITORY_PATH, SVNLOOK_PATH);

$youngest = $subversion->youngest();
$revision = ($_GET['revision']) ? $_GET['revision'] : $subversion->youngest();
$path = ($_GET['path']) ? $_GET['path'] : '';

$file = $subversion->file($revision, $path);

echo $file;

?>
