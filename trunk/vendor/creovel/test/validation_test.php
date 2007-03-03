<?

class validation_test extends unittest
{
	public function setup()
	{
		$this->validation = new validation();
	}

	public function test_validates_presence_of()
	{
		$this->assert_true($this->validation->validates_presence_of('email', 'noone@noone.com'));
		$this->assert_false($this->validation->validates_presence_of('email', ''));
		$this->assert_false($this->validation->validates_presence_of('email', null));
	}

	public function test_validates_format_of()
	{
		$this->assert_true($this->validation->validates_format_of('email', 'noone@noone.com', null, true, '/\w@\w/'));
		$this->assert_false($this->validation->validates_format_of('email', 'noonenoone.com', null, true, '/\w@\w/'));
		$this->assert_true($this->validation->validates_format_of('date', '2007-03-03', null, true, '/\d{4}-\d{2}-\d{2}/'));
		$this->assert_false($this->validation->validates_format_of('date', '2007-3-03', null, true, '/\d{4}-\d{2}-\d{2}/'));
	}

	public function test_validates_email_of()
	{
		$this->assert_true($this->validation->validates_email_of('email', 'noone@noone.com'));
		$this->assert_true($this->validation->validates_email_of('email', 'team@creovel.org'));
		$this->assert_true($this->validation->validates_email_of('email', 'noone@noone.co.uk'));
		$this->assert_true($this->validation->validates_email_of('email', 'noone@sub.noone.co.uk'));

		$this->assert_false($this->validation->validates_email_of('email', 'noonesub.noone.co.uk'));
		$this->assert_false($this->validation->validates_email_of('email', 'noone@\noone.com'));
		$this->assert_false($this->validation->validates_email_of('email', 'no one@noone.com'));
		$this->assert_false($this->validation->validates_email_of('email', 'no one@noone.'));
		$this->assert_false($this->validation->validates_email_of('email', 'no one@noone'));
	}

	public function test_validates_confirmation_of()
	{
		$this->assert_true($this->validation->validates_confirmation_of('password', 'secret', 'secret'));
		$this->assert_true($this->validation->validates_confirmation_of('password', 'SECRET', 'SECRET'));

		$this->assert_false($this->validation->validates_confirmation_of('password', 'secret', 'nomatch'));
		$this->assert_false($this->validation->validates_confirmation_of('password', 'secret', 'SECRET'));
	}

	public function test_validates_uniqueness_of()
	{
		$this->assert_true($this->validation->validates_uniqueness_of('email', 'noone@noone.com', 'users'));
	}

	public function test_validates_agreement()
	{
		$this->assert_true($this->validation->validates_agreement('tos', true));
		$this->assert_true($this->validation->validates_agreement('tos', 'Yes'));

		$this->assert_false($this->validation->validates_agreement('tos', ''));
		$this->assert_false($this->validation->validates_agreement('tos', null));
	}
}

?>
