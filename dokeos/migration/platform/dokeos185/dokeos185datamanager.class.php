<?php

require_once(dirname(__FILE__) . '/../../lib/migrationdatamanager.class.php');
require_once(dirname(__FILE__) . '/../../../admin/lib/admindatamanager.class.php');
require_once 'MDB2.php';

/**
 * Class that connects to the old dokeos185 system
 * 
 * @author Sven Vanpoucke
 * @author David Van Wayenbergh
 */
class Dokeos185DataManager extends MigrationDataManager
{	
	
	private static $old_directory;
	private static $_configuration;
	private $db;
	
	function validate_settings($parameters)
	{
		self :: $old_directory = 'file://' . $parameters[0];

		if(file_exists(self :: $old_directory) && is_dir(self :: $old_directory))
		{
			$config_file = self :: $old_directory . '/main/inc/conf/configuration.php';
			if(file_exists($config_file) && is_file($config_file))
			{
				require_once($config_file);
				
				self :: $_configuration = $_configuration;

				if(mysql_connect($_configuration['db_host']	, $_configuration['db_user'], 
								 $_configuration['db_password']	))
				{
					
					if(mysql_select_db($_configuration['main_database']) &&
					   mysql_select_db($_configuration['statistics_database']) &&
					    mysql_select_db($_configuration['user_personal_database']))
							return true;
				}	
			}
		}
		
		return false;
	}
	
	function db_connect($dbname)
	{
		$param = isset(self :: $_configuration[$dbname])?self :: $_configuration[$dbname]:$dbname;
		$dsn = 'mysql://'.self :: $_configuration['db_user'].':'.self :: $_configuration['db_password'].'@'.
				self :: $_configuration['db_host'].'/'.$param;
		$this->db = MDB2 :: connect($dsn);
	}
	
	function get_all_users()
	{
		$this->db_connect('main_database');
		$query = 'select * from user';
		$result = $this->db->query($query);
		$users = array();
		while($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$users[] = $this->record_to_user($record);
		}
		$result->free();
		
		return $users;
	}
	
	function record_to_user($record)
	{
		if (!is_array($record) || !count($record))
		{
			throw new Exception(get_lang('InvalidDataRetrievedFromDatabase'));
		}
		$defaultProp = array ();
		foreach (Dokeos185User :: get_default_property_names() as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}
		return new Dokeos185User($defaultProp);
	}
}

?>
