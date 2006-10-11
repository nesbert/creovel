<?

class milestone extends model
{
	public function __construct($args = null)
	{
		parent::__construct($args);

		$this->has_many('tickets');
		$this->has_many('open_tickets', array( 'class_name' => 'ticket', 'where' => "status_id = 1 AND in_queue = 0" ));
		$this->has_many('closed_tickets', array( 'class_name' => 'ticket', 'where' => "status_id > 1 AND in_queue = 0" ));
	}
}

?>
