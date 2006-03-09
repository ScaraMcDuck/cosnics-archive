<?php
require_once dirname(__FILE__).'/../weblcmsdatamanager.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/repositorydatamanager.class.php';

class DatabaseWebLCMSDataManager extends WebLCMSDataManager
{
	private $connection;
	
	private $prefix;

	function initialize()
	{
		$m = RepositoryDataManager :: get_instance();
		$this->connection = $m->get_connection();
		$this->prefix = $m->get_table_name_prefix();
	}
}
?>