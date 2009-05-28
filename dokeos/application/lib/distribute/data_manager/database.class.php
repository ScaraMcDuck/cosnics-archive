<?php
/**
 * @package distribute.datamanager
 */
require_once dirname(__FILE__).'/../announcement_distribution.class.php';
require_once Path :: get_library_path() . 'database/database.class.php';
require_once 'MDB2.php';

/**
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *  @author Hans De Bisschop
 */

class DatabaseDistributeDataManager extends DistributeDataManager
{
	private $database;

	function initialize()
	{
		$aliasses = array();
		$aliasses[AnnouncementDistribution :: get_table_name()] = 'dion';

		$this->database = new Database($aliasses);
		$this->database->set_prefix('distribute_');
	}

	function create_storage_unit($name, $properties, $indexes)
	{
		return $this->database->create_storage_unit($name, $properties, $indexes);
	}

	function get_next_announcement_distribution_id()
	{
		return $this->database->get_next_id(AnnouncementDistribution :: get_table_name());
	}

	function create_announcement_distribution($distribute_publication)
	{
		return $this->database->create($distribute_publication);
	}

	function update_announcement_distribution($distribute_publication)
	{
		$condition = new EqualityCondition(AnnouncementDistribution :: PROPERTY_ID, $distribute_publication->get_id());
		return $this->database->update($distribute_publication, $condition);
	}

	function delete_announcement_distribution($distribute_publication)
	{
		$condition = new EqualityCondition(AnnouncementDistribution :: PROPERTY_ID, $distribute_publication->get_id());
		return $this->database->delete($distribute_publication->get_table_name(), $condition);
	}

	function count_announcement_distributions($condition = null)
	{
		return $this->database->count_objects(AnnouncementDistribution :: get_table_name(), $condition);
	}

	function retrieve_announcement_distribution($id)
	{
		$condition = new EqualityCondition(AnnouncementDistribution :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(AnnouncementDistribution :: get_table_name(), $condition);
	}

	function retrieve_announcement_distributions($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		return $this->database->retrieve_objects(AnnouncementDistribution :: get_table_name(), $condition, $offset, $maxObjects, $orderBy, $orderDir);
	}

}
?>