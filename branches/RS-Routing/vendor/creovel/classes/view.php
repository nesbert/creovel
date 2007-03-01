<?php
/*

	Class: view
	
	The *view* class handles all the presentation logic. The Simple Template System (STS) allows an easy way of
	seperating/combining business logic and presentation layers.

*/

class view
{
	// Section: Public
	
	/*
	
		Function: _create_view
		
		Creates the page to be displayed and sets it to the page property.
		
		Parameters:
		
			view_path - Required string of the file path to render.
			layout_path - Required string of the layout path to render.
			options - Optional array of desiplay options.
			
		Returns:
		
			String of HTML used for output.
	
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
	
		Function: _get_include_contents
		
		Using output buffering to include a PHP file into a string. Used to combine coding logic (PHP)
		and views. The main function used by creovel's templating engine (STS).
		
		Parameters:
		
			filename - Required string of the file path.
			options - Optional array of variables to made local to file.
			
		Returns:
		
			String of HTML/Text from buffer.
			
		See Also:
		
			http://us3.php.net/manual/en/function.include.php Example 16-11.
	
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

	/*
	
		Function: _get_view
		
		Return a page view as string. A wrapper to <_create_view>.
		
		Returns:
		
			String of HTML used for output.
			
		See Also:
		
			<_create_view>
	
	*/

	public function _get_view($view_path = null, $layout_path = null, $options = null)
	{
		return self::_create_view($view_path, $layout_path, $options);
	}

	/*
	
		Function: _show_view
		
		Print a page view to screen. A wrapper to <_create_view>.
		
		Returns:
		
			Prints HTML output to screen.
			
		See Also:
		
			<_create_view>
	
	*/

	public function _show_view($view_path = null, $layout_path = null, $options = null)
	{
		print self::_create_view($view_path, $layout_path, $options);
	}

}
?>