<?php

abstract class adapter_base
{
	
	// Section Protected
	
	protected function handle_error($message)
	{
		$_ENV['error']->add($message);
	}
	
}

?>