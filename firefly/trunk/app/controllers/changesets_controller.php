<?

class changesets_controller extends application_controller
{
	public function index()
	{
		$this->textile = new Textile();

		$this->changesets = &new changeset();
		$this->changesets->load_all();
	}

	public function view()
	{
		$this->textile = new Textile();
		$this->subversion = new Subversion(LOCAL_REPOSITORY_PATH, BROWSE_REPOSITORY_PATH, SVNLOOK_PATH);

		$this->changeset = &new changeset($this->params['id']);
	}
}

?>
