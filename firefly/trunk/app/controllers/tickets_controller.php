<?

class tickets_controller extends application_controller
{
	public function index()
	{
		$this->user = new user($_SESSION['user_id']);

		$this->milestones = &new milestone();
		$this->milestones->find_all();

		$this->severities = &new severity();
		$this->severities->find_all();

		$this->statuses = &new status();
		$this->statuses->find_all();

		$this->parts = &new part();
		$this->parts->find_all();

		$this->users = &new user();
		$this->users->find_all(array( 'where' => "access_level = 'developer'" ));

		$this->milestone = $this->params['milestone'];
		$this->severity = $this->params['severity'];
		$this->status = $this->params['status'];
		$this->part = $this->params['part'];
		$this->user = $this->params['user'];

		if ($this->milestone != '') $where .= "milestone_id = {$this->milestone} AND ";
		if ($this->severity != '') $where .= "severity_id = {$this->severity} AND ";
		if ($this->part != '') $where .= "part_id = {$this->part} AND ";
		if ($this->user != '') $where .= "user_id = {$this->user} AND ";

		if ($this->status != '') {
			if ($this->status == 'closed') {
				$where .= "status_id > 1 AND ";
			} else {
				$where .= "status_id = {$this->status} AND ";
			}
		} else {
			$where .= 'status_id = 1 AND ';
		}

		$in_queue = ($where == '') ? 'in_queue = 0' : 'in_queue = 0 AND ';

		$this->tickets = &new ticket();
		$this->tickets->find_all(array( 'where' => $in_queue.' '.substr($where, 0, -5) ));

		$this->in_queue_tickets = &new ticket();
		$this->in_queue_tickets->find_all(array( 'where' => 'in_queue = 1' ));
	}

	public function view()
	{
		$this->user = new user($_SESSION['user_id']);

		$this->textile = new Textile();
		$this->ticket = &new ticket($this->params['id']);

		$this->comment = new comment();
		
		$this->comments = new comment();
		$this->comments->find_by_ticket_id($this->params['id']);

		$milestones = &new milestone();
		$milestones->find_all();
		foreach ($milestones as $milestone) $this->milestones[$milestone->id] = $milestone->name;

		$severities = &new severity();
		$severities->find_all();
		foreach ($severities as $severity) $this->severities[$severity->id] = $severity->name;

		$parts = &new part();
		$parts->find_all();
		foreach ($parts as $part) $this->parts[$parts->id] = $parts->name;

		$statuses = &new status();
		$statuses->find_all();
		foreach ($statuses as $status) $this->statuses[$status->id] = $status->name;

		$users = &new user();
		$users->find_all();
		foreach ($users as $user) $this->users[$user->id] = $user->name;

		if ($this->is_posted()) {
			$this->ticket = new ticket($this->params['ticket']);
			$this->ticket->save();

			flash_notice('Ticket Saved');
			redirect_to('tickets');
		}
	}

	public function create()
	{
		$this->user = new user($_SESSION['user_id']);

		$this->ticket = &new ticket();

		$milestones = &new milestone();
		$milestones->find_all();
		foreach ($milestones as $milestone) $this->milestones[$milestone->id] = $milestone->name;

		$severities = &new severity();
		$severities->find_all();
		foreach ($severities as $severity) $this->severities[$severity->id] = $severity->name;

		$parts = &new part();
		$parts->find_all();
		foreach ($parts as $part) $this->parts[$parts->id] = $parts->name;

		$users = &new user();
		$users->find_all();
		foreach ($users as $user) $this->users[$user->id] = $user->name;

		if ($this->is_posted()) {
			$this->ticket = new ticket($this->params['ticket']);
			$this->ticket->status_id = 1;
			$this->ticket->save();

			flash_notice('Ticket Saved');
			redirect_to('tickets');
		}
	}

	public function delete()
	{
		$this->ticket = new ticket($this->params['id']);
		$this->ticket->delete();

		flash_notice('Ticket Deleted');
		redirect_to('tickets');
	}

	public function add_comment()
	{
		$this->comment = new comment($this->params['comment']);
		$this->comment->save();

		redirect_to('tickets', 'view', $this->comment->ticket_id);
	}
}

?>
