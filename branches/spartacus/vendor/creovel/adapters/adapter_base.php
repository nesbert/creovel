<?php
/**
 * undocumented class
 *
 * @package default
 * @author Nesbert Hidalgo
 **/
class AdapterBase
{
	/**
	 * Stop the application and display/handle error.
	 *
	 * @return void
	 **/
	public function throwError($msg = null)
	{
		if (!$msg) {
			$msg = 'An error occurred while executing the method ' .
			"<em>{$this->_action}</em> in the <strong> " . get_class($this) .
			'</strong>.';
		}
		CREO('application_error', $msg);
	}
} // END class AdapterBase