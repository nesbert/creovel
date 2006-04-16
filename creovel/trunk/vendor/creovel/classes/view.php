<?php
/**
 * View class
 * 
 */ 
 class view
 {
 
 	private $content;
	private $content_key = '@@page_contents@@';
 	private $template;
 	private $page;
	
	public $content_path;
	public $template_path;
 	public $text;
	
	/**
	 * Creates the page to be displayed and sets it to the page property
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 */
	public function create()
	{
		// set view content
		$this->content = $this->get_include_contents($this->content_path);
		$this->content = $this->text . $this->content;
		
		// combine content and template. else use content only
		if ( $this->template_path ) {
			
			// get template
			$this->template = $this->get_include_contents($this->template_path);
			
			if ( $this->template ) {
				$this->page = str_replace($this->content_key, $this->content, $this->template);
			} else {
				$this->page = $this->content;
			}
			
		} else {
		
			$this->page = $this->content;
			
		}
	}
	
	/**
	 * Return the page to be displayed as string
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 * @return string 
	 */
	public function get_page()
	{
		return $this->page;
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