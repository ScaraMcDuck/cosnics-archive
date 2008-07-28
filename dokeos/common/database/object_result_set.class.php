<?php
/**
 * @package application.weblcms
 * @subpackage datamanager.database
 */
require_once dirname(__FILE__) . '/result_set.class.php';
/**
 * This class represents a resultset which represents a set of courses.
 */
class ObjectResultSet extends ResultSet {
	/**
	 * The datamanager used to retrieve objects from the repository
	 */
	private $data_manager;
	
	/**
	 * An instance of DB_result
	 */
	private $handle;
	
	/**
	 * The classname to map the object to
	 */
	private $classname;
	
	/**
	 * Create a new resultset for handling a set of learning objects
	 * @param DataManager $data_manager The datamanager used to
	 * retrieve objects from the repository
	 * @param DB_result $handle The handle to retrieve records from a database
	 * resultset
	 */
    function ObjectResultSet($data_manager, $handle, $classname)
    {
    	$this->data_manager = $data_manager;
    	$this->handle = $handle;
    	$this->classname = $classname;
    }
    
 	/*
	 * Inherited
	 */
    function next_result()
    {
		if ($record = $this->handle->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			return $this->data_manager->record_to_classobject($record, $this->classname);
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