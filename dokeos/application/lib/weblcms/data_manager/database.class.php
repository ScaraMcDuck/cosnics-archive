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
	
	function retrieve_learning_object_publication($pid)
	{
		// TODO
	}

	function retrieve_learning_object_publications($learningObjects, $courses, $conditions = null, $orderBy = array (), $orderDesc = array (), $firstIndex = 0, $maxObjects = -1)
	{
		// TODO
	}

	function count_learning_object_publications($learningObjects, $courses, $conditions = null)
	{
		// TODO
	}

	function create_learning_object_publication($publication)
	{
		// TODO
	}
	
	function update_learning_object_publication($publication)
	{
		// TODO
	}
	
	function delete_learning_object_publication($publication)
	{
		// TODO
	}
}
?>