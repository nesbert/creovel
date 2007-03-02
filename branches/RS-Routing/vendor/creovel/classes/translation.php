<?

/*

Class: translation
	Provides as a helper to load the browsers language set if available.

	Translations are stored in the lang directory in the root of the application.
	They are hashes with the key being the name of the string.

	The language is pulled from the $_SERVER settings based on the client viewing the application.
	If the user is requesting US English content the en_us.php file will be loaded.

	(start code)
	$language_set = array
	(

		'welcome'						=> "Welcome back %s!",
		'local_news'					=> "Local News",
		'copyright'						=> "Copyright &copy; %d."

	);
	(end)

*/

class translation
{
	/*

	Function: string
		Returns a formatted string provided from the language set loaded.
		Language strings follow the formate provided by sprintf.  (http://us2.php.net/manual/en/function.sprintf.php)

	Parameters:
		key - string key
		replacements - Variable number of arguments to replace in the string.

	Returns:
		string or null

	*/

	public static function string()
	{
		static $language;
		static $language_set;
		
		if (!isset($language)) $language = str_replace('-', '_', substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 5));

		if (file_exists(BASE_PATH."lang/{$language}.php")) {

			require_once BASE_PATH."lang/{$language}.php";

		} elseif (file_exists(BASE_PATH."lang/en_us.php")) {

			require_once BASE_PATH."lang/en_us.php";

		}

		if (isset($language_set))
		{
			$args = func_get_args();
			$key = array_shift($args);
			return vsprintf($language_set[$key], $args);
		}
	}
}

?>
