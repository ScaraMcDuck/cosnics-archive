<?php
/**
 * @package webconferencing.datamanager
 */
require_once dirname(__FILE__).'/../webconference.class.php';
require_once dirname(__FILE__).'/../webconference_option.class.php';
require_once Path :: get_library_path() . 'database/database.class.php';
require_once 'MDB2.php';

/**
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *  @author Stefaan Vanbillemont
 */

class DatabaseWebconferencingDataManager extends WebconferencingDataManager
{
	private $database;

	function initialize()
	{
		$aliasses = array();
		$aliasses[Webconference :: get_table_name()] = 'wece';
		$aliasses[WebconferenceOption :: get_table_name()] = 'weon';

		$this->database = new Database($aliasses);
		$this->database->set_prefix('webconferencing_');
	}

	function create_storage_unit($name, $properties, $indexes)
	{
		return $this->database->create_storage_unit($name, $properties, $indexes);
	}

	function get_next_webconference_id()
	{
		return $this->database->get_next_id(Webconference :: get_table_name());
	}

	function create_webconference($webconference)
	{
		return $this->database->create($webconference);
	}

	function update_webconference($webconference)
	{
		$condition = new EqualityCondition(Webconference :: PROPERTY_ID, $webconference->get_id());
		return $this->database->update($webconference, $condition);
	}

	function delete_webconference($webconference)
	{
		$this->delete_webconference_options($webconference);
		$condition = new EqualityCondition(Webconference :: PROPERTY_ID, $webconference->get_id());
		return $this->database->delete($webconference->get_table_name(), $condition);
	}

	function count_webconferences($condition = null)
	{
		return $this->database->count_objects(Webconference :: get_table_name(), $condition);
	}

	function retrieve_webconference($id)
	{
		$condition = new EqualityCondition(Webconference :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(Webconference :: get_table_name(), $condition);
	}

	function retrieve_webconferences($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		return $this->database->retrieve_objects(Webconference :: get_table_name(), $condition, $offset, $maxObjects, $orderBy, $orderDir);
	}

	function get_next_webconference_option_id()
	{
		return $this->database->get_next_id(WebconferenceOption :: get_table_name());
	}

	function create_webconference_option($webconference_option)
	{
		return $this->database->create($webconference_option);
	}

	function update_webconference_option($webconference_option)
	{
		$condition = new EqualityCondition(WebconferenceOption :: PROPERTY_ID, $webconference_option->get_id());
		return $this->database->update($webconference_option, $condition);
	}

	function delete_webconference_option($webconference_option)
	{
		$condition = new EqualityCondition(WebconferenceOption :: PROPERTY_ID, $webconference_option->get_id());
		return $this->database->delete($webconference_option->get_table_name(), $condition);
	}
	
	function delete_webconference_options($webconference)
	{
		$condition = new EqualityCondition(WebconferenceOption :: PROPERTY_CONF_ID, $webconference->get_id());
		return $this->database->delete(WebconferenceOption :: get_table_name(), $condition);
	}

	function count_webconference_options($condition = null)
	{
		return $this->database->count_objects(WebconferenceOption :: get_table_name(), $condition);
	}

	function retrieve_webconference_option($id)
	{
		$condition = new EqualityCondition(WebconferenceOption :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(WebconferenceOption :: get_table_name(), $condition);
	}

	function retrieve_webconference_options($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		return $this->database->retrieve_objects(WebconferenceOption :: get_table_name(), $condition, $offset, $maxObjects, $orderBy, $orderDir);
	}

}
?>