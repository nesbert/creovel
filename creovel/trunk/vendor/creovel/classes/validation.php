<?php
/*
 * Validation class.
 */
class validation
{

	/**
	 * Class properties.
	 *
	 * @author Nesbert Hidalgo
	 * @access private
	 */
	private static $is_valid = false;
	private static $rules;
	private static $data;
	private static $errors;
	
	/**
	 * Class constants.
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 */
	const FIELD_NAME = '@@field_name@@';
	const VALUE = '@@value@@';
	
	function __construct()
	{
		$this->errors = new error('model');
	}
	
}
?>