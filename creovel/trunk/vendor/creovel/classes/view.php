<?php
/**
 * View class
 * 
 */ 
 class view
 {
 
 	protected $view;
	
	/**
	 * Initailize $view properties
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 */
	public function __construct()
	{
		$this->view->content_key = '@@page_contents@@';		
		$this->view->content_path = '';
		//$this->view->content = '';
		$this->view->layout_path = '';
 		//$this->view->template = '';
		//$this->view->page = '';	
	}
	
	/**
	 * Set content for view
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 */
	public function _set_view_content($str)
	{
		$this->view->content = $str;
	}
	
	/**
	 * Set content path for view
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 */
	public function _set_view_content_path($path)
	{
		$this->view->content_path = $path;
	}
	
	/**
	 * Set layout path for view
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 */
	public function _set_view_layout_path($path)
	{
		$this->view->layout_path = $path;
	}
	
	/**
	 * Creates the page to be displayed and sets it to the page property
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 */
	public function _create_view()
	{
		// set view content
		$this->view->content = $this->view->content . $this->_get_include_contents($this->view->content_path);
		
		// combine content and template. else use content only
		if ( $this->view->layout_path ) {
			
			// get template
			$this->view->layout = $this->_get_include_contents($this->view->layout_path);
			
			if ( $this->view->layout ) {
				$this->view->page = str_replace($this->view->content_key, $this->view->content, $this->view->layout);
			} else {
				$this->view->page = $this->view->content;
			}
			
		} else {
		
			$this->view->page = $this->view->content;
			
		}
	}
	
	/**
	 * Return the page to be displayed as string
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 * @return string 
	 */
	public function _get_view()
	{
		$this->_create_view();
		return $this->view->page;
	}

	/**
	 * Prints the $page
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 */
	public function _show_view()
	{
		$this->_create_view();
		print $this->view->page;
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