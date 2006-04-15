<?

class milestones_controller extends application_controller
{
	public function index()
	{
		$this->milestones = &new milestone_model();
		$this->milestones->find_all();
	}
}

?>
