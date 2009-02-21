<?

class file_test extends unittest
{
	public function setup()
	{
		$this->file = new file();

		$this->php_file = '/tmp/temp_file_test.php';
		$this->txt_file = '/tmp/temp_file_test.txt';
		$this->large_file = '/tmp/temp_file_test_large_file.txt';
		
		for ($i = 0; $i < 10000; $i++) $content += '.';
		file_put_contents($this->php_file, $content);
		file_put_contents($this->txt_file, $content);
		file_put_contents($this->large_file, "{$content}{$content}{$content}{$content}{$content}");
	}

	public function teardown()
	{
		// unlink($this->php_file);
		// unlink($this->txt_file);
		// unlink($this->large_file);
	}

	public function test_extension()
	{
		$this->assert_equal('php', $this->file->extension($this->php_file));
		$this->assert_equal('txt', $this->file->extension($this->txt_file));
	}

	public function test_type()
	{
		$this->assert_equal('text/html', $this->file->type($this->php_file));
		$this->assert_equal('text/plain', $this->file->type($this->txt_file));
	}

	public function test_size()
	{
		$this->assert_equal('1 B', $this->file->size($this->php_file));
		$this->assert_equal('1 B', $this->file->size($this->txt_file));
		$this->assert_equal('5 B', $this->file->size($this->large_file));
	}

	public function test_info()
	{
		$info = $this->file->info($this->php_file);
		$this->assert_equal('text/html', $info['type']);
		$this->assert_equal('1 B', $info['size']);
		$this->assert_equal(strftime('%Y-%m-%d %T'), $info['modified']);

		$info = $this->file->info($this->txt_file);
		$this->assert_equal('text/plain', $info['type']);
		$this->assert_equal('1 B', $info['size']);
		$this->assert_equal(strftime('%Y-%m-%d %T'), $info['modified']);
	}

	public function test_copy()
	{
		$this->file->copy($this->large_file, $this->txt_file);	
		$this->assert_equal(file_get_contents($this->large_file), file_get_contents($this->txt_file));
	}

	public function test_move()
	{
		$content = file_get_contents($this->txt_file);

		$this->file->move($this->txt_file, $this->large_file);

		$this->assert_false(file_exists($this->txt_file));
		$this->assert_equal($content, file_get_contents($this->large_file));
	}

	public function test_delete()
	{
		$this->file->delete($this->txt_file);

		$this->assert_false(file_exists($this->txt_file));
	}
}

?>
