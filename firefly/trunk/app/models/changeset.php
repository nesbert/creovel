<?

class changeset extends model
{
	public function diff($revision = null)
	{
		if ($revision == null) $revision = (int)shell_exec(SVNLOOK_PATH." youngest ".LOCAL_REPOSITORY_PATH);
		//echo (SVNLOOK_PATH." diff -r {$revision} ".LOCAL_REPOSITORY_PATH);
		return shell_exec(SVNLOOK_PATH." diff -r {$revision} ".LOCAL_REPOSITORY_PATH);
	}

	public function load_all()
	{
		$this->paginate(array(
			'order' => 'revision DESC',
			'limit' => 20
		));
	}
}

?>
