<?php
/*

Class: unittest
	Handles the unit testing of the model classes.

*/

class unittest
{
	private $num_tests = 0;
	private $num_assertions = 0;
	private $num_failed_assertions = 0;
	private $num_passed_assertions = 0;
	private $messages = array();

	// Section: Public
	
	/*
	
	Function: run
		This main function runs all the tests created.

	Parameters:
		null

	Returns:
		null

	*/

	public function run()
	{
		$start_time = microtime();

		echo "\033[36m-------------------------------------------------\033[0m\n";
		echo "\033[36mStarting Tests ".get_class($this)."\033[0m\n";
		echo "\033[36m-------------------------------------------------\033[0m\n";

		$tests = $this->class_test_methods();

		$total_num_passed = 0;
		$total_num_failed = 0;

		foreach ($tests as $test)
		{
			call_user_func(array( $this, 'setup' ));
			call_user_func(array( $this, $test ));
			call_user_func(array( $this, 'teardown' ));
		
			if ($this->num_passed_assertions == $this->num_assertions) {
				$total_num_passed++;
				echo "\033[32;1m".humanize($test).": PASSED ({$this->num_passed_assertions}/{$this->num_assertions})\033[0m\n";
			} else {
				$total_num_failed++;
				echo "\033[31;1m".humanize($test).": FAILED ({$this->num_passed_assertions}/{$this->num_assertions})\033[0m\n";
			}

			if (count($this->messages) > 0) {
				foreach ($this->messages as $message)
				{
					switch ($message['type'])
					{
						case 'error':
							echo "  \033[31m{$message['message']}\033[0m\n";
					}
				}
				echo "\n";
			}

			$this->num_tests++;
			$total_num_assertions += $this->num_assertions;
			$this->num_assertions = 0;
			$this->num_failed_assertions = 0;
			$this->num_passed_assertions = 0;
			$this->messages = array();
		}

		echo "\n\033[36;1m{$this->num_tests} Tests ({$total_num_passed}/{$this->num_tests} ".number_format(($total_num_passed/$this->num_tests) * 100)."%) : {$total_num_assertions} Assertions\033[0m\n\n";

		echo "\033[36m-------------------------------------------------\033[0m\n";
		echo "\033[36mFinished Tests in ".(microtime() - $start_time)." seconds.\033[0m\n";
		echo "\033[36m-------------------------------------------------\033[0m\n";

	}

	/*
	
	Function: assert_true
		Tests to see if the value passed is true.

	Parameters:
		val - Value to test

	Returns:
		null

	*/

	public function assert_true($val)
	{
		if (!$this->assert(true, $val)) {
			$this->messages[] = array( 'type' => 'error', 'message' => "'{$val}' is not TRUE" );
		}
	}

	/*
	
	Function: assert_false
		Tests to see if the value passed is false.

	Parameters:
		val - Value to test

	Returns:
		null

	*/

	public function assert_false($val)
	{
		if (!$this->assert(false, $val)) {
			$this->messages[] = array( 'type' => 'error', 'message' => "'{$val}' is not FALSE" );
		}
	}

	/*
	
	Function: assert_equal
		Tests to see if the values passed are equal.

	Parameters:
		val1 - First Value
		val2 - Second Value

	Returns:
		null

	*/

	public function assert_equal($val1, $val2)
	{
		if (!$this->assert(true, ($val1 === $val2))) {
			$this->messages[] = array( 'type' => 'error', 'message' => "'{$val1}' does not equal '{$val2}'" );
		}
	}

	/*
	
	Function: assert
		Tests to see if the values passed are equal.

	Parameters:
		val1 - First Value
		val2 - Second Value

	Returns:
		bool

	*/

	public function assert($val1, $val2)
	{
		$this->num_assertions++;

		if ($val1 == $val2) {
			$this->num_passed_assertions++;
			return true;
		} else {
			$this->num_failed_assertions++;
			return false;
		}
	}

	public function setup() { }
	public function teardown() { }

	// Section: Private

	/*
	
	Function: class_test_methods
		Finds all the methods in a class that start with 'test'

	Parameters:
		null

	Returns:
		Array of method names.

	*/

	private function class_test_methods()
	{
		$reflection = new ReflectionClass(get_class($this));
		foreach ($reflection->getMethods() as $method) 
		{
			if (preg_match('/^test/',$method->name)) $methods[] = $method->name;
		}

		return $methods;
	}
	
}
?>
