<?php

require_once dirname(__FILE__).'/../help_data_manager.class.php';
require_once dirname(__FILE__).'/../help_item.class.php';
require_once Path :: get_library_path() . 'database/database.class.php';
require_once 'MDB2.php';

/**
==============================================================================
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *	@author Tim De Pauw
 *	@author Bart Mollet
 *  @author Sven Vanpoucke
==============================================================================
 */

class DatabaseHelpDataManager extends HelpDataManager
{
	private $database;
	
	function initialize()
	{
		$this->database = new Database(array('help_item' => 'hi'));
		$this->database->set_prefix('help_');
	}
	
	function update_help_item($help_item)
	{
		$condition = new EqualityCondition(HelpItem :: PROPERTY_NAME, $help_item->get_name());
		return $this->database->update($help_item, $condition);
	}
	
	function create_help_item($help_item)
	{
		return $this->database->create($help_item);
	}
	
	function count_help_items($condition = null)
	{
		return $this->database->count_objects(HelpItem :: get_table_name(), $condition);
	}

	function retrieve_help_items($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		return $this->database->retrieve_objects(HelpItem :: get_table_name(), $condition, $offset, $maxObjects, $orderBy, $orderDir);
	}
	
	function retrieve_help_item($name)
	{
		$condition = new EqualityCondition(HelpItem :: PROPERTY_NAME, $name);
		return $this->database->retrieve_object(HelpItem :: get_table_name(), $condition);
	}
	
	function create_storage_unit($name, $properties, $indexes)
	{
		return $this->database->create_storage_unit($name, $properties, $indexes);
	}
}
?>