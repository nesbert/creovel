<?

class index_controller extends application_controller
{
	public function index()
	{
		$this->textile = new Textile();

		$this->changessets = &new changeset_model();
		$this->changessets->find_all(array( 'order' => 'created_at DESC' ));
	}
}

?>
