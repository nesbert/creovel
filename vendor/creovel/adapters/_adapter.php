<?php

abstract class _adapter
{
	
	// Section Protected
	
	protected function handle_error($message)
	{
		$_ENV['error']->add($message);
	}
	
}

?>