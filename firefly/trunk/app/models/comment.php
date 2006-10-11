<?

class comment extends model
{
	public function __construct($args = null)
	{
		parent::__construct($args);

		$this->belongs_to('ticket');
	}
}

?>
