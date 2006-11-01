<?

define(BROWSE_REPOSITORY_PREFIX, 'http://dev.propertyline.com/pldsvn/');
define(LOCAL_REPOSITORY_PATH, '/usr/local/lib/pldsvn/');
define(SVNLOOK_PATH, '/usr/bin/svnlook');

// set application mode
$_ENV['mode'] = 'development'; // development, test, production

// set development database properties
$_ENV['development']['adapter']		= 'mysql';
$_ENV['development']['host']		= 'localhost';
$_ENV['development']['database']	= 'bugs_pld';
$_ENV['development']['username']	= 'root';
$_ENV['development']['password']	= 'b4mb4m';

// set test database properties
$_ENV['test']['adapter']			= 'mysql';
$_ENV['test']['host']				= 'localhost';
$_ENV['test']['database']			= '';
$_ENV['test']['username']			= '';
$_ENV['test']['password']			= '';

// set production database properties
$_ENV['production']['adapter']		= 'mysql';
$_ENV['production']['host']			= 'localhost';
$_ENV['production']['database']		= '';
$_ENV['production']['username']		= '';
$_ENV['production']['password']		= '';

// define application urls
define(BASE_URL, 			'http'.( getenv('HTTPS') == 'on' ? 's' : '' ).'://'.getenv('HTTP_HOST'));
define(CCS_URL,				BASE_URL.'/stylesheets/');
define(JAVASCRIPT_URL,		BASE_URL.'/javascripts/');

// define application paths
define(BASE_PATH, 			dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
define(CONFIG_PATH, 		BASE_PATH.'config'.DIRECTORY_SEPARATOR);
define(PUBLIC_PATH, 		BASE_PATH.'public'.DIRECTORY_SEPARATOR);
define(APP_PATH, 			BASE_PATH.'app'.DIRECTORY_SEPARATOR);
define(MODELS_PATH, 		APP_PATH.'models'.DIRECTORY_SEPARATOR);
define(VIEWS_PATH, 			APP_PATH.'views'.DIRECTORY_SEPARATOR);
define(CONTROLLERS_PATH, 	APP_PATH.'controllers'.DIRECTORY_SEPARATOR);
define(HELPERS_PATH, 		APP_PATH.'helpers'.DIRECTORY_SEPARATOR);
define(SCRIPT_PATH, 		BASE_PATH.'script'.DIRECTORY_SEPARATOR);
define(VENDOR_PATH, 		BASE_PATH.'vendor'.DIRECTORY_SEPARATOR);
define(CREOVEL_PATH, 		VENDOR_PATH.'creovel'.DIRECTORY_SEPARATOR);

// set session handler
$_ENV['sessions'] = 'table'; // false, true, 'table'

// initialize framework and include core libraries
require_once(CREOVEL_PATH.'initialize.php');

// set default routing: controller, action, layout
$_ENV['routes']['default']['controller'] 	= 'index';
$_ENV['routes']['default']['action'] 		= 'index';
$_ENV['routes']['default']['layout']		= 'default';

require_once(BASE_PATH.'lib/textile.php');
require_once(BASE_PATH.'lib/subversion.php');

?>
