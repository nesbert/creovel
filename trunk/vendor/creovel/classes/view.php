<?

/*

Class: view

*/

class view
{
	// Section: Public
		
	/*

	Function: _create_view
		Creates the page to be displayed and sets it to the page property

	Parameters:	
		view_path - required
		layout_path - required
		options - optional

	Returns:
		string

	*/

	public function _create_view($view_path, $layout_path, $options = null)
	{
		try {
			// set content data
			$content = $options['text'];

			if ( $options['render'] !== false ) {
				$content .= self::_get_include_contents($view_path, $options);
			}

			// combine content and template. else use content only
			switch (true)
			{
				case ( $options['layout'] !== false ):
					if ( file_exists($layout_path) ) {
						$page = str_replace('@@page_contents@@', $content, self::_get_include_contents($layout_path, $options));
					} else {
						throw new Exception("Unable to render 'layout'. File not found <strong>{$layout_path}</strong>.");
					}
				break;

				default:
					$page = $content;
					break;
			}

			return $page;

		} catch ( Exception $e ) {

			// add to errors
			$_ENV['error']->add($e->getMessage());

		}		
	}

	/*

	Function: _get_view		
		Return the page to be displayed as string

	Returns:
		string

	*/

	public function _get_view($view_path = null, $layout_path = null, $options = null)
	{
		return self::_create_view($view_path, $layout_path, $options);
	}

	/**
	* Print page to screen
	*
	* @author Nesbert Hidalgo
	* @access public 
	 */

	public function _show_view($view_path = null, $layout_path = null, $options = null)
	{
		print self::_create_view($view_path, $layout_path, $options);
	}

	/*
	
	Function: _get_include_contents
		http://us3.php.net/manual/en/function.include.php
		Example 16-11. Using output buffering to include a PHP file into a string

	Parameters:	
		filename - required
		options - optional
	
	Returns:
		string

	*/

	public function _get_include_contents($filename, $options = null)
	{
		if ( is_file($filename) ) {
			ob_start();

			// create a variable foreach option, using its key as the vairable name
			if ( count($options) ) foreach ( $options as $key => $values ) $$key = $values;

			include $filename;
			$contents = ob_get_contents();
			ob_end_clean();
			return $contents;
		}
		return false;
	}
}

?>
