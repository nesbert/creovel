<?

class users_controller extends application_controller
{
	public function index()
	{
		$this->users = new user();
		$this->users->find_all();
	}

	public function create()
	{
		$this->user = new user();

		if ($this->is_posted()) {
			$this->user = new user($this->params['user']);
			if ($this->user->save()) {
				flash_notice('User Saved');
				redirect_to('users');
			}
		}
	}

	public function view()
	{
		$this->user = new user($this->params['id']);

		if ($this->is_posted()) {
			$this->user = new user($this->params['user']);
			if ($this->user->save()) {
				flash_notice('User Saved');
				redirect_to('users');
			}
		}
	}

	public function delete()
	{
		$this->user = new user($this->params['id']);
		$this->user->delete();

		flash_notice('User Deleted');
		redirect_to('users');
	}
}

?>
