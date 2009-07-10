<?php
/**
 * $Id$
 * @package application.weblcms
 * @subpackage datamanager.database
 */
require_once Path :: get_library_path(). 'database/result_set.class.php';
/**
 * This class represents a resultset which represents a set of learning object
 * publications.
 */
class DatabaseLearningObjectPublicationResultSet extends ResultSet
{
	/**
	 * The data manager.
	 */
	private $data_manager;
	/**
	 * An instance of DB_result
	 */
	private $handle;
	/**
	 * Constructor.
	 * @param DatabaseWeblcmsDataManager $data_manager The datamanager used to
	 * retrieve the learning object publications.
	 * @param DB_result $handle The handle to retrieve records from a database
	 * resultset
	 */
	function DatabaseLearningObjectPublicationResultSet ($data_manager, $handle)
	{
		$this->data_manager = $data_manager;
		$this->handle = $handle;
	}
 	/*
	 * Inherited
	 */
	function next_result()
	{
		if ($record = $this->handle->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			return $this->data_manager->get_database()->record_to_object($record, LearningObjectPublication :: CLASS_NAME);
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
	/**
	 * Returns the id of the next learning object
	 * @return int|null
	 */
	function next_learning_object_id()
	{
		if ($record = $this->handle->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			return $record[LearningObjectPublication::PROPERTY_LEARNING_OBJECT_ID];
		}
		return null;
	}
}
?>