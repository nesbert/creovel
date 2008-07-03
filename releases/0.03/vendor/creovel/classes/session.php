<?php
/*

	Class: session
	
	Table session class.

	Todo:
	
		* Storing class/object bug.
 
*/

class session extends model
{

	/*
	
		Function: __construct
		
		Constructor for session model
		
		Paramters:
		
		mixed - Array of connection arguments.

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
		
		Cleans data.
		
		Parameters:	
		
			data - Mixed.
			
		Returns:
		
			Mixed
	
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
						started_at datetime default NULL,
						updated_at datetime default NULL,
						expires_at datetime default NULL,
						data TEXT NOT NULL,
						PRIMARY KEY (id)
						)");
	}
	
	/*
	
		Function: get_session_data
		
		Get session data.
		
		Parameters:
		
			id - Optional session ID.
		
		Returns:
		
			Mixed.
	
	*/

	public function get_session_data($id = false)
	{
		if ( !$id ) return false;
		
		$this->table_check();
		$this->query("SELECT * FROM {$this->_table_name} WHERE id = '".$this->clean_data($id)."' AND expires_at > '".datetime()."'");
		$this->next();

		if ( $this->row_count() == 1 ) {
			return $this->get_data();
		} else {
			return "";
		}
	}
	
	/*
	
		Function: set_session_data
		
		Sets session data.
		
		Parameters:	
		
			id - Session ID
			val - Optional session value.
			
		Returns:
		
			Integer
	
	*/

	public function set_session_data($id = false, $val = '')
	{
		if ( !$id ) return false;
		$expires = time() + get_cfg_var("session.gc_maxlifetime");

		$this->query("SELECT * FROM {$this->_table_name} WHERE id = '".$this->clean_data($id)."'");
		$this->next();

		if ( $this->row_count() ) {
			$this->query("UPDATE {$this->_table_name} SET expires_at = '".datetime($expires)."', updated_at = '".datetime()."', data = '".$this->clean_data($val)."' WHERE id = '".$this->clean_data($id)."'");
		} else {		
			$this->query("INSERT INTO {$this->_table_name} (id, started_at, updated_at, expires_at, data) VALUES ('".$this->clean_data($id)."', '".datetime()."', '', '".datetime($expires)."', '".$this->clean_data($val)."')");
		}
		
	    return $this->get_affected_rows();
	}

	/*
	
		Function: destroy_session_data
		
		Deletes session data.
		
		Parameters:
		
			id - Session ID.
			
		Returns:
		
			Boolean
	
	*/

	public function destroy_session_data($id)
	{
		$this->query("DELETE FROM {$this->_table_name} WHERE id = '".$this->clean_data($id)."'");
	    return $this->get_affected_rows();
	}
	
	/*
	
		Function: clean_sesssion_data
		
		Delete all expired rows from session table.
		
		Parameters:
		
			maxlifetime - Session max life time.
			
		Returns:
		
			Boolean.

	*/

	public function clean_session_data($maxlifetime)
	{
		$this->query("DELETE FROM {$this->_table_name} WHERE expires_at < '".datetime()."'");
	    return $this->get_affected_rows();
	}
	
	/*
	
		Function: load_sesesion_by_id
		
		Load seesion by id.

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