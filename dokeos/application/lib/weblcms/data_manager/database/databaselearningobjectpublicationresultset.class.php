<?php
require_once dirname(__FILE__).'/../../../../../repository/lib/resultset.class.php';

class DatabaseLearningObjectPublicationResultSet extends ResultSet
{
	private $data_manager;
	
	private $handle;
	
	function DatabaseLearningObjectPublicationResultSet ($data_manager, $handle)
	{
		$this->data_manager = $data_manager;
		$this->handle = $handle;
	}
	
	function next_result()
	{
		if ($record = $this->handle->fetchRow(DB_FETCHMODE_ASSOC))
		{
			return $this->data_manager->record_to_publication($record);
		}
		return null;
	}
}
?>