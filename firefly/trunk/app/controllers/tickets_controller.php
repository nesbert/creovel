<?

class tickets_controller extends application_controller
{
	public function index()
	{
		$this->tickets = &new ticket_model();
		$this->tickets->find_all();
	}

	public function view()
	{
		$this->ticket = &new ticket_model($this->params['id']);
	}

	public function create()
	{
		$this->ticket = &new ticket_model();

		$this->severities = &new severity_model();
		$this->severities->find_all();

		$this->parts = &new part_model();
		$this->parts->find_all();

		print_obj($this->parts);

		$this->statuses = &new status_model();
		$this->statuses->find_all();
	}
}

?>
