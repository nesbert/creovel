<?

class source_controller extends application_controller
{
	public function index()
	{
		$subversion = new Subversion(LOCAL_REPOSITORY_PATH, BROWSE_REPOSITORY_PATH, SVNLOOK_PATH);

		$this->youngest = $subversion->youngest();
		$this->revision = ($this->params['revision']) ? $this->params['revision'] : $subversion->youngest();
		$this->path = ($this->params['path']) ? $this->params['path'] : '';

		$this->tree = $subversion->tree($this->revision, $this->path);
	}

	public function view_file()
	{
		$subversion = new Subversion(LOCAL_REPOSITORY_PATH, BROWSE_REPOSITORY_PATH, SVNLOOK_PATH);

		$this->youngest = $subversion->youngest();
		$this->revision = ($this->params['revision']) ? $this->params['revision'] : $subversion->youngest();
		$this->path = ($this->params['path']) ? $this->params['path'] : '';

		$this->file = $subversion->file($this->params['revision'], $this->params['path']);
	}
}

?>
