<?

class application_controller extends controller
{
	public function before_filter()
	{
		if (!isset($_SESSION['user_id']) && get_controller() != 'login') {
			redirect_to('login');
		}
	}
}

?>
