<?

class parts_controller extends application_controller
{
	public function index()
	{
		$this->parts = new part();
		$this->parts->find_all();
	}

	public function create()
	{
		$this->part = new part();

		if ($this->is_posted()) {
			$this->part = new part($this->params['part']);
			if ($this->part->save()) {
				flash_notice('Part Saved');
				redirect_to('parts');
			}
		}
	}

	public function view()
	{
		$this->part = new part($this->params['id']);

		if ($this->is_posted()) {
			$this->part = new part($this->params['part']);
			if ($this->part->save()) {
				flash_notice('Part Saved');
				redirect_to('parts');
			}
		}
	}

	public function delete()
	{
		$this->part = new part($this->params['id']);
		$this->part->delete();

		flash_notice('Part Deleted');
		redirect_to('parts');
	}
}

?>
