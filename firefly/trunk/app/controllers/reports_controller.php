<?

class reports_controller extends application_controller
{
	public function index()
	{
	}

	public function view()
	{
		$this->textile = new Textile();

		$this->start_date = strftime('%Y-%m-%d', get_timestamp_from_post('start_date'));
		$this->end_date = strftime('%Y-%m-%d', get_timestamp_from_post('end_date'));

		$this->changesets = new changeset();
		$this->changesets->find_all(array( 'where' => "commit_date >= '{$this->start_date}' AND commit_date <= '{$this->end_date}'" ));

		$this->tickets = new ticket();
		$this->tickets->find_all(array( 'where' => "updated_at >= '{$this->start_date}' AND updated_at <= '{$this->end_date}'" ));
	}
}

?>
