<?php
/*

	Script: index_controller
	
	Default Controller

*/

class index_controller extends application_controller
{
	// Section: Public
	
	/*
	
		Function: index
		
		Default Action
	
	*/

	public function index()
	{
		$this->say_hello();
	}

	/*
	
		Function: error
		
		Sample Error Action
	
	*/

	public function error()
	{
	}
	
	// Section: Private
	
	/*
	
		Function: say_hello
		
		Sample Function
	
	*/
	
	private function say_hello()
	{
		$this->render_text = "<p>$ Hello World!</p>";
	}

}
?>
