<?php
/**
 * undocumented class
 *
 * @package default
 * @author Nesbert Hidalgo
 **/
class ActiveRecord
{
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Nesbert Hidalgo
	 **/
	public function connectionProperties()
	{
		switch (CREO('mode')) {
			case 'PRODUCTION':
				return $GLOBALS['CREOVEL']['DATABASES']['PRODUCTION'];
				break;
			
			case 'TEST':
				return $GLOBALS['CREOVEL']['DATABASES']['TEST'];
				break;
			
			case 'DEVELOPMENT':
			default:
				return $GLOBALS['CREOVEL']['DATABASES']['DEVELOPMENT'];
				break;
		}
	}
	
	/**
	 * Choose the correct DB adapter to use and sets its properties and
	 * return an db object.
	 *
	 * @param array $db_properties
	 * @return object
	 **/
	public function establishConnection($db_properties = null)
	{
		if (!$db_properties || !is_array($db_properties)) {
			$db_properties = self::connectionProperties();
		}
		
		$adapter = isset($db_properties['adapter']) ? strtolower($db_properties['adapter']) : 'None';
		$adapter_path = dirname(dirname(__FILE__)) . DS . 'adapters' . DS;
		
		switch ($adapter) {
			case 'mysql':
				$adapter = Inflector::classify('mysql');
				break;
			
			case 'mysql_improved':
				require_once $adapter_path . 'mysql_improved.php';
				$adapter = Inflector::classify('mysql_improved');
				break;
			
			case 'sqlite':
				$adapter = Inflector::classify('sqlite');
				break;
			
			default:
				self::throwError("Unknown database adapter '{$adapter}'. Please check database configuration file.");
				break;
		}
		
		return new $adapter($db_properties);
	}
	
	/**
	 * Stop the application and display/handle error.
	 *
	 * @return void
	 **/
	public function throwError($msg = null)
	{
		if (!$msg) {
			$msg = 'An error occurred while executing the method ' .
			"<em>{$this->_action}</em> in the <strong> " . get_class($this) .
			'</strong>.';
		}
		CREO('application_error', $msg);
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Nesbert Hidalgo
	 **/
	public static function query($sql)
	{
		if (isset($this)) {
			$dbo = &$this->_action_query;
		} else {
			$dbo = self::establishConnection();
		}
		
		$dbo->query($sql);
		
		if ($dbo->totalRows() == 1) {
			print_obj($dbo->getRow());
		}
		
		print_obj($dbo);
		
		return ;
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public static function find_by_sql($sql)
	{
		return self::query($sql);
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Nesbert Hidalgo
	 **/
	public static function quote($string)
	{
		return "'" . 2 . "'";
	}
} // END abstract class ActiveRecord implements Interator