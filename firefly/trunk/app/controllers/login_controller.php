<?

class login_controller extends application_controller
{
	public function index()
	{
		$this->layout = 'login';

		$this->user = new user();

		if ($this->is_posted()) {
			$this->user->authenticate($this->params['user']);
			if ($this->user->row_count() > 0) {
				$_SESSION['user_id'] = $this->user->id;
				redirect_to('changesets');
			} else {
				$this->errors['general'] = 'The username and password you entered do not match any accounts on record. Please try again.';
			}
		}
	}

	public function logout()
	{
		$_SESSION['user_id'] = null;
		redirect_to('login');
	}
}

?>
