<?

class index_controller extends application_controller
{
	public function index()
	{
		redirect_to('changesets');
	}

	public function import()
	{
		//shell_exec("php ../svn/import");
	}
}

?>
