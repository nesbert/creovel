<?php
interface adapter_interface
{

	public function connect($db_properties);
	public function disconnect();
	public function set_table($table);
	public function get_fields_object();
	public function query();
	public function reset();
	public function get_row();
	public function get_affected_rows();
	
}
?>