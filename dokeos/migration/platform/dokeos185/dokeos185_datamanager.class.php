<?php

require_once(dirname(__FILE__) . '/../../lib/migrationdatamanager.class.php');
require_once 'MDB2.php';

class Dokeos185_DataManager extends MigrationDataManager
{	
	
	private $old_directory;
	private $db;
	
	function validateSettings($parameters)
	{
		$old_directory = 'file://' . $parameters[0];

		if(file_exists($old_directory) && is_dir($old_directory))
		{
			$config_file = $old_directory . '/main/inc/conf/configuration.php';
			if(file_exists($config_file) && is_file($config_file))
			{
				require_once($config_file);

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
	function dbConnect($dbname)
	{
		$config_file = $this ->old_directory . '/main/inc/conf/configuration.php';
		require_once($config_file);
		
		$param = isset($_configuration[$dbname])?$_configuration[$dbname]:$dbname;
		$dsn = 'mysql://'.$_configuration['db_user'].':'.$_configuration['db_password'].'@'.
				$_configuration['db_host'].'/'.$param;
		
		$db->connection = MDB2 :: connect($dsn);
	}
	function getAllUsers()
	{
		dbConnect(main_database);
		$query = 'select * from user';
		$result = $db->query($query);
		$users = array();
		while($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$users[] = self :: record_to_user($record);
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
		foreach (Dokeos185_User :: get_default_property_names() as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}
		return new Dokeos185_User($defaultProp);
	}
}

?>
