<?php
/**
 * @package repository
 */
require_once dirname(__FILE__).'/../../resultset.class.php';
/**
 * Resultset to hold a set of learning objects
 */
class DatabaseLearningObjectResultSet extends ResultSet {
	private $data_manager;

	private $handle;

	private $single_type;
	/**
	 * Create a new resultset for handling a set of learning objects
	 * @param RepositoryDataManager $data_manager The datamanager used to
	 * retreve objects from the repository
	 * @param $handle
	 * @param boolean $single_type True if the handle holds all properties of
	 * the learning objects (so when retrieving the learning objects, the
	 * datamanager shouldn't perform additional queries)
	 */
    function DatabaseLearningObjectResultSet($data_manager, $handle, $single_type)
    {
    	$this->data_manager = $data_manager;
    	$this->handle = $handle;
    	$this->single_type = $single_type;
    }
 	/*
	 * Inherited
	 */
    function next_result()
    {
		if ($record = $this->handle->fetchRow(DB_FETCHMODE_ASSOC))
		{
			return $this->data_manager->record_to_learning_object($record, $this->single_type);
		}
    	return null;
    }
	/*
	 * Inherited
	 */
	function size()
	{
		return $this->handle->numRows();
	}
	/*
	 * Inherited
	 */
	function skip ($count)
	{
		for ($i = 0; $i < $count; $i++)
		{
			$this->handle->fetchRow();
		}
	}
}
?>