<?php
require_once dirname(__FILE__).'/../../resultset.class.php';

class DatabaseLearningObjectResultSet extends ResultSet {
	private $data_manager;
	
	private $handle;
	
	private $single_type;
	
    function DatabaseLearningObjectResultSet($data_manager, $handle, $single_type)
    {
    	$this->data_manager = $data_manager;
    	$this->handle = $handle;
    	$this->single_type = $single_type;
    }
    
    function next_result()
    {
		if ($record = $this->handle->fetchRow(DB_FETCHMODE_ASSOC))
		{
			return $this->data_manager->record_to_learning_object($record, $this->single_type);
		}
    	return null;
    }
	
	function size()
	{
		return $this->handle->numRows();
	}
	
	function skip ($count)
	{
		for ($i = 0; $i < $count; $i++)
		{
			$this->handle->fetchRow();
		}
	}
}
?>