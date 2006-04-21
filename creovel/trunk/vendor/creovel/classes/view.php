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
	 * @param string $view_path optional
	 * @param string $layout_path optional
	 */
	public function _create_view($view_path = null, $layout_path = null)
	{
		$view_path = $view_path ? $view_path : VIEWS_PATH.$this->_controller.DS.( $this->render ? $this->render : $this->_action ).'.php';
		$layout_path = $layout_path ? $layout_path : VIEWS_PATH.'layouts'.DS.$this->layout.'.php';
	
		// set view content
		$content = $this->render_text . ( $this->render !== false ? $this->_get_include_contents($view_path) : '' );
		
		// combine content and template. else use content only
		switch ( true ) {
		
			case ( $this->layout !== false ):				
				// get layout template
				$layout = $this->_get_include_contents($layout_path);
				$page = str_replace('@@page_contents@@', $content, $layout);
			break;
		
			default:
				$page = $content;
			break;
		
		}
		
		return $page;
	}
	
	/**
	 * Return the page to be displayed as string
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 * @return string 
	 */
	public function _get_view($view_path = null, $layout_path = null)
	{
		return $this->_create_view($view_path, $layout_path);
	}

	/**
	 * Print page to screen
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 */
	public function _show_view($view_path = null, $layout_path = null)
	{
		print $this->_create_view($view_path, $layout_path);
	}

	/*
	 * http://us3.php.net/manual/en/function.include.php
	 * Example 16-11. Using output buffering to include a PHP file into a string
	 */
	public function _get_include_contents($filename)
	{
	   if ( is_file($filename) ) {
		   ob_start();
		   include $filename;
		   $contents = ob_get_contents();
		   ob_end_clean();
		   return $contents;
	   }
	   return false;
	}
	
 }
 ?>