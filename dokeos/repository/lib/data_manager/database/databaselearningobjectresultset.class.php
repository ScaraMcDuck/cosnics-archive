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
		if ($this->single_type)
		{
			if ($record = $this->handle->fetchRow(DB_FETCHMODE_ASSOC))
			{
				return $this->data_manager->record_to_learning_object($record);
			}
		}
		else
		{
			/*
			 * TODO: Extend so additional properties can be fetched when
			 * needed. This would probably involve reviewing LearningObject's
			 * additional property accessor methods.
			 */
			if ($record = $this->handle->fetchRow(DB_FETCHMODE_ASSOC))
			{
				if ($this->data_manager->is_extended_type($record[LearningObject :: PROPERTY_TYPE]))
				{
					return $this->data_manager->retrieve_learning_object($record[LearningObject :: PROPERTY_ID], $record[LearningObject :: PROPERTY_TYPE]);
				}
				else
				{
					return $this->data_manager->record_to_learning_object($record);
				}
			}
		}
    	return null;
    }
}
?>