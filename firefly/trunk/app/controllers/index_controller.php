<?php
class index_controller extends application_controller
{

	public function index()
	{
		$this->say_hello();
	}
	
	private function say_hello()
	{
		$this->render_text = "<p>$ Hello World!</p>";
	}

}
?>