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
		$this->view->content = '';
		$this->view->template_path = '';
 		$this->view->template = '';
		$this->view->page = '';	
	}
	
	/**
	 * Set content for view
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 */
	public function set_content($str)
	{
		$this->view->content = $str;
	}
	
	/**
	 * Set content path for view
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 */
	public function set_content_path($path)
	{
		$this->view->content_path = $path;
	}
	
	/**
	 * Set template path for view
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 */
	public function set_template_path($path)
	{
		$this->view->template_path = $path;
	}
	
	/**
	 * Creates the page to be displayed and sets it to the page property
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 */
	public function create_view()
	{
		// set view content
		$this->view->content = $this->view->content . $this->get_include_contents($this->view->content_path);
		
		// combine content and template. else use content only
		if ( $this->view->template_path ) {
			
			// get template
			$this->view->template = $this->get_include_contents($this->view->template_path);
			
			if ( $this->view->template ) {
				$this->view->page = str_replace($this->view->content_key, $this->view->content, $this->view->template);
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
	public function get_view()
	{
		return $this->view->page;
	}

	/**
	 * Prints the $page
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 */
	public function show_view()
	{
		print $this->view->page;
	}

	/*
	 * http://us3.php.net/manual/en/function.include.php
	 * Example 16-11. Using output buffering to include a PHP file into a string
	 */
	public function get_include_contents($filename)
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