<?php
/**
 * @package application.weblcms
 * @subpackage datamanager.database
 */
require_once dirname(__FILE__).'/../../../../common/database/resultset.class.php';
/**
 * This class represents a resultset which represents a set of courses.
 */
class DatabaseHomeColumnResultSet extends ResultSet {
	/**
	 * The datamanager used to retrieve objects from the repository
	 */
	private $data_manager;
	/**
	 * An instance of DB_result
	 */
	private $handle;
	
	private $current;
	/**
	 * Flag to know if the $handle contains all properties of the course
	 * category
	 */
	private $single_type;
	/**
	 * Create a new resultset for handling a set of learning objects
	 * @param RepositoryDataManager $data_manager The datamanager used to
	 * retrieve objects from the repository
	 * @param DB_result $handle The handle to retrieve records from a database
	 * resultset
	 * @param boolean $single_type True if the handle holds all properties of
	 * the learning objects (so when retrieving the learning objects, the
	 * datamanager shouldn't perform additional queries)
	 */
    function DatabaseHomeColumnResultSet($data_manager, $handle, $single_type)
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
		if ($record = $this->handle->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$this->current++;
			return $this->data_manager->record_to_home_column($record);
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
	
	function current ()
	{
		return $this->current;
	}
	
	function position ()
	{
		$current = $this->current();
		$size = $this->size();
		
		if ($current == 1 && $size == 1)
		{
			return 'single';
		}
		elseif ($size > 1 && $current == $size)
		{
			return 'last';
		}
		elseif ($size > 1 && $current == 1)
		{
			return 'first';
		}
		else
		{
			return 'middle';
		}
	}
}
?>