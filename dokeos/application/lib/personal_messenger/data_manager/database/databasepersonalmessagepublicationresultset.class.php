<?php
/**
 * @package application.lib.personal_messenger.data_manager.database
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../../../../../repository/lib/resultset.class.php';
/**
 * Resultset to hold a set of personal message publications
 */
class DatabasePersonalMessagePublicationResultSet extends ResultSet {
	/**
	 * The datamanager used to retrieve publication from the database
	 */
	private $data_manager;
	/**
	 * An instance of DB_result
	 */
	private $handle;
	/**
	 * Create a new resultset for handling a set of personal message publications
	 * @param RepositoryDataManager $data_manager The datamanager used to
	 * retrieve objects from the repository
	 * @param DB_result $handle The handle to retrieve records from a database
	 * resultset
	 * @param boolean $single_type True if the handle holds all properties of
	 * the learning objects (so when retrieving the personal message publications, the
	 * datamanager shouldn't perform additional queries)
	 */
    function DatabasePersonalMessagePublicationResultSet($data_manager, $handle)
    {
    	$this->data_manager = $data_manager;
    	$this->handle = $handle;
    }
 	/**
	 * Inherited
	 */
    function next_result()
    {
		if ($record = $this->handle->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			return $this->data_manager->record_to_personal_message_publication($record);
		}
    	return null;
    }
	/**
	 * Inherited
	 */
	function size()
	{
		return $this->handle->numRows();
	}
	/**
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