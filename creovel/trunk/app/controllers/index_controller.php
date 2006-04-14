<?php
class index_controller extends application_controller
{

	public function index()
	{
		$this->render_text = "Hello World!<br /><br />";
		$this->render_text .= 'You have successfully installed <a href="http://www.creovel.org" target="_blank">creovel</a> version '.get_version().'!';
	}

	public function test()
	{
		$this->render_text = "Hello World!<br /><br />";
		$this->render_text .= 'You have successfully installed <a href="http://www.creovel.org" target="_blank">creovel</a> version '.get_version().'!';
	}

}
?>