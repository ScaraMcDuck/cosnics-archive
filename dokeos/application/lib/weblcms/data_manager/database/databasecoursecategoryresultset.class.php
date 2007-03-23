<?php
/**
 * @package application.weblcms
 * @subpackage datamanager.database
 */
require_once dirname(__FILE__).'/../../../../../repository/lib/resultset.class.php';
/**
 * This class represents a resultset which represents a set of course categories.
 */
class DatabaseCourseCategoryResultSet extends ResultSet {
	/**
	 * The datamanager used to retrieve objects from the repository
	 */
	private $data_manager;
	/**
	 * An instance of DB_result
	 */
	private $handle;
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
    function DatabaseCourseCategoryResultSet($data_manager, $handle, $single_type)
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
			return $this->data_manager->record_to_course_category($record);
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