<?php
/**
 * View class
 * 
 */ 
 class view
 {
 
 	private $page;
 	private $view;
	
 	public $controller;
 	public $render;
 	public $text;
	public $layout;
	
	/**
	 * Creates the page to be displayed and sets it to the page property
	 *
	 * @author Nesbert Hidalgo
	 * @access public 
	 */
	public function create()
	{
	
		if ( $this->render ) $this->view = $this->get_include_contents(VIEWS_PATH.$this->controller.DS.$this->render.'.php');
		if ( $this->text ) $this->view = $this->text . $this->view;
		// include template
		if ( $this->layout ) {
			
			// get layout contents
			$this->page = $this->get_include_contents(VIEWS_PATH.'layouts'.DS.$this->layout.'.php');
			
			if ( $this->page ) {
				$this->page = str_replace('@@page_contents@@', $this->view, $this->page);
			} else {
				$this->page = $this->view;
			}
			
		} else {
		
			$this->page = $this->view;
			
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