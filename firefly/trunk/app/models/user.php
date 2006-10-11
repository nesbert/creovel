<?

class user extends model
{
	public function authenticate($params)
	{
		$this->find_first_by_email_and_password($params['email'], $params['password']);
	}
}

?>
