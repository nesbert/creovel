<?

class ticket extends model
{
	public function __construct($args = null)
	{
		parent::__construct($args);

		$this->belongs_to('part');
		$this->belongs_to('severity');
		$this->belongs_to('milestone');
		$this->belongs_to('status', array( 'class_name' => 'status' ));
		$this->has_many('comments');
	}
}

?>
