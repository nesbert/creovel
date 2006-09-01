<?php
/**
 * Copyright (c) 2005-2006, creovel.org
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated 
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software,
 * and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions
 * of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED
 * TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * Licensed under The MIT License. Redistributions of files must retain the above copyright notice.
 */

/**
 * Table session class.
 *
 * @copyright	Copyright (c) 2005-2006, creovel.org
 * @package		creovel
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @todo		storing class/object bug
 */
class session extends model
{

	/**
	 * Cleans data
	 *
	 * @author Nesbert Hidalgo
	 * @access private
	 * @param mixed $data optional returns controller as string
	 * @return mixed
	 */
	private function clean_data($data)
	{
		return addslashes($data);	
	}
	
	/**
	 * Create sessions table if it doesn't exists
	 *
	 * @author Nesbert Hidalgo
	 * @access private
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
	
	/**
	 * Get session data
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $id
	 * @return mixed
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
	
	/**
	 * Set session data
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $id
	 * @param mixed $val
	 * @return int
	 */
	public function set_session_data($id = false, $val = '')
	{
		$expires = time() + get_cfg_var("session.gc_maxlifetime");
		if ( !$id ) return false;
		$this->query("REPLACE INTO {$this->_table_name} (id, expires, data) VALUES ('".$this->clean_data($id)."', '".$expires."', '".$this->clean_data($val)."')");
	    return $this->get_affected_rows();
	}

	/**
	 * Delete session data
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @return bool
	 */
	public function destroy_session_data($id)
	{
		$this->query_records("DELETE FROM {$this->_table_name} WHERE id = '".$this->clean_data($id)."'");
	    return $this->get_affected_rows();
	}
	
	/**
	 * Delete all expired rows from session table
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @return bool
	 */
	public function clean_session_data($maxlifetime)
	{
		$this->query("DELETE FROM {$this->_table_name} WHERE expires < '".time()."'");
	    return $this->get_affected_rows();
	}
	
	/**
	 * Load seesion by id
	 *
	 * @author Nesbert Hidalgo
	 * @access public
	 * @param string $id
	 * @return object
	 */
	public function load_session_by_id($id)
	{
		if (!$id) return false;
		$this->query("SELECT * FROM {$this->_table_name} WHERE id = '".$this->clean_data($id)."'");
		return $this->next();
	}

}
?>