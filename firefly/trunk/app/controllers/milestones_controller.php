<?

class milestones_controller extends application_controller
{
	public function index()
	{
		$this->textile = new Textile();
		$this->milestones = &new milestone();
		$this->milestones->find_all();
	}

	public function create()
	{
		$this->milestone = new milestone();

		if ($this->is_posted()) {
			$this->milestone = new milestone($this->params['milestone']);
			if ($this->milestone->save()) {
				flash_notice('Milestone Saved');
				redirect_to('milestones');
			}
		}
	}

	public function view()
	{
		$this->milestone = new milestone($this->params['id']);

		if ($this->is_posted()) {
			$this->milestone = new milestone($this->params['milestone']);
			if ($this->milestone->save()) {
				flash_notice('Milestone Saved');
				redirect_to('milestones');
			}
		}
	}

	public function delete()
	{
		$this->milestone = new milestone($this->params['id']);
		$this->milestone->delete();

		flash_notice('Milestone Deleted');
		redirect_to('milestones');
	}
}

?>
