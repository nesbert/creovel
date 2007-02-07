<?

/*

Class: session
	Table session class.

Todo:
	Storing class/object bug
 
*/

class session extends model
{

	/*
	
	Function: __construct
		Constructor for session model

	Paramters:	
		mixed - connection arguments array

	*/

	public function __construct($args = null)
	{	
		
		if ($_ENV['sessions_table_name']) {

			$this->_table_name = $_ENV['sessions_table_name'];
		}
		parent::__construct($args);
	}

	/*
	
	Function: clean_data
		Cleans data

	Parameters:	
		data - optional

	Returns:
		mixed

	*/

	private function clean_data($data)
	{
		return addslashes($data);	
	}
	
	/*
	
	Function: table_check	
		Create sessions table if it doesn't exists

	*/

	private function table_check()
	{
		$this->query("CREATE TABLE IF NOT EXISTS {$this->_table_name}
						(
						id VARCHAR (255) NOT NULL,
						expires INT (10) NOT NULL default '0',
						data TEXT NOT NULL,
						PRIMARY KEY (id)
						)");
	}
	
	/*
	
	Function: get_session_data	
		Get session data
	
	Parameters:
		id - optional

	Returns:
		mixed

	*/

	public function get_session_data($id = false)
	{
		if ( !$id ) return false;
		
		$this->table_check();		
		$this->query("SELECT * FROM {$this->_table_name} WHERE id = '".$this->clean_data($id)."' AND expires > '".time()."'");
		$this->next();

		if ( $this->row_count() == 1 ) {			
			return $this->get_data();
		} else {
			return "";
		}
	}
	
	/*
	
	Function: set_session_data	
		Set session data

	Parameters:	
		id - session id
		val - optional

	Returns:
		int

	*/

	public function set_session_data($id = false, $val = '')
	{
		$expires = time() + get_cfg_var("session.gc_maxlifetime");
		if ( !$id ) return false;
		$this->query("REPLACE INTO {$this->_table_name} (id, expires, data) VALUES ('".$this->clean_data($id)."', '".$expires."', '".$this->clean_data($val)."')");
	    return $this->get_affected_rows();
	}

	/*
	
	Function: destroy_session_data
		Delete session data

	Parameters:
		id - session id
	
	Returns:	
		bool

	*/

	public function destroy_session_data($id)
	{
		$this->query_records("DELETE FROM {$this->_table_name} WHERE id = '".$this->clean_data($id)."'");
	    return $this->get_affected_rows();
	}
	
	/*

	Function: clean_sesssion_data
		Delete all expired rows from session table

	Parameters:
		maxlifetime - session max life time			

	Returns:
		bool

	*/

	public function clean_session_data($maxlifetime)
	{
		$this->query("DELETE FROM {$this->_table_name} WHERE expires < '".time()."'");
	    return $this->get_affected_rows();
	}
	
	/*
	
	Function: load_sesesion_by_id
		Load seesion by id

	Parameters:	
		id - session id

	Returns:
		session object

	*/

	public function load_session_by_id($id)
	{
		if (!$id) return false;
		$this->query("SELECT * FROM {$this->_table_name} WHERE id = '".$this->clean_data($id)."'");
		return $this->next();
	}
}

?>
