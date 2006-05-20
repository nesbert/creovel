<?php
/**
 * View class
 * 
 */ 
 class view
 {
 
	/**
	 * Creates the page to be displayed and sets it to the page property
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 * @param string $view_path
	 * @param string $layout_path
	 */
	public function _create_view($view_path, $layout_path, $options = null)
	{
		// set view content
		$content = $options['text'] . ( $options['render'] !== false ? self::_get_include_contents($view_path, $options) : '' );
		
		// combine content and template. else use content only
		switch ( true ) {
		
			case ( $options['layout'] !== false ):				
				// get layout template
				$layout = self::_get_include_contents($layout_path, $options);
				if ($layout) {
					$page = str_replace('@@page_contents@@', $content, $layout);
				} else {
					$page = $content;
				}
			break;
		
			default:
				$page = $content;
			break;
		
		}
		//echo $page;
		return $page;
	}
	
	/**
	 * Return the page to be displayed as string
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 * @return string 
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
	 * http://us3.php.net/manual/en/function.include.php
	 * Example 16-11. Using output buffering to include a PHP file into a string
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 * @param string $filename
	 * @return string
	 */
	public function _get_include_contents($filename, $options = null)
	{
	   if ( is_file($filename) ) {
		   ob_start();
		   
			// create a variable foreach other option, using its key as the vairable name
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