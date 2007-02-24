<?php

/*

Script: index_controller
	Default Controller

*/

class index_controller extends application_controller
{
	/*
	
	Function: index
		Default Action

	*/

	public function index()
	{
		$this->say_hello();
	}

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
