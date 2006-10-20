<?php
class index_controller extends application_controller
{

	public function index()
	{
		$this->no_view();
		
		$user = new user;
		
		print_obj($user, 1);
		
		$path = PUBLIC_PATH.'digg.xml';
		
		/*
		$xml = new xml;
		$xml->load($path);
		echo $xml->to_str();
		
		print_obj($xml, 1);
		*/

		$rss = new rss;
		
		
		$rss->load($path);
		print_obj($rss, 1);
		
		
	}
	
	public function test()
	{
		$this->layout = false;
	}
	
}
?>