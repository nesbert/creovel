<?

class unittest
{
	private $num_tests = 0;
	private $num_assertions = 0;
	private $num_failed_assertions = 0;
	private $num_passed_assertions = 0;
	private $messages = array();

	public function run()
	{
		echo "\033[36m---------------------------------------\033[0m\n";
		echo "\033[36mStarting Tests\033[0m\n";
		echo "\033[36m---------------------------------------\033[0m\n";

		$tests = $_SERVER['argv'];
		array_shift($tests);
		$tests = (count($tests) > 0) ? $tests : $this->class_test_methods();

		$total_num_passed = 0;
		$total_num_failed = 0;

		foreach ($tests as $test)
		{
			call_user_func(array( $this, $test ));
		
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

		echo "\033[36m---------------------------------------\033[0m\n";
		echo "\033[36mFinished Tests\033[0m\n";
		echo "\033[36m---------------------------------------\033[0m\n";

	}

	public function assert_true($val)
	{
		if (!$this->assert(true, $val)) {
			$this->messages[] = array( 'type' => 'error', 'message' => "'{$val}' is not TRUE" );
		}
	}

	public function assert_false($val)
	{
		if (!$this->assert(false, $val)) {
			$this->messages[] = array( 'type' => 'error', 'message' => "'{$val}' is not FALSE" );
		}
	}

	public function assert_equal($val1, $val2)
	{
		if (!$this->assert(true, ($val1 === $val2))) {
			$this->messages[] = array( 'type' => 'error', 'message' => "'{$val1}' does not equal '{$val2}'" );
		}
	}

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
