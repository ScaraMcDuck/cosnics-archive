<?php
/**
 * @package users
 * @subpackage datamanager
 */
require_once dirname(__FILE__).'/../link.class.php';
require_once Path :: get_library_path() . 'database/database.class.php';
require_once 'MDB2.php';

/**
==============================================================================
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *  @author Sven Vanpoucke
==============================================================================
 */

class DatabaseLinkerDataManager extends LinkerDataManager
{
	private $database;

	/**
	 * Initialize
	 * Create a new database
	 * Define aliases
	 * Define prefixes
	 *
	 */
	function initialize()
	{
		$this->database = new Database();
		$this->database->set_prefix('link_');
	}

    function get_database()
    {
        return $this->database;
    }

	function update_link($link)
	{
		$condition = new EqualityCondition(Link :: PROPERTY_ID, $link->get_id());
		return $this->database->update($link, $condition);
	}

	function get_next_link_id()
	{
		return $this->database->get_next_id(Link :: get_table_name());
	}

	function delete_link($link)
	{
		$condition = new EqualityCondition(Link :: PROPERTY_ID, $link->get_id());
		return $this->database->delete($link->get_table_name(), $condition);
	}

	function create_link($link)
	{
		return $this->database->create($link);
	}

	function count_links($condition = null)
	{
		return $this->database->count_objects(Link :: get_table_name(), $condition);
	}

	function retrieve_links($condition = null, $offset = null, $max_objects = null, $order_by = null, $order_dir = null)
	{
		return $this->database->retrieve_objects(Link :: get_table_name(), $condition, $offset, $max_objects, $order_by, $order_dir);
	}

	function retrieve_link($id)
	{
		$condition = new EqualityCondition(Link :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(Link :: get_table_name(), $condition);
	}

	function create_storage_unit($name, $properties, $indexes)
	{
		return $this->database->create_storage_unit($name, $properties, $indexes);
	}
}
?>