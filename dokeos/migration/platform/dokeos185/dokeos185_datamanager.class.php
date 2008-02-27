<?php

require_once(dirname(__FILE__) . '/../../lib/migrationdatamanager.class.php');

class Dokeos185_DataManager extends MigrationDataManager
{	
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
}

?>
